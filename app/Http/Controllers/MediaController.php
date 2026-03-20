<?php
namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Models\MediaGroup;
use App\Models\MediaTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use App\Models\MediaLike;
use App\Models\MediaComment;
use App\Models\MediaView;
class MediaController extends Controller {
	public function index() {
		$groups = MediaGroup::all();
		return view('admin.media.index', compact('groups'));
	}

	public function getMediaFiles(Request $request) {
		try {
			$query = MediaFile::with(['groups', 'tags', 'industries','dealers','manufacturers']);
			if ($request->type && $request->type !== 'all') {
				$query->where('file_type', $request->type);
			}
			if ($request->filled('group')) {
				$query->whereHas('groups', function ($q) use ($request) {
					$q->where('name', $request->group);
				});
			}
			if ($request->filled('tag') && !empty($request->tag)) {
				$tagName = $request->tag;
				$query->whereHas('tags', function($q) use ($tagName) {
					$q->where('name', 'like', "%$tagName%");
				});
			}
			$files = $query->latest()->get();
			$files = $files->map(function ($file) {
				return [
					'id' => $file->id,
					'title' => $file->title,
					'description' => $file->description,
					'file_type' => $file->file_type,
					'mime_type' => $file->mime_type,
					'thumbnail_url' => $file->thumbnail_path ? asset('storage/' . $file->thumbnail_path) : null,
					'medium_url' => $file->medium_path ? asset('storage/' . $file->medium_path) : null,
					'url' => $file->file_path ? asset('storage/' . $file->file_path) : null,
					'is_featured' => $file->is_featured,
					'width' => $file->width,
					'height' => $file->height,
					'size' => $file->size,
					'thumbnail_size' => $file->thumbnail_size,
					'group' => $file->groups->first() ? [
						'id' => $file->groups->first()->id,
						'name' => $file->groups->first()->name
					] : null,
					'industries' => $file->industries->map(function ($industry) {
					return [
						'id' => $industry->id,
						'name' => $industry->name
					];
				})->toArray(),
				'dealers' => $file->dealers->map(function ($dealer) {
					return [
						'id' => $dealer->id,
						'name' => $dealer->name
					];
				})->toArray(),
				'manufacturers' => $file->manufacturers->map(function ($manufacturer) {
					return [
						'id' => $manufacturer->id,
						'name' => $manufacturer->name
					];
				})->toArray(),
					'tags' => $file->tags->pluck('name')->toArray()
				];
			});
			return response()->json([
				'data' => $files,
				'status' => 'success'
			]);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Error fetching media files',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function store(Request $request) {
		try {
			if ($request->hasFile('files')) {
				$validated = $request->validate([
					'files' => 'required',
					// 'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,pdf,doc,docx|max:500000',
					'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,csv,xlsx,xls,pdf,docx|max:500000',
					'title' => 'required|string|max:255',
					'description' => 'nullable|string',
					'group_name' => 'required|string|max:255',
					'industry_ids' => 'nullable|array',
					'industry_ids.*' => 'nullable|integer|exists:industries,id',
					'dealer_ids' => 'nullable|array',
					'dealer_ids.*' => 'nullable|integer|exists:dealers,id',
					'manufacturer_ids' => 'nullable|array',
					'manufacturer_ids.*' => 'nullable|integer|exists:manufacturers,id',
					'tags' => 'nullable|string',
					'is_featured' => 'nullable'
				]);
			} else {
				$validated = $request->validate([
					'title' => 'required|string|max:255',
					'description' => 'nullable|string',
					'group_name' => 'required|string|max:255',
					'industry_ids' => 'nullable|array',
					'industry_ids.*' => 'nullable|integer|exists:industries,id',
					'dealer_ids' => 'nullable|array',
					'dealer_ids.*' => 'nullable|integer|exists:dealers,id',
					'manufacturer_ids' => 'nullable|array',
					'manufacturer_ids.*' => 'nullable|integer|exists:manufacturers,id',
					'tags' => 'nullable|string',
					'is_featured' => 'nullable'
				]);
			}
			DB::beginTransaction();
			$group = MediaGroup::firstOrCreate(
				['name' => $request->group_name],
				['created_by' => auth()->id()]
			);
			$uploadedFiles = [];
			if ($request->hasFile('files')) {
				foreach ($request->file('files') as $file) {
					$filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
					$path = $file->storeAs('media', $filename, 'public');

					// Generate thumbnails and get paths
					$thumbnailData = $this->generateThumbnails($file, $path);
					$media = MediaFile::create([
						'title' => $request->title,
						'description' => $request->description,
						'file_path' => $path,
						'thumbnail_path' => $thumbnailData['thumbnail_path'],
						'medium_path' => $thumbnailData['medium_path'],
						'file_type' => $this->determineFileType($file->getMimeType()),
						'mime_type' => $file->getMimeType(),
						'size' => $file->getSize(),
						'thumbnail_size' => $thumbnailData['thumbnail_size'],
						'width' => $thumbnailData['width'],
						'height' => $thumbnailData['height'],
						'is_featured' => $request->boolean('is_featured', false),
						'uploaded_by' => auth()->id(),
						'created_by' => auth()->id()
					]);
					$media->groups()->attach($group->id);
					if ($request->filled('tags')) {
						$tagIds = collect(explode(',', $request->tags))
							->map(fn($tag) => trim($tag))
							->filter()
							->map(fn($tag) => MediaTag::firstOrCreate(['name' => $tag])->id)
							->toArray();
						$media->tags()->sync($tagIds);
					}
					if ($request->filled('industry_ids')) {
						$media->industries()->sync($request->industry_ids);
					}
					if ($request->filled('dealer_ids')) {
						$media->dealers()->sync($request->dealer_ids);
					}
					if ($request->filled('manufacturer_ids')) {
						$media->manufacturers()->sync($request->manufacturer_ids);
					}
					$uploadedFiles[] = [
						'id' => $media->id,
						'title' => $media->title,
						'url' => asset('storage/' . $media->file_path),
						'type' => $media->file_type
					];
				}
			}
			if ($request->has('existing_file_ids')) {
				$existingFileIds = json_decode($request->existing_file_ids, true);
				if (!empty($existingFileIds)) {
					foreach ($existingFileIds as $fileId) {
						$media = MediaFile::findOrFail($fileId);
						$media->update([
							'title' => $request->title,
							'description' => $request->description,
							'is_featured' => $request->boolean('is_featured', false)
						]);
						$media->groups()->sync([$group->id]);
						if ($request->filled('tags')) {
							$tagIds = collect(explode(',', $request->tags))
								->map(fn($tag) => trim($tag))
								->filter()
								->map(fn($tag) => MediaTag::firstOrCreate(['name' => $tag])->id)
								->toArray();
							$media->tags()->sync($tagIds);
						}
						if ($request->filled('industry_ids')) {
							$media->industries()->sync($request->industry_ids);
						}
						if ($request->filled('dealer_ids')) {
							$media->dealers()->sync($request->dealer_ids);
						}
						if ($request->filled('manufacturer_ids')) {
							$media->manufacturers()->sync($request->manufacturer_ids);
						}
						$uploadedFiles[] = [
							'id' => $media->id,
							'title' => $media->title,
							'url' => asset('storage/' . $media->file_path),
							'type' => $media->file_type
						];
					}
				}
			}
			DB::commit();
			return response()->json([
				'message' => $request->hasFile('files') ? 'Files uploaded successfully' : 'Files updated successfully',
				'files' => $uploadedFiles,
				'status' => 'success'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error('Media upload/update error: ' . $e->getMessage());
			return response()->json([
				'message' => 'Error processing files',
				'error' => $e->getMessage()
			], 500);
		}
	}
	public function show(MediaFile $media) {
		$media->load(['groups', 'tags', 'industries', 'dealers', 'manufacturers', 'comments.user']);
		return response()->json([
			'data' => array_merge($media->toArray(), [
				'likes_count' => $media->likes()->count(),
				'views_count' => $media->uniqueViewCount(),
				'comments_count' => $media->comments()->count(),
				'is_liked_by_user' => $media->isLikedByUser(auth()->id()),
				'comments' => $media->comments()->with('user')->latest()->get()
			])
		]);
	}

	private function determineFileType($mimeType) {
		if (strpos($mimeType, 'image/') === 0) {
			return 'image';
		}
		if (strpos($mimeType, 'video/') === 0) {
			return 'video';
		}
		$documentTypes = [
			'application/pdf',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'text/csv',
			'application/csv'
		];
		if (in_array($mimeType, $documentTypes)) {
			return 'document';
		}
		throw new \Exception('Unsupported file type: ' . $mimeType);
	}

	public function createGroup(Request $request) {
		$request->validate([
			'name' => 'required|string|max:255',
			'description' => 'nullable|string'
		]);
		$group = MediaGroup::create([
			'name' => $request->name,
			'description' => $request->description,
			'created_by' => auth()->id()
		]);
		return response()->json([
			'message' => 'Group created successfully',
			'data' => $group
		]);
	}

	public function getGroups(Request $request) {
		try {
			$query = MediaGroup::select('media_groups.*')
				->leftJoin('media_file_group', 'media_groups.id', '=', 'media_file_group.media_group_id')
				->leftJoin('media_files', 'media_file_group.media_file_id', '=', 'media_files.id')
				->groupBy('media_groups.id')
				->selectRaw('media_groups.*, COUNT(DISTINCT CASE WHEN media_files.deleted_at IS NULL THEN media_files.id END) as files_count');
			if ($request->has('search') && !empty($request->search)) {
				$query->where('media_groups.name', 'LIKE', '%' . $request->search . '%');
			}
			$groups = $query->orderBy('media_groups.name')->get();
			$groupData = $groups->map(function ($group) {
				return [
					'id' => $group->id,
					'name' => $group->name,
					'description' => $group->description,
					'files_count' => (int) $group->files_count
				];
			});
			return response()->json([
				'data' => $groupData,
				'status' => 'success'
			]);
		} catch (\Exception $e) {
			\Log::error('Error in getGroups: ' . $e->getMessage());
			\Log::error('Stack trace: ' . $e->getTraceAsString());

			return response()->json([
				'message' => 'Error fetching groups',
				'error' => $e->getMessage()
			], 500);
		}
	}
	public function updateGroup(Request $request) {
		try {
			$request->validate([
				'group_name' => 'required|string',
				'existing_file_ids' => 'required|json',
				'title' => 'required|string',
				'description' => 'nullable|string',
				'industry_ids' => 'nullable|array',
				'dealer_ids' => 'nullable|array',
				'manufacturer_ids' => 'nullable|array',
				'tags' => 'nullable|string',
				'is_featured' => 'required|boolean'
			]);
			$fileIds = json_decode($request->existing_file_ids, true);
			DB::beginTransaction();
			$tagIds = [];
			if ($request->tags) {
				$tagIds = collect(explode(',', $request->tags))
					->map(fn($tag) => trim($tag))
					->filter()
					->map(fn($tag) => MediaTag::firstOrCreate(['name' => $tag])->id)
					->toArray();
			}
			foreach ($fileIds as $fileId) {
				$media = MediaFile::findOrFail($fileId);
				if ($request->hasFile('files')) {
					$this->cleanupMediaFiles($media);
					$file = $request->file('files')[0];
					$filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
					$path = $file->storeAs('media', $filename, 'public');
					$thumbnailData = $this->generateThumbnails($file, $path);
					$media->update([
						'file_path' => $path,
						'thumbnail_path' => $thumbnailData['thumbnail_path'],
						'medium_path' => $thumbnailData['medium_path'],
						'size' => $file->getSize(),
						'thumbnail_size' => $thumbnailData['thumbnail_size'],
						'width' => $thumbnailData['width'],
						'height' => $thumbnailData['height'],
					]);
				}
				$media->update([
					'title' => $request->title,
					'description' => $request->description,
					'is_featured' => $request->boolean('is_featured')
				]);
				$media->tags()->sync($tagIds);
				$media->industries()->sync($request->industry_ids ?: []);
				$media->dealers()->sync($request->dealer_ids ?: []);
				$media->manufacturers()->sync($request->manufacturer_ids ?: []);
			}
			DB::commit();
			return response()->json([
				'message' => 'Files updated successfully',
				'status' => 'success'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'message' => 'Error updating files',
				'error' => $e->getMessage()
			], 500);
		}
	}
	public function removeFromGroup(Request $request) {
		try {
			$request->validate([
				'group_name' => 'required|string',
				'media_ids' => 'required|array',
				'media_ids.*' => 'exists:media_files,id'
			]);
			$group = MediaGroup::where('name', $request->group_name)->firstOrFail();
			$group->files()->detach($request->media_ids);
			return response()->json([
				'message' => 'Files removed from group successfully',
				'status' => 'success'
			]);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Error removing files from group',
				'error' => $e->getMessage()
			], 500);
		}
	}
	public function getGroupInfo(Request $request) {
		try {
			$request->validate([
				'group_name' => 'required|string'
			]);
			$group = MediaGroup::where('name', $request->group_name)
				->with([
					'files' => function ($query) {
						$query->with(['tags', 'industries', 'dealers', 'manufacturers']);
					}
				])
				->firstOrFail();
			$files = $group->files;
			$data = [
				'title' => $this->findCommonValue($files, 'title'),
				'description' => $this->findCommonValue($files, 'description'),
				'is_featured' => $this->findCommonValue($files, 'is_featured'),
				'tags' => $this->findCommonTags($files),
				'industries' => $files->flatMap->industries->unique('id')->values(),
				'dealers' => $files->flatMap->dealers->unique('id')->values(),
				'manufacturers' => $files->flatMap->manufacturers->unique('id')->values(),
				'files' => $files->map(function ($file) {
					return [
						'id' => $file->id,
						'title' => $file->title,
						'size' => $file->size,
						'mime_type' => $file->mime_type,
						'file_type' => $file->file_type,
						'url' => asset('storage/' . $file->file_path)
					];
				})
			];
			return response()->json([
				'data' => $data,
				'status' => 'success'
			]);
		} catch (\Exception $e) {
			return response()->json([
				'message' => 'Error fetching group information',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function deleteGroup($groupName) {
		DB::beginTransaction();
		try {
			$group = MediaGroup::where('name', $groupName)->firstOrFail();
			$mediaFiles = $group->files;
			foreach ($mediaFiles as $media) {
				if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
					Storage::disk('public')->delete($media->file_path);
				}
				$media->industries()->detach();
				$media->dealers()->detach();
				$media->manufacturers()->detach();
				$media->tags()->detach();
				$media->groups()->detach();
				$media->delete();
			}
			$group->delete();
			DB::commit();
			return response()->json(['message' => 'Group and associated media deleted successfully']);
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error('Error deleting group: ' . $e->getMessage());
			return response()->json(['message' => 'Error deleting group: ' . $e->getMessage()], 500);
		}
	}

	public function getBulkInfo(Request $request) {
		try {
			$ids = json_decode($request->input('ids'), true);
			if (empty($ids)) {
				return response()->json([
					'message' => 'No IDs provided',
					'common' => [
						'title' => null,
						'description' => null,
						'group_name' => null,
						'tags' => [],
						'industry_ids' => [],
						'dealer_ids' => [],
						'manufacturer_ids' => [],
						'is_featured' => false
					]
				]);
			}
			$mediaItems = MediaFile::whereIn('id', $ids)
				->with(['tags', 'industries', 'dealers', 'manufacturers', 'group'])->get();
			$common = [
				'title' => $this->findCommonValue($mediaItems, 'title'),
				'description' => $this->findCommonValue($mediaItems, 'description'),
				'group_name' => $this->findCommonValue($mediaItems, 'group.name'),
				'tags' => $this->findCommonTags($mediaItems),
				'industry_ids' => $this->findCommonRelationIds($mediaItems, 'industries'),
				'dealer_ids' => $this->findCommonRelationIds($mediaItems, 'dealers'),
				'manufacturer_ids' => $this->findCommonRelationIds($mediaItems, 'manufacturers'),
				'is_featured' => $this->findCommonValue($mediaItems, 'is_featured'),
			];
			$industries = $mediaItems->pluck('industries')->flatten()->unique('id')->values();
			$dealers = $mediaItems->pluck('dealers')->flatten()->unique('id')->values();
			$manufacturers = $mediaItems->pluck('manufacturers')->flatten()->unique('id')->values();
			return response()->json([
				'common' => $common,
				'data' => [
					'industries' => $industries,
					'dealers' => $dealers,
					'manufacturers' => $manufacturers
				]
			]);
		} catch (\Exception $e) {
			\Log::error('Error in getBulkInfo: ' . $e->getMessage());
			return response()->json([
				'message' => 'Error fetching bulk info',
				'error' => $e->getMessage()
			], 500);
		}
	}


	private function findCommonValue($items, $field) {
		$values = $items->pluck($field)->unique();
		return $values->count() === 1 ? $values->first() : null;
	}

	private function findCommonTags($items) {
		$commonTags = null;
		foreach ($items as $item) {
			$tags = $item->tags->pluck('name')->toArray();
			$commonTags = $commonTags === null ? $tags : array_intersect($commonTags, $tags);
		}
		return $commonTags ?? [];
	}

	private function findCommonRelationIds($items, $relation) {
		$commonIds = null;
		foreach ($items as $item) {
			$ids = $item->$relation->pluck('id')->toArray();
			$commonIds = $commonIds === null ? $ids : array_intersect($commonIds, $ids);
		}
		return $commonIds ?? [];
	}


	public function bulkUpdate(Request $request) {
		try {
			$validated = $request->validate([
				'media_ids' => 'array',
				'media_ids.*' => 'nullable|exists:media_files,id',
				'title' => 'required|string|max:255',
				'description' => 'nullable|string',
				'group_name' => 'required|string|max:255',
				'tags' => 'nullable|string',
				'is_featured' => 'nullable',
				'files' => 'nullable|array',
				'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,csv,xlsx,xls,pdf,docx|max:500000',
				'industry_ids' => 'nullable|array',
				'industry_ids.*' => 'nullable|integer|exists:industries,id',
				'dealer_ids' => 'nullable|array',
				'dealer_ids.*' => 'nullable|integer|exists:dealers,id',
				'manufacturer_ids' => 'nullable|array',
				'manufacturer_ids.*' => 'nullable|integer|exists:manufacturers,id'
			]);
			DB::beginTransaction();
			$group = MediaGroup::firstOrCreate(
				['name' => $request->group_name],
				['created_by' => auth()->id()]
			);
			$tagIds = [];
			if ($request->filled('tags')) {
				$tagIds = collect(explode(',', $request->tags))
					->map(fn($tag) => trim($tag))->filter()
					->map(fn($tag) => MediaTag::firstOrCreate(['name' => $tag])->id)->toArray();
			}
			$currentMediaIds = $group->files()->pluck('media_files.id')->toArray();
			$newMediaIds = $request->media_ids ?? [];
			$mediaIdsToRemove = array_diff($currentMediaIds, $newMediaIds);
			foreach ($mediaIdsToRemove as $mediaId) {
				$media = MediaFile::find($mediaId);
				if ($media) {
					$this->cleanupMediaFiles($media);
					$media->industries()->detach();
					$media->dealers()->detach();
					$media->manufacturers()->detach();
					$media->tags()->detach();
					$media->groups()->detach();
					$media->delete();
				}
			}
			foreach ($newMediaIds as $mediaId) {
				if (!$mediaId) continue;
				$media = MediaFile::findOrFail($mediaId);
				$media->update([
					'title' => $request->title,
					'description' => $request->description,
					'is_featured' => (bool) $request->is_featured
				]);
				$media->groups()->sync([$group->id]);
				$media->tags()->sync($tagIds);
				$media->industries()->sync($request->industry_ids ?? []);
				$media->dealers()->sync($request->dealer_ids ?? []);
				$media->manufacturers()->sync($request->manufacturer_ids ?? []);
			}
			if ($request->hasFile('files')) {
				foreach ($request->file('files') as $file) {
					$filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
					$path = $file->storeAs('media', $filename, 'public');
					$thumbnailData = $this->generateThumbnails($file, $path);
					$media = MediaFile::create([
						'title' => $request->title,
						'description' => $request->description,
						'file_path' => $path,
						'thumbnail_path' => $thumbnailData['thumbnail_path'],
						'medium_path' => $thumbnailData['medium_path'],
						'file_type' => $this->determineFileType($file->getMimeType()),
						'mime_type' => $file->getMimeType(),
						'size' => $file->getSize(),
						'thumbnail_size' => $thumbnailData['thumbnail_size'],
						'width' => $thumbnailData['width'],
						'height' => $thumbnailData['height'],
						'is_featured' => (bool) $request->is_featured,
						'uploaded_by' => auth()->id(),
						'created_by' => auth()->id()
					]);
					$media->groups()->attach($group->id);
					$media->tags()->attach($tagIds);
					$media->industries()->attach($request->industry_ids ?? []);
					$media->dealers()->attach($request->dealer_ids ?? []);
					$media->manufacturers()->attach($request->manufacturer_ids ?? []);
				}
			}
			DB::commit();
			return response()->json([
				'message' => 'Files updated successfully',
				'status' => 'success'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error('Bulk update error: ' . $e->getMessage());
			\Log::error('Stack trace: ' . $e->getTraceAsString());
			return response()->json([
				'message' => 'Error updating files',
				'error' => $e->getMessage()
			], 500);
		}
	}
	public function bulkDelete(Request $request) {
		DB::beginTransaction();
		try {
			MediaFile::whereIn('id', $request->media_ids)->each(function ($media) {
				$this->cleanupMediaFiles($media);
				$media->industries()->detach();
				$media->dealers()->detach();
				$media->manufacturers()->detach();
				$media->tags()->detach();
				$media->delete();
			});
			DB::commit();
			return response()->json(['message' => 'Media items deleted successfully']);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(['message' => 'Error deleting media items'], 500);
		}
	}
	private function generateThumbnails($file, $originalPath) {
		try {
			$manager = new ImageManager(new Driver());
			$thumbPath = 'media/thumbnails/';
			$mediumPath = 'media/medium/';
			$filename = basename($originalPath);
			foreach ([$thumbPath, $mediumPath] as $path) {
				if (!Storage::disk('public')->exists($path)) {
					Storage::disk('public')->makeDirectory($path);
				}
			}
			$dimensions = [
				'thumbnail' => [200, 200],
				'medium' => [800, 800]
			];
			$paths = [
				'thumbnail_path' => null,
				'medium_path' => null,
				'width' => null,
				'height' => null,
				'thumbnail_size' => null
			];
			if (strpos($file->getMimeType(), 'image/') === 0) {
				$image = $manager->read($file->getPathname());
				$paths['width'] = $image->width();
				$paths['height'] = $image->height();
				$thumbFullPath = Storage::disk('public')->path($thumbPath . $filename);
				$image->cover($dimensions['thumbnail'][0], $dimensions['thumbnail'][1])->save($thumbFullPath);
				$paths['thumbnail_path'] = $thumbPath . $filename;
				$paths['thumbnail_size'] = File::size($thumbFullPath);
				$mediumFullPath = Storage::disk('public')->path($mediumPath . $filename);
				$image->cover($dimensions['medium'][0], $dimensions['medium'][1])->save($mediumFullPath);
				$paths['medium_path'] = $mediumPath . $filename;
			} elseif (strpos($file->getMimeType(), 'video/') === 0) {
				$paths['thumbnail_path'] = 'defaults/video-thumbnail.jpg';
				$paths['medium_path'] = 'defaults/video-thumbnail.jpg';
			} else {
				$paths['thumbnail_path'] = 'defaults/document-thumbnail.jpg';
				$paths['medium_path'] = 'defaults/document-thumbnail.jpg';
			}
			return $paths;
		} catch (\Exception $e) {
			\Log::error('Thumbnail generation error: ' . $e->getMessage());
			throw $e;
		}
	}

	private function cleanupMediaFiles($media) {
		$paths = [
			$media->file_path,
			$media->thumbnail_path,
			$media->medium_path
		];
		foreach ($paths as $path) {
			if ($path && Storage::disk('public')->exists($path)) {
				Storage::disk('public')->delete($path);
			}
		}
	}

	public function addComment(Request $request, MediaFile $media) {
		$validated = $request->validate([
			'comment' => 'required|string|max:1000'
		]);
		$comment = $media->comments()->create([
			'user_id' => auth()->id(),
			'comment' => $validated['comment'],
			'is_approved' => !auth()->user()->is_admin
		]);
		return response()->json([
			'status' => 'success',
			'comment' => $comment->load('user')
		]);
	}

	public function toggleLike(MediaFile $media) {
		try {
			$user = auth()->user();
			$like = MediaLike::where('media_file_id', $media->id)->where('user_id', $user->id);
			if ($like->exists()) {
				$like->delete();
				$action = 'unliked';
			} else {
				MediaLike::updateOrCreate(
					[
						'media_file_id' => $media->id,
						'user_id' => $user->id
					],
					[
						'created_at' => now(),
						'updated_at' => now()
					]
				);
				$action = 'liked';
			}
			return response()->json([
				'status' => 'success',
				'action' => $action,
				'likes_count' => $media->likes()->count()
			]);
		} catch (\Exception $e) {
			\Log::error('Like toggle error: ' . $e->getMessage());
			return response()->json([
				'status' => 'error',
				'message' => 'Error processing like'
			], 500);
		}
	}

	public function recordView(MediaFile $media) {
		if (auth()->user()->is_admin) {
			return response()->json(['status' => 'success']);
		}
		$media->views()->create([
			'user_id' => auth()->id(),
			'ip_address' => request()->ip()
		]);
		return response()->json([
			'status' => 'success',
			'views_count' => $media->uniqueViewCount()
		]);
	}

	public function deleteComment($commentId) {
		try {
			$comment = MediaComment::findOrFail($commentId);
			$comment->delete();
			return response()->json([
				'status' => 'success',
				'message' => 'Comment deleted successfully'
			]);
		} catch (\Exception $e) {
			return response()->json([
				'status' => 'error',
				'message' => 'Error deleting comment'
			], 500);
		}
	}


	public function getMediaEngagement(MediaFile $media) {
		$viewsCount = $media->views()->count();
		$likesCount = $media->likes()->count();
		$comments = $media->comments()->with('user:id,name')->latest()->get()
			->map(function ($comment) {
				return [
					'id' => $comment->id,
					'comment' => $comment->comment,
					'created_at' => $comment->created_at,
					'user' => [
						'id' => $comment->user->id,
						'name' => $comment->user->name
					]
				];
			});
		$likes = $media->likes()->with('user:id,name')->get()
			->map(function ($like) {
				return [
					'user_id' => $like->user->id,
					'user_name' => $like->user->name,
					'created_at' => $like->created_at
				];
			});
		return response()->json([
			'likes_count' => $likesCount,
			'views_count' => $viewsCount,
			'comments' => $comments,
			'likes' => $likes,
			'is_liked' => false
		]);
	}
	public function getAssignedUsers(Request $request) {
		$request->validate([
			'industry_ids' => 'nullable|array',
			'industry_ids.*' => 'exists:industries,id',
			'dealer_ids' => 'nullable|array',
			'dealer_ids.*' => 'exists:dealers,id',
			'manufacturer_ids' => 'nullable|array',
			'manufacturer_ids.*' => 'exists:manufacturers,id',
		]);
		
		if (!$request->filled('industry_ids') && !$request->filled('dealer_ids') && !$request->filled('manufacturer_ids')) {
			return response()->json([
				'users' => [],
				'total' => 0
			]);
		}
		
		$industryIds = $request->industry_ids;
		$dealerIds = $request->dealer_ids;
		$manufacturerIds = $request->manufacturer_ids;
		
		$query = User::query()
			->with(['industries', 'dealers', 'manufacturers', 'userDetail.company', 'role'])
			->where('is_active', true)
			->where('is_approved', true)
			->where('role_id', 2);
		
		// FIRST REQUIREMENT: User must match at least one Dealer Company if provided
		if ($request->filled('dealer_ids')) {
			$query->whereHas('dealers', function ($q) use ($dealerIds) {
				$q->whereIn('dealers.id', $dealerIds);
			});
		}
		
		// SECOND REQUIREMENT: User must match at least one Industry OR one Manufacturer if either is provided
		if ($request->filled('industry_ids') || $request->filled('manufacturer_ids')) {
			$query->where(function($q) use ($request, $industryIds, $manufacturerIds) {
				// Match any industry if industries are provided
				if ($request->filled('industry_ids')) {
					$q->orWhereHas('industries', function ($subq) use ($industryIds) {
						$subq->whereIn('industries.id', $industryIds);
					});
				}
				
				// Match any manufacturer if manufacturers are provided
				if ($request->filled('manufacturer_ids')) {
					$q->orWhereHas('manufacturers', function ($subq) use ($manufacturerIds) {
						$subq->whereIn('manufacturers.id', $manufacturerIds);
					});
				}
			});
		}
		
		$users = $query->get()->map(function ($user) {
			return [
				'id' => $user->id,
				'name' => $user->name,
				'email' => $user->email,
				'industries' => $user->industries->pluck('name'),
				'dealers' => $user->dealers->pluck('name'),
				'manufacturers' => $user->manufacturers->pluck('name')
			];
		});
		
		return response()->json([
			'users' => $users,
			'total' => $users->count()
		]);
	}
}

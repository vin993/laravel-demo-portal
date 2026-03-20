<?php
namespace App\Http\Controllers;

use App\Models\MarketingMaterial;
use App\Models\MarketingMaterialGroup;
use App\Models\MarketingMaterialTag;
use App\Models\Manufacturer;
use App\Models\Dealer;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use App\Models\MarketingMaterialLike;
use App\Models\MarketingMaterialView;

class MarketingMaterialController extends Controller 
{
    public function index() 
    {
        $groups = MarketingMaterialGroup::all();
        return view('admin.marketing-materials.newIndex', compact('groups'));
    }

    public function getMaterialFiles(Request $request) 
    {
        try {
            $query = MarketingMaterial::with(['groups', 'tags', 'industries', 'dealers', 'manufacturers']);
            
            if ($request->type && $request->type !== 'all') {
                $query->where('file_type', $request->type);
            }
            
            if ($request->filled('group')) {
                $query->whereHas('groups', function ($q) use ($request) {
                    $q->where('name', $request->group);
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
                'message' => 'Error fetching marketing materials',
                'error' => $e->getMessage()
            ], 500);
        }
    }
	public function store(Request $request) 
{
    try {
        if ($request->hasFile('files')) {
            $validated = $request->validate([
                'files' => 'required',
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

        $group = MarketingMaterialGroup::firstOrCreate(
            ['name' => $request->group_name],
            ['created_by' => auth()->id()]
        );

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('marketing-materials', $filename, 'public');

                // Generate thumbnails and get paths
                $thumbnailData = $this->generateThumbnails($file, $path);

                $material = MarketingMaterial::create([
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

                $material->groups()->attach($group->id);

                if ($request->filled('tags')) {
                    $tagIds = collect(explode(',', $request->tags))
                        ->map(fn($tag) => trim($tag))
                        ->filter()
                        ->map(fn($tag) => MarketingMaterialTag::firstOrCreate(['name' => $tag])->id)
                        ->toArray();
                    $material->tags()->sync($tagIds);
                }

                if ($request->filled('industry_ids')) {
                    $material->industries()->sync($request->industry_ids);
                }

                if ($request->filled('dealer_ids')) {
                    $material->dealers()->sync($request->dealer_ids);
                }

                if ($request->filled('manufacturer_ids')) {
                    $material->manufacturers()->sync($request->manufacturer_ids);
                }

                $uploadedFiles[] = [
                    'id' => $material->id,
                    'title' => $material->title,
                    'url' => asset('storage/' . $material->file_path),
                    'type' => $material->file_type
                ];
            }
        }

        if ($request->has('existing_file_ids')) {
            $existingFileIds = json_decode($request->existing_file_ids, true);
            if (!empty($existingFileIds)) {
                foreach ($existingFileIds as $fileId) {
                    $material = MarketingMaterial::findOrFail($fileId);
                    $material->update([
                        'title' => $request->title,
                        'description' => $request->description,
                        'is_featured' => $request->boolean('is_featured', false)
                    ]);

                    $material->groups()->sync([$group->id]);

                    if ($request->filled('tags')) {
                        $tagIds = collect(explode(',', $request->tags))
                            ->map(fn($tag) => trim($tag))
                            ->filter()
                            ->map(fn($tag) => MarketingMaterialTag::firstOrCreate(['name' => $tag])->id)
                            ->toArray();
                        $material->tags()->sync($tagIds);
                    }

                    if ($request->filled('industry_ids')) {
                        $material->industries()->sync($request->industry_ids);
                    }

                    if ($request->filled('dealer_ids')) {
                        $material->dealers()->sync($request->dealer_ids);
                    }

                    if ($request->filled('manufacturer_ids')) {
                        $material->manufacturers()->sync($request->manufacturer_ids);
                    }

                    $uploadedFiles[] = [
                        'id' => $material->id,
                        'title' => $material->title,
                        'url' => asset('storage/' . $material->file_path),
                        'type' => $material->file_type
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
        \Log::error('Marketing material upload/update error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error processing files',
            'error' => $e->getMessage()
        ], 500);
    }
}

private function determineFileType($mimeType)
{
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
public function createGroup(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string'
    ]);

    $group = MarketingMaterialGroup::create([
        'name' => $request->name,
        'description' => $request->description,
        'created_by' => auth()->id()
    ]);

    return response()->json([
        'message' => 'Group created successfully',
        'data' => $group
    ]);
}

public function getGroups(Request $request)
{
    try {
        $query = MarketingMaterialGroup::select('marketing_material_groups.*')
            ->leftJoin('mm_group_pivot', 'marketing_material_groups.id', '=', 'mm_group_pivot.group_id')
            ->leftJoin('marketing_materials', 'mm_group_pivot.material_id', '=', 'marketing_materials.id')
            ->groupBy('marketing_material_groups.id')
            ->selectRaw('marketing_material_groups.*, COUNT(DISTINCT CASE WHEN marketing_materials.deleted_at IS NULL THEN marketing_materials.id END) as files_count');

        if ($request->has('search') && !empty($request->search)) {
            $query->where('marketing_material_groups.name', 'LIKE', '%' . $request->search . '%');
        }

        $groups = $query->orderBy('marketing_material_groups.name')->get();

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

private function generateThumbnails($file, $originalPath)
{
    try {
        $manager = new ImageManager(new Driver());
        $thumbPath = 'marketing-materials/thumbnails/';
        $mediumPath = 'marketing-materials/medium/';
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

private function cleanupMaterialFiles($material)
{
    $paths = [
        $material->file_path,
        $material->thumbnail_path,
        $material->medium_path
    ];

    foreach ($paths as $path) {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

public function getGroupInfo(Request $request) {
	try {
		$request->validate([
			'group_name' => 'required|string|exists:marketing_material_groups,name'
		]);
		$group = MarketingMaterialGroup::where('name', $request->group_name)
			->with([
				'materials' => function ($query) {
					$query->with(['industries', 'dealers', 'manufacturers', 'tags']);
				}
			])->firstOrFail();
		$files = $group->materials;
		$allManufacturers = Manufacturer::select('id', 'name')->get();
		$allDealers = Dealer::select('id', 'name')->get();
		$allIndustries = Industry::select('id', 'name')->get();
		$title = $files->first()?->title ?? '';
		$description = $files->first()?->description ?? '';
		$data = [
			'name' => $group->name,
			'description' => $description,
			'title' => $title,
			'is_featured' => (bool) $this->findCommonValue($files, 'is_featured'),
			'tags' => $this->findCommonTags($files),
			'industries' => $files->flatMap->industries->unique('id')->values() ?? [],
			'dealers' => $files->flatMap->dealers->unique('id')->values() ?? [],
			'manufacturers' => $files->flatMap->manufacturers->unique('id')->values() ?? [],
			'allManufacturers' => $allManufacturers ?? [],
			'allDealers' => $allDealers ?? [],
			'allIndustries' => $allIndustries ?? [],
			'files' => $files->map(function ($file) {
				return [
					'id' => $file->id,
					'title' => $file->title,
					'size' => $file->size,
					'mime_type' => $file->mime_type,
					'file_type' => $file->file_type,
					'url' => asset('storage/' . $file->file_path)
				];
			}) ?? []
		];
		return response()->json([
			'data' => $data,
			'status' => 'success'
		]);
	} catch (\Exception $e) {
		\Log::error('Error in getGroupInfo', ['error' => $e->getMessage()]);
		return response()->json([
			'message' => 'Error fetching group information',
			'error' => $e->getMessage()
		], 500);
	}
}
public function deleteGroup($groupName)
{
    DB::beginTransaction();
    try {
        $group = MarketingMaterialGroup::where('name', $groupName)->firstOrFail();
        $materials = $group->files;

        foreach ($materials as $material) {
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            $material->industries()->detach();
            $material->dealers()->detach();
            $material->manufacturers()->detach();
            $material->tags()->detach();
            $material->groups()->detach();
            $material->delete();
        }

        $group->delete();
        DB::commit();

        return response()->json(['message' => 'Group and associated materials deleted successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error deleting group: ' . $e->getMessage());
        return response()->json(['message' => 'Error deleting group: ' . $e->getMessage()], 500);
    }
}

public function getBulkInfo(Request $request)
{
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

        $materials = MarketingMaterial::whereIn('id', $ids)
            ->with(['tags', 'industries', 'dealers', 'manufacturers', 'group'])->get();

        $common = [
            'title' => $this->findCommonValue($materials, 'title'),
            'description' => $this->findCommonValue($materials, 'description'),
            'group_name' => $this->findCommonValue($materials, 'group.name'),
            'tags' => $this->findCommonTags($materials),
            'industry_ids' => $this->findCommonRelationIds($materials, 'industries'),
            'dealer_ids' => $this->findCommonRelationIds($materials, 'dealers'),
            'manufacturer_ids' => $this->findCommonRelationIds($materials, 'manufacturers'),
            'is_featured' => $this->findCommonValue($materials, 'is_featured'),
        ];

        $industries = $materials->pluck('industries')->flatten()->unique('id')->values();
        $dealers = $materials->pluck('dealers')->flatten()->unique('id')->values();
        $manufacturers = $materials->pluck('manufacturers')->flatten()->unique('id')->values();

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

private function findCommonValue($items, $field)
{
    $values = $items->pluck($field)->unique();
    return $values->count() === 1 ? $values->first() : null;
}

private function findCommonTags($items)
{
    $commonTags = null;
    foreach ($items as $item) {
        $tags = $item->tags->pluck('name')->toArray();
        $commonTags = $commonTags === null ? $tags : array_intersect($commonTags, $tags);
    }
    return $commonTags ?? [];
}

private function findCommonRelationIds($items, $relation)
{
    $commonIds = null;
    foreach ($items as $item) {
        $ids = $item->$relation->pluck('id')->toArray();
        $commonIds = $commonIds === null ? $ids : array_intersect($commonIds, $ids);
    }
    return $commonIds ?? [];
}
public function bulkUpdate(Request $request)
{
    try {
        $validated = $request->validate([
            'material_ids' => 'array',
            'material_ids.*' => 'nullable|exists:marketing_materials,id',
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

        $group = MarketingMaterialGroup::firstOrCreate(
            ['name' => $request->group_name],
            ['created_by' => auth()->id()]
        );

        $tagIds = [];
        if ($request->filled('tags')) {
            $tagIds = collect(explode(',', $request->tags))
                ->map(fn($tag) => trim($tag))
                ->filter()
                ->map(fn($tag) => MarketingMaterialTag::firstOrCreate(['name' => $tag])->id)
                ->toArray();
        }

        $currentMaterialIds = $group->files()->pluck('marketing_materials.id')->toArray();
        $newMaterialIds = $request->material_ids ?? [];
        $materialIdsToRemove = array_diff($currentMaterialIds, $newMaterialIds);

        foreach ($materialIdsToRemove as $materialId) {
            $material = MarketingMaterial::find($materialId);
            if ($material) {
                $this->cleanupMaterialFiles($material);
                $material->industries()->detach();
                $material->dealers()->detach();
                $material->manufacturers()->detach();
                $material->tags()->detach();
                $material->groups()->detach();
                $material->delete();
            }
        }

        // Update existing materials
        foreach ($newMaterialIds as $materialId) {
            if (!$materialId) continue;
            
            $material = MarketingMaterial::findOrFail($materialId);
            $material->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_featured' => (bool) $request->is_featured
            ]);

            $material->groups()->sync([$group->id]);
            $material->tags()->sync($tagIds);
            $material->industries()->sync($request->industry_ids ?? []);
            $material->dealers()->sync($request->dealer_ids ?? []);
            $material->manufacturers()->sync($request->manufacturer_ids ?? []);
        }

        // Handle new file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('marketing-materials', $filename, 'public');
                $thumbnailData = $this->generateThumbnails($file, $path);

                $material = MarketingMaterial::create([
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

                $material->groups()->attach($group->id);
                $material->tags()->attach($tagIds);
                $material->industries()->attach($request->industry_ids ?? []);
                $material->dealers()->attach($request->dealer_ids ?? []);
                $material->manufacturers()->attach($request->manufacturer_ids ?? []);
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

public function getMaterialEngagement(MarketingMaterial $media) {
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

// public function getAssignedUsers(Request $request) {
//     $request->validate([
//         'industry_ids' => 'nullable|array',
//         'industry_ids.*' => 'exists:industries,id',
//         'dealer_ids' => 'nullable|array',
//         'dealer_ids.*' => 'exists:dealers,id',
//         'manufacturer_ids' => 'nullable|array',
//         'manufacturer_ids.*' => 'exists:manufacturers,id',
//     ]);
//     if (!$request->filled('industry_ids') && !$request->filled('dealer_ids') && !$request->filled('manufacturer_ids')) {
//         return response()->json([
//             'users' => [],
//             'total' => 0
//         ]);
//     }
//     $industryIds = $request->industry_ids;
//     $dealerIds = $request->dealer_ids;
//     $manufacturerIds = $request->manufacturer_ids;
//     $query = User::query()->with(['industries', 'dealers', 'manufacturers', 'userDetail.company', 'role'])->where('is_active', true)->where('is_approved', true)->where('role_id', 2);
//     // Only industry_ids provided
//     if ($request->filled('industry_ids') && !$request->filled('dealer_ids') && !$request->filled('manufacturer_ids')) {
//         $query->doesntHave('dealers')
//             ->doesntHave('manufacturers')
//             ->whereHas('industries', function ($q) use ($industryIds) {
//                 $q->whereIn('industries.id', $industryIds);
//             }, '=', count($industryIds))
//             ->whereDoesntHave('industries', function ($q) use ($industryIds) {
//                 $q->whereNotIn('industries.id', $industryIds);
//             });
//     }
//     // Only dealer_ids provided
//     else if (!$request->filled('industry_ids') && $request->filled('dealer_ids') && !$request->filled('manufacturer_ids')) {
//         $query->doesntHave('industries')
//             ->doesntHave('manufacturers')
//             // Old code to fetch exact matches
//             // ->whereHas('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereIn('dealers.id', $dealerIds);
//             // }, '=', count($dealerIds))
//             // ->whereDoesntHave('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereNotIn('dealers.id', $dealerIds);
//             // });

//             ->whereHas('dealers', function ($q) use ($dealerIds) {
//                 $q->whereIn('dealers.id', $dealerIds);
//             });
//     }
//     // Only manufacturer_ids provided
//     else if (!$request->filled('industry_ids') && !$request->filled('dealer_ids') && $request->filled('manufacturer_ids')) {
//         $query->doesntHave('industries')
//             ->doesntHave('dealers')
//             ->whereHas('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereIn('manufacturers.id', $manufacturerIds);
//             }, '=', count($manufacturerIds))
//             ->whereDoesntHave('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereNotIn('manufacturers.id', $manufacturerIds);
//             });
//     }
//     // Both industry_ids and dealer_ids provided
//     else if ($request->filled('industry_ids') && $request->filled('dealer_ids') && !$request->filled('manufacturer_ids')) {
//         $query->has('industries')
//             ->has('dealers')
//             ->doesntHave('manufacturers')
//             ->whereHas('industries', function ($q) use ($industryIds) {
//                 $q->whereIn('industries.id', $industryIds);
//             }, '=', count($industryIds))
//             ->whereDoesntHave('industries', function ($q) use ($industryIds) {
//                 $q->whereNotIn('industries.id', $industryIds);
//             })
//             // Old code to fetch exact matches
//             // ->whereHas('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereIn('dealers.id', $dealerIds);
//             // }, '=', count($dealerIds))
//             // ->whereDoesntHave('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereNotIn('dealers.id', $dealerIds);
//             // });

//             ->whereHas('dealers', function ($q) use ($dealerIds) {
//                 $q->whereIn('dealers.id', $dealerIds);
//             });
//     }
//     // Both industry_ids and manufacturer_ids provided
//     else if ($request->filled('industry_ids') && !$request->filled('dealer_ids') && $request->filled('manufacturer_ids')) {
//         $query->has('industries')
//             ->has('manufacturers')
//             ->doesntHave('dealers')
//             ->whereHas('industries', function ($q) use ($industryIds) {
//                 $q->whereIn('industries.id', $industryIds);
//             }, '=', count($industryIds))
//             ->whereDoesntHave('industries', function ($q) use ($industryIds) {
//                 $q->whereNotIn('industries.id', $industryIds);
//             })
//             ->whereHas('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereIn('manufacturers.id', $manufacturerIds);
//             }, '=', count($manufacturerIds))
//             ->whereDoesntHave('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereNotIn('manufacturers.id', $manufacturerIds);
//             });
//     }
//     // Both dealer_ids and manufacturer_ids provided
//     else if (!$request->filled('industry_ids') && $request->filled('dealer_ids') && $request->filled('manufacturer_ids')) {
//         $query->has('dealers')
//             ->has('manufacturers')
//             ->doesntHave('industries')
//             // Old code to fetch exact matches
//             // ->whereHas('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereIn('dealers.id', $dealerIds);
//             // }, '=', count($dealerIds))
//             // ->whereDoesntHave('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereNotIn('dealers.id', $dealerIds);
//             // })
//             ->whereHas('dealers', function ($q) use ($dealerIds) {
//                 $q->whereIn('dealers.id', $dealerIds);
//             })
//             ->whereHas('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereIn('manufacturers.id', $manufacturerIds);
//             }, '=', count($manufacturerIds))
//             ->whereDoesntHave('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereNotIn('manufacturers.id', $manufacturerIds);
//             });
//     }
//     // All three provided
//     else if ($request->filled('industry_ids') && $request->filled('dealer_ids') && $request->filled('manufacturer_ids')) {
//         $query->has('industries')
//             ->has('dealers')
//             ->has('manufacturers')
//             ->whereHas('industries', function ($q) use ($industryIds) {
//                 $q->whereIn('industries.id', $industryIds);
//             }, '=', count($industryIds))
//             ->whereDoesntHave('industries', function ($q) use ($industryIds) {
//                 $q->whereNotIn('industries.id', $industryIds);
//             })
//             // Old code to fetch exact matches
//             // ->whereHas('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereIn('dealers.id', $dealerIds);
//             // }, '=', count($dealerIds))
//             // ->whereDoesntHave('dealers', function ($q) use ($dealerIds) {
//             // 	$q->whereNotIn('dealers.id', $dealerIds);
//             // })

//             ->whereHas('dealers', function ($q) use ($dealerIds) {
//                 $q->whereIn('dealers.id', $dealerIds);
//             })
//             ->whereHas('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereIn('manufacturers.id', $manufacturerIds);
//             }, '=', count($manufacturerIds))
//             ->whereDoesntHave('manufacturers', function ($q) use ($manufacturerIds) {
//                 $q->whereNotIn('manufacturers.id', $manufacturerIds);
//             });
//     }
//     $users = $query->get()->map(function ($user) {
//         return [
//             'id' => $user->id,
//             'name' => $user->name,
//             'email' => $user->email,
//             'industries' => $user->industries->pluck('name'),
//             'dealers' => $user->dealers->pluck('name'),
//             'manufacturers' => $user->manufacturers->pluck('name')
//         ];
//     });
//     return response()->json([
//         'users' => $users,
//         'total' => $users->count()
//     ]);
// }

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
          
            if ($request->filled('industry_ids')) {
                $q->orWhereHas('industries', function ($subq) use ($industryIds) {
                    $subq->whereIn('industries.id', $industryIds);
                });
            }
            
          
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
private function queryForIndustriesOnly($query, $industryIds)
{
    $query->doesntHave('dealers')
        ->doesntHave('manufacturers')
        ->whereHas('industries', function ($q) use ($industryIds) {
            $q->whereIn('industries.id', $industryIds);
        }, '=', count($industryIds))
        ->whereDoesntHave('industries', function ($q) use ($industryIds) {
            $q->whereNotIn('industries.id', $industryIds);
        });
}

private function queryForDealersOnly($query, $dealerIds)
{
    $query->doesntHave('industries')
        ->doesntHave('manufacturers')
        ->whereHas('dealers', function ($q) use ($dealerIds) {
            $q->whereIn('dealers.id', $dealerIds);
        }, '=', count($dealerIds))
        ->whereDoesntHave('dealers', function ($q) use ($dealerIds) {
            $q->whereNotIn('dealers.id', $dealerIds);
        });
}

private function queryForManufacturersOnly($query, $manufacturerIds)
{
    $query->doesntHave('industries')
        ->doesntHave('dealers')
        ->whereHas('manufacturers', function ($q) use ($manufacturerIds) {
            $q->whereIn('manufacturers.id', $manufacturerIds);
        }, '=', count($manufacturerIds))
        ->whereDoesntHave('manufacturers', function ($q) use ($manufacturerIds) {
            $q->whereNotIn('manufacturers.id', $manufacturerIds);
        });
}

private function queryForIndustriesAndDealers($query, $industryIds, $dealerIds)
{
    $query->has('industries')
        ->has('dealers')
        ->doesntHave('manufacturers')
        ->whereHas('industries', function ($q) use ($industryIds) {
            $q->whereIn('industries.id', $industryIds);
        }, '=', count($industryIds))
        ->whereDoesntHave('industries', function ($q) use ($industryIds) {
            $q->whereNotIn('industries.id', $industryIds);
        })
        ->whereHas('dealers', function ($q) use ($dealerIds) {
            $q->whereIn('dealers.id', $dealerIds);
        }, '=', count($dealerIds))
        ->whereDoesntHave('dealers', function ($q) use ($dealerIds) {
            $q->whereNotIn('dealers.id', $dealerIds);
        });
}

public function recordView(MarketingMaterial $material)
{
    if (!auth()->user()->is_admin) {
        $material->views()->create([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip()
        ]);
    }
    
    return response()->json([
        'status' => 'success',
        'views_count' => $material->views()->count()
    ]);
}

public function toggleLike(MarketingMaterial $material)
{
    try {
        $user = auth()->user();
        $like = $material->likes()->where('user_id', $user->id);
        
        if ($like->exists()) {
            $like->delete();
            $action = 'unliked';
        } else {
            $material->likes()->create([
                'user_id' => $user->id
            ]);
            $action = 'liked';
        }

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'likes_count' => $material->likes()->count()
        ]);
    } catch (\Exception $e) {
        \Log::error('Like toggle error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Error processing like'
        ], 500);
    }
}

public function getMarketingMaterialEngagement(MarketingMaterial $material)
{
    $viewsCount = $material->views()->count();
    $likesCount = $material->likes()->count();
    
    $likes = $material->likes()->with('user:id,name')->get()
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
        'likes' => $likes,
        'is_liked' => $material->likes()->where('user_id', auth()->id())->exists()
    ]);
}

public function removeFromGroup(Request $request)
{
    try {
        $request->validate([
            'group_name' => 'required|string',
            'material_ids' => 'required|array',
            'material_ids.*' => 'exists:marketing_materials,id'
        ]);

        $group = MarketingMaterialGroup::where('name', $request->group_name)->firstOrFail();
        $group->files()->detach($request->material_ids);

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

public function bulkDelete(Request $request)
{
    DB::beginTransaction();
    try {
        MarketingMaterial::whereIn('id', $request->material_ids)->each(function ($material) {
            $this->cleanupMaterialFiles($material);
            $material->industries()->detach();
            $material->dealers()->detach();
            $material->manufacturers()->detach();
            $material->tags()->detach();
            $material->delete();
        });

        DB::commit();
        return response()->json(['message' => 'Marketing materials deleted successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error deleting marketing materials'], 500);
    }
}
public function show(MarketingMaterial $material)
{
    $material->load(['groups', 'tags', 'industries', 'dealers', 'manufacturers']);
    return response()->json([
        'data' => array_merge($material->toArray(), [
            'likes_count' => $material->likes()->count(),
            'views_count' => $material->views()->count(),
            'is_liked_by_user' => $material->likes()->where('user_id', auth()->id())->exists()
        ])
    ]);
}

public function updateGroup(Request $request, MarketingMaterialGroup $group)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $group->update($validated);

        return response()->json([
            'message' => 'Group updated successfully',
            'data' => $group
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating group',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
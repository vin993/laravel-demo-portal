<?php
namespace App\Http\Controllers;

use App\Models\MarketingMaterial;
use Illuminate\Http\Request;
use App\Models\MediaFile;
use App\Models\Industry;
use App\Models\Dealer;
use App\Models\Manufacturer;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Announcement;
use App\Models\MediaLike;
use App\Models\MediaComment;
use App\Models\MediaView;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use DB;

class CustomerController extends Controller
{
	public function dashboard()
	{
		$user = auth()->user();
		$userIndustryIds = $user->industries->pluck('id');
		$userDealerIds = $user->dealers->pluck('id');
		$userManufacturerIds = $user->manufacturers->pluck('id');

		$announcements = Announcement::where('status', true)
			->where(function ($query) use ($userIndustryIds, $userDealerIds, $userManufacturerIds) {

				$query->where(function ($q) {
					$q->whereDoesntHave('industries')
						->whereDoesntHave('dealers')
						->whereDoesntHave('manufacturers');
				});


				$query->orWhere(function ($q) use ($userIndustryIds, $userDealerIds, $userManufacturerIds) {

					if ($userDealerIds->isNotEmpty()) {
						$q->whereHas('dealers', function ($subQ) use ($userDealerIds) {
							$subQ->whereIn('dealers.id', $userDealerIds);
						});


						if ($userIndustryIds->isNotEmpty() || $userManufacturerIds->isNotEmpty()) {
							$q->where(function ($innerQ) use ($userIndustryIds, $userManufacturerIds) {

								if ($userIndustryIds->isNotEmpty()) {
									$innerQ->orWhereHas('industries', function ($indQ) use ($userIndustryIds) {
										$indQ->whereIn('industries.id', $userIndustryIds);
									});
								}


								if ($userManufacturerIds->isNotEmpty()) {
									$innerQ->orWhereHas('manufacturers', function ($manQ) use ($userManufacturerIds) {
										$manQ->whereIn('manufacturers.id', $userManufacturerIds);
									});
								}


								$innerQ->orWhere(function ($noIndManQ) {
									$noIndManQ->whereDoesntHave('industries')
										->whereDoesntHave('manufacturers');
								});
							});
						}
					} else if ($userIndustryIds->isNotEmpty() || $userManufacturerIds->isNotEmpty()) {
						$q->where(function ($altQ) use ($userIndustryIds, $userManufacturerIds) {

							if ($userIndustryIds->isNotEmpty()) {
								$altQ->orWhereHas('industries', function ($indQ) use ($userIndustryIds) {
									$indQ->whereIn('industries.id', $userIndustryIds);
								});
							}


							if ($userManufacturerIds->isNotEmpty()) {
								$altQ->orWhereHas('manufacturers', function ($manQ) use ($userManufacturerIds) {
									$manQ->whereIn('manufacturers.id', $userManufacturerIds);
								});
							}
						})
							->whereDoesntHave('dealers');
					}
				});
			})
			->orderBy('created_at', 'desc')
			->get();

		return view('customer.dashboard', compact('user', 'announcements'));
	}

	public function edit()
	{
		$user = auth()->user();
		$industries = Industry::all();
		$dealers = Dealer::all();
		$manufacturers = Manufacturer::all();

		$currentCountry = null;
		$currentState = null;

		if ($user->userDetail->country) {
			$currentCountry = Country::find($user->userDetail->country);
		}
		if ($user->userDetail->state) {
			$currentState = State::find($user->userDetail->state);
		}

		return view('customer.profile.edit', compact(
			'user',
			'industries',
			'dealers',
			'manufacturers',
			'currentCountry',
			'currentState',
		));
	}

	public function update(Request $request)
	{
		$user = auth()->user();

		try {
			$validatedData = $request->validate([
				'name' => 'required|string|max:255',
				'phone' => 'required|string|max:20',
				'address' => 'required|string|max:500',
				'country' => 'nullable|exists:countries,id',
				'state' => 'nullable|exists:states,id',
				'postal_code' => 'nullable|string|max:20',
				// 'industry_interests' => 'nullable|array|min:1',
				// 'dealer_id' => 'nullable|array|min:1',
				// 'manufacturer_id' => 'nullable|array|min:1',
			], [
				// Custom error messages
				'name.required' => 'Name is required',
				'phone.required' => 'Phone number is required',
				'address.required' => 'Address is required',
				// 'industry_interests.min' => 'Please select at least one industry',
				// 'dealer_id.min' => 'Please select at least one dealer',
				// 'manufacturer_id.min' => 'Please select at least one manufacturer',
			]);

			$user->update(['name' => $request->name]);
			$userDetail = UserDetail::where('user_id', $user->id)->first();

			if ($userDetail) {
				$userDetail->update([
					// 'company_name' => $request->company_name,
					'phone' => $request->phone,
					'address' => $request->address,
					'city' => $request->city,
					'state' => $request->state,
					'country' => $request->country,
					'postal_code' => $request->postal_code,
				]);
			}

			// Sync relationships
			// if ($request->has('industry_interests')) {
			// 	$user->industries()->sync($request->industry_interests);
			// }
			// if ($request->has('dealer_id')) {
			// 	$user->dealers()->sync($request->dealer_id);
			// }
			// if ($request->has('manufacturer_id')) {
			// 	$user->manufacturers()->sync($request->manufacturer_id);
			// }

			return redirect()->route('profile.edit')->with('success', 'Profile updated successfully');
		} catch (\ValidationException $e) {
			return redirect()->route('profile.edit')
				->withErrors($e->validator)
				->withInput();
		} catch (\Exception $e) {
			\Log::error('Profile update failed', [
				'user_id' => $user->id,
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			]);
			return redirect()->route('profile.edit')
				->with('error', 'An error occurred while updating the profile. Please try again.')
				->withInput();
		}
	}
	public function medias()
	{
		$user = auth()->user();
		$userIndustryIds = $user->industries->pluck('id');
		$userDealerIds = $user->dealers->pluck('id');
		$userManufacturerIds = $user->manufacturers->pluck('id');

		$query = MediaFile::with(['group', 'tags']);


		if ($userDealerIds->isNotEmpty()) {
			$query->whereHas('dealers', function ($q) use ($userDealerIds) {
				$q->whereIn('dealers.id', $userDealerIds);
			});
		}


		if ($userIndustryIds->isNotEmpty() || $userManufacturerIds->isNotEmpty()) {
			$query->where(function ($q) use ($userIndustryIds, $userManufacturerIds) {

				if ($userIndustryIds->isNotEmpty()) {
					$q->orWhereHas('industries', function ($subq) use ($userIndustryIds) {
						$subq->whereIn('industries.id', $userIndustryIds);
					});
				}


				if ($userManufacturerIds->isNotEmpty()) {
					$q->orWhereHas('manufacturers', function ($subq) use ($userManufacturerIds) {
						$subq->whereIn('manufacturers.id', $userManufacturerIds);
					});
				}
			});
		}

		$mediaFiles = $query->latest()->get();

		return view('customer.media.index', compact('mediaFiles'));
	}


	public function getMediaEngagement(MediaFile $media)
	{
		$user = auth()->user();
		return response()->json([
			'likes_count' => $media->likes()->count(),
			'views_count' => $media->uniqueViewCount(),
			'comments' => $media->comments()->with('user:id,name')->latest()->get(),
			'is_liked' => $media->isLikedByUser($user->id)
		]);
	}

	public function toggleLike(MediaFile $media)
	{
		try {
			$user = auth()->user();
			$exists = DB::table('media_likes')->where('media_file_id', $media->id)->where('user_id', $user->id)->exists();
			if ($exists) {
				DB::table('media_likes')->where('media_file_id', $media->id)->where('user_id', $user->id)->delete();
				$action = 'unliked';
			} else {
				DB::table('media_likes')->insert([
					'media_file_id' => $media->id,
					'user_id' => $user->id,
					'created_at' => now(),
					'updated_at' => now()
				]);
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

	public function addComment(Request $request, MediaFile $media)
	{
		$request->validate([
			'comment' => 'required|string|max:1000'
		]);
		$comment = $media->comments()->create([
			'user_id' => auth()->id(),
			'comment' => $request->comment
		]);
		return response()->json([
			'status' => 'success',
			'comment' => $comment->load('user:id,name')
		]);
	}

	public function recordView(MediaFile $media)
	{
		$user = auth()->user();
		$recentView = $media->views()->where('user_id', $user->id)->where('created_at', '>', now()->subDay())->exists();
		if (!$recentView) {
			$media->views()->create([
				'user_id' => $user->id,
				'ip_address' => request()->ip()
			]);
		}
		return response()->json([
			'status' => 'success',
			'views_count' => $media->uniqueViewCount()
		]);
	}

	public function managedUsers()
	{
		if (!auth()->user()->is_primary) {
			abort(403, 'Only primary users can manage secondary users');
		}

		$primaryUser = auth()->user();
		$managedUsers = User::where('invited_by', $primaryUser->id)
			->with([
				'userDetail',
				'industries',
				'dealers',
				'manufacturers',
			])
			->get();

		return view('customer.managed-users', compact('managedUsers'));
	}
	// public function marketingMaterials()
	// {
	// 	$user = auth()->user();
	// 	$userIndustryIds = $user->industries->pluck('id');
	// 	$userDealerIds = $user->dealers->pluck('id');
	// 	$userManufacturerIds = $user->manufacturers->pluck('id');

	// 	$marketingMaterials = MarketingMaterial::with(['groups', 'tags'])
	// 		->whereDoesntHave('industries', function ($q) use ($userIndustryIds) {
	// 			$q->whereNotIn('industries.id', $userIndustryIds);
	// 		})
	// 		->whereHas('industries', function ($q) use ($userIndustryIds) {
	// 			$q->whereIn('industries.id', $userIndustryIds);
	// 		}, '=', count($userIndustryIds))

	// 		->whereHas('dealers', function ($q) use ($userDealerIds) {
	// 			$q->whereIn('dealers.id', $userDealerIds);
	// 		})

	// 		->whereDoesntHave('manufacturers', function ($q) use ($userManufacturerIds) {
	// 			$q->whereNotIn('manufacturers.id', $userManufacturerIds);
	// 		})
	// 		->whereHas('manufacturers', function ($q) use ($userManufacturerIds) {
	// 			$q->whereIn('manufacturers.id', $userManufacturerIds);
	// 		}, '=', count($userManufacturerIds))

	// 		->latest()->get();

	// 	return view('customer.marketing-materials.index', compact('marketingMaterials'));
	// }

	public function marketingMaterials()
	{
		$user = auth()->user();
		$userIndustryIds = $user->industries->pluck('id');
		$userDealerIds = $user->dealers->pluck('id');
		$userManufacturerIds = $user->manufacturers->pluck('id');

		$query = MarketingMaterial::with(['groups', 'tags']);

		// FIRST REQUIREMENT: Material must match at least one user's Dealer Company
		if ($userDealerIds->isNotEmpty()) {
			$query->whereHas('dealers', function ($q) use ($userDealerIds) {
				$q->whereIn('dealers.id', $userDealerIds);
			});
		}

		// SECOND REQUIREMENT: Material must match at least one user's Industry OR one user's Manufacturer
		if ($userIndustryIds->isNotEmpty() || $userManufacturerIds->isNotEmpty()) {
			$query->where(function ($q) use ($userIndustryIds, $userManufacturerIds) {
				if ($userIndustryIds->isNotEmpty()) {
					$q->orWhereHas('industries', function ($subq) use ($userIndustryIds) {
						$subq->whereIn('industries.id', $userIndustryIds);
					});
				}

				if ($userManufacturerIds->isNotEmpty()) {
					$q->orWhereHas('manufacturers', function ($subq) use ($userManufacturerIds) {
						$subq->whereIn('manufacturers.id', $userManufacturerIds);
					});
				}
			});
		}

		$marketingMaterials = $query->latest()->get();

		return view('customer.marketing-materials.index', compact('marketingMaterials'));
	}
	public function getMarketingMaterialEngagement(MarketingMaterial $material)
	{
		$user = auth()->user();
		return response()->json([
			'likes_count' => $material->likes()->count(),
			'views_count' => $material->uniqueViewCount(),
			'comments' => $material->comments()->with('user:id,name')->latest()->get(),
			'is_liked' => $material->isLikedByUser($user->id)
		]);
	}

	public function toggleMarketingMaterialLike(MarketingMaterial $material)
	{
		try {
			$user = auth()->user();
			$exists = DB::table('mm_likes')->where('material_id', $material->id)->where('user_id', $user->id)->exists();
			if ($exists) {
				DB::table('mm_likes')->where('material_id', $material->id)->where('user_id', $user->id)->delete();
				$action = 'unliked';
			} else {
				DB::table('mm_likes')->insert([
					'material_id' => $material->id,
					'user_id' => $user->id,
					'created_at' => now(),
					'updated_at' => now()
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

	public function recordMarketingMaterialView(MarketingMaterial $material)
	{
		$user = auth()->user();
		$recentView = DB::table('mm_views')->where('material_id', $material->id)->where('user_id', $user->id)->where('created_at', '>', now()->subDay())->exists();
		if (!$recentView) {
			DB::table('mm_views')->insert([
				'material_id' => $material->id,
				'user_id' => $user->id,
				'ip_address' => request()->ip(),
				'created_at' => now(),
				'updated_at' => now()
			]);
		}
		return response()->json([
			'status' => 'success',
			'views_count' => $material->uniqueViewCount()
		]);
	}



	public function updateSecondaryUser(Request $request, User $user)
	{
		if ($user->invited_by != auth()->id()) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized action'
			], 403);
		}

		$request->validate([
			'name' => 'required|string|max:255',
			'phone' => 'nullable|string|max:20',
			'address' => 'nullable|string|max:255',
			'city' => 'nullable|string|max:255',
			'state' => 'nullable|string|max:255',
			'postal_code' => 'nullable|string|max:20',
			'country' => 'nullable|string|max:255',
		]);

		try {
			DB::beginTransaction();

			$user->update([
				'name' => $request->name,
			]);

			// Update or create user details
			$user->userDetail()->updateOrCreate(
				['user_id' => $user->id],
				[
					'phone' => $request->phone,
					'address' => $request->address,
					'city' => $request->city,
					'state' => $request->state,
					'postal_code' => $request->postal_code,
					'country' => $request->country,
				]
			);

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => 'User updated successfully'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'Failed to update user'
			], 500);
		}
	}
	public function toggleSecondaryUserStatus(Request $request, User $user)
	{
		if ($user->invited_by != auth()->id()) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized action'
			], 403);
		}

		try {
			$user->update([
				'is_active' => !$user->is_active,
				'is_approved' => !$user->is_approved
			]);

			return response()->json([
				'success' => true,
				'message' => $user->is_approved ? 'User activated successfully' : 'User deactivated successfully',
				'is_active' => $user->is_approved
			]);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Failed to update user status'
			], 500);
		}
	}
	public function destroySecondaryUser(User $user)
	{
		if ($user->invited_by != auth()->id()) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized action'
			], 403);
		}

		try {
			DB::beginTransaction();

			if ($user->userDetail) {
				$user->userDetail->delete();
			}

			$user->industries()->detach();
			$user->dealers()->detach();
			$user->manufacturers()->detach();

			$user->delete();

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => 'User deleted successfully'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'Failed to delete user'
			], 500);
		}
	}


	public function fileRequest(Request $request)
	{
		try {
			$request->validate([
				'file_name' => 'required|string|max:255',
				'description' => 'required|string',
				'files' => 'required|array',
				'files.*' => 'required|file|max:10240',
			]);

			\Log::info('File request received', [
				'user' => auth()->user()->id,
				'file_name' => $request->file_name,
				'has_files' => $request->hasFile('files'),
				'file_count' => $request->hasFile('files') ? count($request->file('files')) : 0
			]);

			$fileData = [
				'file_name' => $request->file_name,
				'description' => $request->description,
				'user' => auth()->user(),
				'has_files' => false,
				'files' => [],
				'file_count' => 0
			];

			if ($request->hasFile('files')) {
				$fileData['has_files'] = true;
				$fileData['file_count'] = count($request->file('files'));

				foreach ($request->file('files') as $file) {
					$path = $file->store('file-requests', 'public');
					$fileData['files'][] = [
						'original_filename' => $file->getClientOriginalName(),
						'file_path' => asset('storage/' . $path),
						'size' => round($file->getSize() / 1024, 2) . ' KB'
					];
				}

				\Log::info('Files uploaded successfully', [
					'count' => count($fileData['files'])
				]);
			}

			// Send notification to admin(s)
			$adminUsers = User::where('role_id', 1)->get();

			\Log::info('Sending email notifications to admins', [
				'admin_count' => $adminUsers->count()
			]);

			foreach ($adminUsers as $admin) {
				try {
					Mail::send('customer.emails.file-request-notification', $fileData, function ($message) use ($admin) {
						$message->to($admin->email);
						$message->subject('New File Request Submitted');
					});

					\Log::info('Email sent successfully', [
						'admin_id' => $admin->id,
						'admin_email' => $admin->email
					]);
				} catch (\Exception $e) {
					\Log::error('Failed to send email to admin', [
						'admin_id' => $admin->id,
						'admin_email' => $admin->email,
						'error' => $e->getMessage()
					]);
				}
			}

			return response()->json(['success' => true]);
		} catch (\Exception $e) {
			\Log::error('File request error', [
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'success' => false,
				'error' => 'An error occurred while processing your request.'
			], 500);
		}
	}

}

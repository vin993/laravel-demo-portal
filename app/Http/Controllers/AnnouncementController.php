<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Industry;
use App\Models\Dealer;
use App\Models\Manufacturer;
use App\Models\User;
use Storage;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller {
	public function index() {
		if (request()->ajax()) {
			$announcements = Announcement::with(['industries', 'dealers', 'manufacturers'])->orderBy('created_at', 'desc')->get();
			return response()->json(['data' => $announcements]);
		}
		$industries = Industry::orderBy('name')->get();
		$dealers = Dealer::orderBy('name')->get();
		$manufacturers = Manufacturer::orderBy('name')->get();
		return view('admin.announcements.index', compact('industries', 'dealers', 'manufacturers'));
	}

	public function store(Request $request) {
		$request->validate([
			'title' => 'required|string|max:255',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'status' => 'boolean',
			'industries' => 'nullable|array',
			'dealers' => 'nullable|array',
			'manufacturers' => 'nullable|array',
		]);
		$data = $request->except(['image', 'industries', 'dealers', 'manufacturers']);
		if ($request->hasFile('image')) {
			$imagePath = $request->file('image')->store('announcements', 'public');
			$data['image_path'] = $imagePath;
		}
		$announcement = Announcement::create($data);
		if (!empty($request->industries)) {
			$announcement->industries()->sync($request->industries);
		}
		if (!empty($request->dealers)) {
			$announcement->dealers()->sync($request->dealers);
		}
		if (!empty($request->manufacturers)) {
			$announcement->manufacturers()->sync($request->manufacturers);
		}
		return response()->json(['success' => true]);
	}

	public function show(Announcement $announcement) {
		$announcement->load(['industries', 'dealers', 'manufacturers']);
		return response()->json($announcement);
	}

	public function update(Request $request, Announcement $announcement) {
		$request->validate([
			'title' => 'required|string|max:255',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'status' => 'boolean',
			'industries' => 'nullable|array',
			'dealers' => 'nullable|array',
			'manufacturers' => 'nullable|array',
		]);
		$data = $request->except(['image', 'industries', 'dealers', 'manufacturers']);
		if ($request->hasFile('image')) {
			if ($announcement->image_path) {
				Storage::disk('public')->delete($announcement->image_path);
			}
			$imagePath = $request->file('image')->store('announcements', 'public');
			$data['image_path'] = $imagePath;
		}
		$announcement->update($data);
		$announcement->industries()->sync($request->input('industries', []));
		$announcement->dealers()->sync($request->input('dealers', []));
		$announcement->manufacturers()->sync($request->input('manufacturers', []));
		return response()->json(['success' => true]);
	}

	public function destroy(Announcement $announcement) {
		if ($announcement->image_path) {
			Storage::disk('public')->delete($announcement->image_path);
		}
		$announcement->delete();
		return response()->json(['success' => true]);
	}

	// public function getEligibleUsers(Request $request) {
	// 	$industries = $request->input('industries', []);
	// 	$dealers = $request->input('dealers', []);
	// 	$manufacturers = $request->input('manufacturers', []);
	// 	$users = User::where(function ($query) use ($industries, $dealers, $manufacturers) {
	// 		if (!empty($industries)) {
	// 			$query->whereHas('industries', function ($q) use ($industries) {
	// 				$q->whereIn('industries.id', $industries);
	// 			});
	// 		}
	// 		if (!empty($dealers)) {
	// 			$query->orWhereHas('dealers', function ($q) use ($dealers) {
	// 				$q->whereIn('dealers.id', $dealers);
	// 			});
	// 		}
	// 		if (!empty($manufacturers)) {
	// 			$query->orWhereHas('manufacturers', function ($q) use ($manufacturers) {
	// 				$q->whereIn('manufacturers.id', $manufacturers);
	// 			});
	// 		}
	// 	})->with([
	// 		'industries' => function ($q) use ($industries) {
	// 			$q->whereIn('industries.id', $industries);
	// 		},
	// 		'dealers' => function ($q) use ($dealers) {
	// 			$q->whereIn('dealers.id', $dealers);
	// 		},
	// 		'manufacturers' => function ($q) use ($manufacturers) {
	// 			$q->whereIn('manufacturers.id', $manufacturers);
	// 		}
	// 	])->get()
	// 	->map(function ($user) {
	// 		return [
	// 			'name' => $user->name,
	// 			'email' => $user->email,
	// 			'is_primary' => $user->is_primary,
	// 			'matched_industries' => $user->industries->pluck('name')->toArray(),
	// 			'matched_dealers' => $user->dealers->pluck('name')->toArray(),
	// 			'matched_manufacturers' => $user->manufacturers->pluck('name')->toArray()
	// 		];
	// 	});
	// 	return response()->json(['users' => $users]);
	// }

	public function getEligibleUsers(Request $request) {
		$industries = $request->input('industries', []);
		$dealers = $request->input('dealers', []);
		$manufacturers = $request->input('manufacturers', []);
		
		$query = User::query();
		
		// FIRST REQUIREMENT: User must match at least one Dealer Company if provided
		if (!empty($dealers)) {
			$query->whereHas('dealers', function ($q) use ($dealers) {
				$q->whereIn('dealers.id', $dealers);
			});
		}
		
		// SECOND REQUIREMENT: User must match at least one Industry OR one Manufacturer if either is provided
		if (!empty($industries) || !empty($manufacturers)) {
			$query->where(function($q) use ($industries, $manufacturers) {
				
				if (!empty($industries)) {
					$q->orWhereHas('industries', function ($subq) use ($industries) {
						$subq->whereIn('industries.id', $industries);
					});
				}
				
		
				if (!empty($manufacturers)) {
					$q->orWhereHas('manufacturers', function ($subq) use ($manufacturers) {
						$subq->whereIn('manufacturers.id', $manufacturers);
					});
				}
			});
		}
		
		$users = $query->with([
			'industries' => function ($q) use ($industries) {
				if (!empty($industries)) {
					$q->whereIn('industries.id', $industries);
				}
			},
			'dealers' => function ($q) use ($dealers) {
				if (!empty($dealers)) {
					$q->whereIn('dealers.id', $dealers);
				}
			},
			'manufacturers' => function ($q) use ($manufacturers) {
				if (!empty($manufacturers)) {
					$q->whereIn('manufacturers.id', $manufacturers);
				}
			}
		])->get()
		->map(function ($user) {
			return [
				'name' => $user->name,
				'email' => $user->email,
				'is_primary' => $user->is_primary,
				'matched_industries' => $user->industries->pluck('name')->toArray(),
				'matched_dealers' => $user->dealers->pluck('name')->toArray(),
				'matched_manufacturers' => $user->manufacturers->pluck('name')->toArray()
			];
		});
		
		return response()->json(['users' => $users]);
	}
	public function reorder(Request $request) {
		$orders = $request->input('orders', []);
		foreach ($orders as $order) {
			Announcement::where('id', $order['id'])->update([
				'created_at' => $order['order']
			]);
		}
		return response()->json(['success' => true]);
	}
}

<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dealer;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Mail;
class AdminController extends Controller
{
	public function index()
	{
		$query = User::with([
			'role',
			'userDetail.city',
			'userDetail.state',
			'userDetail.country',
			'userDetail.company',
			'industries',
			'dealers',
			'manufacturers',
			'approvedBy',
			'secondaryUsers',
			'invitedBy'
		]);

		if (auth()->user()->isSuperAdmin()) {

			$users = $query->whereHas('role', function ($q) {
				$q->where('name', 'User');
			})->latest()->get();


			$adminUsers = User::whereHas('role', function ($q) {
				$q->where('name', 'Admin');
			})->with(['userDetail', 'role'])->get();

			return view('admin.dashboard', compact('users', 'adminUsers'));
		} else {

			$users = $query->whereHas('role', function ($q) {
				$q->where('name', 'User');
			})->latest()->get();

			return view('admin.dashboard', compact('users'));
		}
	}
	public function dashboard()
	{
		$users = User::with([
			'role',
			'userDetail.city',
			'userDetail.state',
			'userDetail.country',
			'userDetail.company',
			'industries',
			'dealers',
			'manufacturers',
			'approvedBy',
			'secondaryUsers',
			'invitedBy'
		])->whereHas('role', function ($q) {
			$q->where('name', 'User');
		})->latest()->get();

		$allDealers = Dealer::all();

		return view('admin.dashboard', compact('users', 'allDealers'));
	}
	public function toggleApproval(Request $request, User $user)
	{
		if ($request->ajax()) {
			$previousState = $user->is_approved;
			$user->is_approved = !$user->is_approved;
			$user->approved_by = $user->is_approved ? auth()->id() : null;
			$user->save();
			if ($user->is_approved && !$previousState) {
				Mail::send('auth.emails.user-approved', ['user' => $user], function ($message) use ($user) {
					$message->to($user->email);
					$message->subject('Your Account Has Been Approved - Demo App');
				});
			} elseif (!$user->is_approved && $previousState) {
				Mail::send('auth.emails.user-rejected', ['user' => $user], function ($message) use ($user) {
					$message->to($user->email);
					$message->subject('Account Status Update - Demo App');
				});
			}
			return response()->json([
				'success' => true,
				'message' => $user->is_approved ? 'User approved successfully' : 'User approval revoked',
				'is_approved' => $user->is_approved
			]);
		}
		return back();
	}


	public function update(Request $request, User $user)
	{
		$request->validate([
			'industry_interests' => 'nullable|array',
			'dealer_id' => 'nullable|array',
			'manufacturer_id' => 'nullable|array',
			'is_primary' => 'nullable|boolean'
		]);
		try {
			DB::beginTransaction();
			$isPrimary = filter_var($request->input('is_primary'), FILTER_VALIDATE_BOOLEAN);
			$oldIsPrimary = $user->is_primary;
			$user->is_primary = $isPrimary;
			if ($isPrimary && !$oldIsPrimary) {
				$user->primary_user_since = now();
				$user->primary_set_by = auth()->id();
			}
			$user->save();
			if ($user->is_primary) {
				$user->industries()->sync($request->input('industry_interests', []));
				$user->dealers()->sync($request->input('dealer_id', []));
				$user->manufacturers()->sync($request->input('manufacturer_id', []));
				$secondaryUsers = User::where('invited_by', $user->id)->get();
				foreach ($secondaryUsers as $secondaryUser) {
					$secondaryUser->industries()->sync($request->input('industry_interests', []));
					$secondaryUser->dealers()->sync($request->input('dealer_id', []));
					$secondaryUser->manufacturers()->sync($request->input('manufacturer_id', []));
					if ($user->userDetail && $secondaryUser->userDetail) {
						$secondaryUser->userDetail->update([
							'company_name' => $user->userDetail->company_name
						]);
					}
				}
				$user->save();
			} else {
				$user->industries()->sync($request->input('industry_interests', []));
				$user->dealers()->sync($request->input('dealer_id', []));
				$user->manufacturers()->sync($request->input('manufacturer_id', []));
				$user->save();
			}
			DB::commit();
			return response()->json([
				'success' => true,
				'message' => 'User interests updated successfully'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'Failed to update user interests'
			], 500);
		}
	}

	public function destroy(User $user)
	{
		try {
			if ($user->userDetail) {
				$user->userDetail->delete();
			}
			$user->industries()->detach();
			$user->dealers()->detach();
			$user->manufacturers()->detach();
			$user->delete();
			return response()->json([
				'success' => true,
				'message' => 'User deleted successfully'
			]);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Failed to delete user'
			], 500);
		}
	}

	public function export(Request $request)
	{
		$fileName = 'Users_Export_' . date('Y-m-d_H-i-s') . '.csv';

		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
			'Pragma' => 'no-cache',
			'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
			'Expires' => '0'
		];


		$tempFile = tempnam(sys_get_temp_dir(), 'csv');
		$handle = fopen($tempFile, 'w');


		fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));


		$csvHeaders = [
			'ID',
			'Full Name',
			'Email Address',
			'Phone Number',
			'Account Type',
			'Registration Date',
			'Account Status',
			'Company Name',
			'Address',
			'City',
			'State/Province',
			'Postal Code',
			'Country',
			'Industry Interests',
			'Dealer Companies',
			'Manufacturers'
		];

		fputcsv($handle, $csvHeaders);


		$query = User::with(['userDetail', 'industries', 'dealers', 'manufacturers', 'invitedBy'])
			->whereHas('role', function ($q) {
				$q->where('name', 'User');
			});


		if ($request->has('status') && !empty($request->status)) {
			$query->where('status', $request->status);
		}

		if ($request->has('location') && !empty($request->location)) {
			$query->whereHas('userDetail', function ($q) use ($request) {
				$q->where('state', $request->location);
			});
		}

		if ($request->has('industry') && !empty($request->industry)) {
			$industryName = $request->industry;
			$query->whereHas('industries', function ($q) use ($industryName) {
				$q->where('name', $industryName);
			});
		}

		$users = $query->get();

		foreach ($users as $user) {

			$industries = $user->industries->pluck('name')->implode('; ');
			$dealers = $user->dealers->pluck('name')->implode('; ');
			$manufacturers = $user->manufacturers->pluck('name')->implode('; ');

			$accountType = $user->is_primary ? 'Primary' : 'Secondary';

			$registrationDate = $user->created_at->format('M d, Y');

			$status = $user->is_approved ? ($user->is_active ? 'Active' : 'Inactive') : 'Pending';


			$row = [
				$user->id,
				$user->name,
				$user->email,
				$user->userDetail->phone ?? 'N/A',
				$accountType,
				$registrationDate,
				$status,
				$user->userDetail->company_name ?? 'N/A',
				$user->userDetail->address ?? 'N/A',
				$user->userDetail->city ?? 'N/A',
				$user->userDetail->state ?? 'N/A',
				$user->userDetail->postal_code ?? 'N/A',
				$user->userDetail->country ?? 'N/A',
				$industries,
				$dealers,
				$manufacturers
			];

			fputcsv($handle, $row);
		}

		fclose($handle);


		return response()->download($tempFile, $fileName, $headers)->deleteFileAfterSend(true);
	}

}


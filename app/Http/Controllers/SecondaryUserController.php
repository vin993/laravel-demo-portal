<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\UserDetail;
use Carbon\Carbon;

class SecondaryUserController extends Controller
{
	public function inviteSecondaryUser(Request $request)
	{
		\Log::info('Invitation request received', ['user_id' => auth()->id(), 'emails' => $request->emails]);

		$request->validate([
			'emails' => 'required|array',
			'emails.*' => 'required|email|unique:users,email'
		], [
			'emails.*.unique' => 'Invitation already sent to this email.'
		]);

		try {
			DB::beginTransaction();
			$invitedBy = auth()->user();
			$successCount = 0;

			foreach ($request->emails as $email) {
				\Log::info('Processing invitation', ['email' => $email]);

				$existingInvitation = UserInvitation::where('email', $email)->whereNull('accepted_at')->first();
				if ($existingInvitation) {
					\Log::warning('Invitation already exists and is pending', ['email' => $email]);
					continue;
				}

				$token = Str::random(64);

				$invitation = UserInvitation::create([
					'email' => $email,
					'token' => $token,
					'invited_by' => auth()->id(),
					'expires_at' => now()->addHours(48)
				]);

				\Log::info('Invitation created', ['email' => $email, 'token' => $token]);

				Mail::send(
					'auth.emails.secondary-invitation',
					[
						'token' => $token,
						'invitedBy' => $invitedBy
					],
					function ($message) use ($email) {
						$message->to($email);
						$message->subject('Secondary User Invitation');
					}
				);

				\Log::info('Email sent', ['email' => $email]);
				$successCount++;
			}

			DB::commit();
			\Log::info('Invitations sent successfully', ['count' => $successCount]);

			return response()->json([
				'message' => $successCount . ' invitation(s) sent successfully'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error('Failed to send invitations', ['error' => $e->getMessage()]);

			return response()->json([
				'message' => 'Failed to send invitations'
			], 500);
		}
	}

	public function showRegistrationForm($token)
	{
		$invitation = UserInvitation::where('token', $token)->where('expires_at', '>', now())->whereNull('accepted_at')->firstOrFail();
		$primaryUser = User::findOrFail($invitation->invited_by);
		return view('auth.register-secondary', [
			'token' => $token,
			'email' => $invitation->email,
			'primaryUser' => $primaryUser
		]);
	}

	public function completeRegistration(Request $request)
	{
		$invitation = UserInvitation::where('token', $request->token)->where('expires_at', '>', now())->whereNull('accepted_at')->firstOrFail();
		$request->validate([
			'name' => 'required|string|max:255',
			'password' => [
				'required',
				'string',
				'min:8',
				'confirmed'
			],
			'email' => [
				'required',
				'string',
				'email',
				'max:255',
				'unique:users',
				'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
			],
			'phone' => 'required|string|max:20',
			'country' => 'required|exists:countries,id',
			'state' => 'nullable|string|max:255',
			'city' => 'nullable|string|max:255',
			'postal_code' => 'required|string|max:10',
			'address' => 'required|string|max:500',
			'user_agreement' => 'required|accepted'
		]);
		try {
			DB::beginTransaction();
			$primaryUser = User::with(['industries', 'dealers', 'manufacturers', 'userDetail'])
				->findOrFail($invitation->invited_by);
			$user = User::create([
				'name' => $request->name,
				'email' => $invitation->email,
				'password' => Hash::make($request->password),
				'role_id' => 3,
				'invited_by' => $invitation->invited_by,
				'is_primary' => false,
				'is_approved' => false,
				'is_active' => true
			]);
			UserDetail::create([
				'user_id' => $user->id,
				'company_name' => $primaryUser->userDetail->company_name,
				'phone' => $request->phone,
				'address' => $request->address,
				'city' => $request->city,
				'state' => $request->state,
				'postal_code' => $request->postal_code,
				'country' => $request->country,
			]);
			$user->industries()->attach($primaryUser->industries->pluck('id'));
			$user->dealers()->attach($primaryUser->dealers->pluck('id'));
			$user->manufacturers()->attach($primaryUser->manufacturers->pluck('id'));
			$invitation->update(['accepted_at' => now()]);
			Mail::send('auth.emails.registration', ['user' => $user], function ($message) use ($user) {
				$message->to($user->email);
				$message->subject('Welcome to Demo App - Registration Successful');
			});
			$adminUsers = User::where('role_id', 1)->get();
			foreach ($adminUsers as $admin) {
				Mail::send(
					'auth.emails.secondary-registration-admin',
					[
						'user' => $user,
						'primaryUser' => User::find($user->invited_by)
					],
					function ($message) use ($admin) {
						$message->to($admin->email);
						$message->subject('New Secondary User Registration Requires Approval');
					}
				);
			}
			DB::commit();
			return redirect()->route('login')->with('success', 'Registration completed successfully. Please wait for admin approval to access your account.');
		} catch (\Exception $e) {
			dd($e->getMessage());
			DB::rollBack();
			return back()->with('error', 'Registration failed. Please try again.');
		}
	}
}


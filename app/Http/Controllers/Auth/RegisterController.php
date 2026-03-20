<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dealer;
use App\Models\UserDetail;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use ReCaptcha\ReCaptcha;

class RegisterController extends Controller {
	protected $recaptcha;

	public function __construct(ReCaptcha $recaptcha) {
		$this->recaptcha = $recaptcha;
	}

	public function showRegistrationForm() {
		$industries = Industry::select('id', 'name')->get();
		return view('auth.register', compact('industries'));
	}

	public function register(Request $request) {
		$validatedData = $request->validate([
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
			'address' => 'required|string|max:500',
			'city' => 'nullable|string|max:255',
			'state' => 'nullable|string|max:255',
			'postal_code' => 'nullable|string|max:20',
			'country' => 'required|string|max:255',
			'industry_interests' => 'required|array|min:1',
			'industry_interests.*' => 'exists:industries,id',
			'dealer_id' => 'required|array|min:1',
			'dealer_id.*' => 'exists:dealers,id',
			'manufacturer_id' => 'required|array|min:1',
			'manufacturer_id.*' => 'exists:manufacturers,id',
			'g-recaptcha-response' => 'required',
			'user_agreement' => 'required|accepted',
			'is_primary' => 'required|accepted',
		]);
		$recaptchaResponse = $this->recaptcha->verify(
			$request->input('g-recaptcha-response'),
			$request->ip()
		);
		if (!$recaptchaResponse->isSuccess()) {
			return back()
				->withInput($request->except('password'))
				->withErrors(['g-recaptcha-response' => 'Please complete the reCAPTCHA verification.']);
		}
		$user = null;
		DB::transaction(function () use ($request) {
			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => Hash::make($request->password),
				'role_id' => 3,
				'is_approved' => false,
				'is_active' => true,
				'is_primary' => true,
				'primary_user_since' => now(),
				'primary_set_by' => null,
				'invited_by' => null
			]);
			UserDetail::create([
				'user_id' => $user->id,
				'phone' => $request->phone,
				'address' => $request->address,
				'city' => $request->city,
				'state' => $request->state,
				'postal_code' => $request->postal_code,
				'country' => $request->country,
			]);
			if ($request->has('industry_interests')) {
				$user->industries()->attach($request->industry_interests);
			}
			if ($request->has('dealer_id')) {
				$user->dealers()->attach($request->dealer_id);
			}
			if ($request->has('dealer_id') && count($request->dealer_id) > 0) {
				$firstDealer = Dealer::find($request->dealer_id[0]);
				if ($firstDealer) {
					$user->userDetail->update([
						'company_name' => $firstDealer->name
					]);
				}
			}
			if ($request->has('manufacturer_id')) {
				$user->manufacturers()->attach($request->manufacturer_id);
			}

			// Send welcome email to user
			Mail::send('auth.emails.registration', ['user' => $user], function ($message) use ($user) {
				$message->to($user->email);
				$message->subject('Welcome to Demo App - Registration Successful');
			});

			// Send notification to admin(s)
			$adminUsers = User::where('role_id', '!=',3)->get();
			foreach ($adminUsers as $admin) {
				Mail::send('auth.emails.admin-new-user-notification', ['user' => $user], function ($message) use ($admin) {
					$message->to($admin->email);
					$message->subject('New User Registration Requires Approval');
				});
			}
		});
		return redirect()->route('login')->with('info', 'Registration completed! Please wait for admin verification before logging in.');
	}
	public function userAgreement() {
		return view('auth.user-agreement');
	}
}


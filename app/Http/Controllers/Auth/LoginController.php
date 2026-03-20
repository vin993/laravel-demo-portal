<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReCaptcha\ReCaptcha;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    protected $recaptcha;
    public function __construct(ReCaptcha $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }
    protected function authenticated(Request $request, $user)
    {
        if (!$user->is_approved) {
            auth()->logout();
            return back()->withErrors([
                'email' => 'Your account is pending admin approval.',
            ]);
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return ($user->isAdmin() || $user->isSuperAdmin())
                ? redirect()->route('admin.dashboard')
                : redirect()->route('customer.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ]);

        $recaptchaResponse = $this->recaptcha->verify(
            $request->input('g-recaptcha-response'),
            $request->ip()
        );

        if (!$recaptchaResponse->isSuccess()) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Please complete the reCAPTCHA verification.'
            ])->withInput($request->except('password'));
        }


        $user = User::where('email', $request->email)->first();

        if ($user) {

            $user->checkInactivity();

            if (!$user->is_active) {
                return view('auth.account-inactive', compact('user'));
            }
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (!$user->is_approved) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending admin approval.',
                ]);
            }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
            }
            $user->update(['last_login_at' => now()]);
            $user->save();
            // Redirect based on role
            return ($user->isAdmin() || $user->isSuperAdmin())
                ? redirect()->route('admin.dashboard')
                : redirect()->route('customer.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }


    public function requestReactivation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'phone' => 'required',
            'message' => 'nullable',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $adminUsers = User::where('role_id', 1)->get();

        $emailData = [
            'user' => $user,
            'phone' => $request->phone,
            'userMessage' => $request->message,
            'primaryUser' => $user->invitedBy
        ];

        foreach ($adminUsers as $admin) {
            Mail::send('auth.emails.reactivation-request', $emailData, function ($message) use ($admin) {
                $message->to($admin->email)
                    ->subject('Account Reactivation Request');
            });
        }

        return back()->with('status', 'Your reactivation request has been sent to our support team. We will review your request and contact you soon.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
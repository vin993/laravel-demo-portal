<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ReCaptcha\ReCaptcha;
class ForgotPasswordController extends Controller
{
    protected $recaptcha;

    public function __construct(ReCaptcha $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        $recaptchaResponse = $this->recaptcha->verify(
            $request->input('g-recaptcha-response'),
            $request->ip()
        );

        if (!$recaptchaResponse->isSuccess()) {
            return back()
                ->withInput()
                ->withErrors(['g-recaptcha-response' => 'Please complete the reCAPTCHA verification.']);
        }

        $token = Str::random(64);
        $email = $request->email;

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('auth.emails.forgot-password', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Reset Password Notification');
        });

        return back()->with('status', 'We have emailed your password reset link!');
    }
}
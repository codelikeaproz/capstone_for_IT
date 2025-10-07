<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LoginAttempt;

class EmailVerificationController extends Controller
{
    /**
     * Verify email address using token
     */
    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Invalid verification link. Please request a new verification email.'
            ]);
        }

        // Check if email is already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Email already verified. You can now login.');
        }

        // Mark email as verified
        $user->markEmailAsVerified();

        // Log the verification
        activity()
            ->performedOn($user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Email address verified via verification link');

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now login to your account.');
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email address.'
            ]);
        }

        // Check if email is already verified
        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email is already verified. You can login to your account.');
        }

        // Check rate limiting (prevent spam)
        $recentAttempts = LoginAttempt::where('email', $request->email)
            ->where('failure_reason', 'email_verification_resend')
            ->where('attempted_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentAttempts >= 3) {
            return back()->withErrors([
                'email' => 'Too many verification emails sent. Please wait 5 minutes before requesting another.'
            ]);
        }

        try {
            // Send new verification email
            $user->sendEmailVerificationNotification();

            // Log the resend attempt
            LoginAttempt::create([
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'successful' => true,
                'user_agent' => $request->userAgent(),
                'failure_reason' => 'email_verification_resend',
                'attempted_at' => now(),
            ]);

            // Log activity
            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('Email verification resent');

            return back()->with('success', 'Verification email sent! Please check your inbox and click the verification link.');

        } catch (\Exception $e) {
            // Log the error
            activity()
                ->performedOn($user)
                ->withProperties([
                    'error' => $e->getMessage(),
                    'ip_address' => $request->ip()
                ])
                ->log('Failed to send email verification');

            return back()->withErrors([
                'email' => 'Failed to send verification email. Please try again later.'
            ]);
        }
    }

    /**
     * Show resend verification form
     */
    public function showResendForm()
    {
        return view('Auth.ResendVerification');
    }
}
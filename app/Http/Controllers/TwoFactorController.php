<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Log;

class TwoFactorController extends Controller
{
    /**
     * Show 2FA verification form
     */
    public function showVerifyForm()
    {
        // Check if user is in 2FA session
        if (!Session::has('2fa_user_id')) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please login again.']);
        }

        // Check session timeout (30 minutes)
        $loginTime = Session::get('2fa_login_time');
        if ($loginTime && now()->diffInMinutes($loginTime) > 30) {
            Session::forget(['2fa_user_id', '2fa_remember', '2fa_login_time']);
            return redirect()->route('login')->withErrors(['email' => '2FA session expired. Please login again.']);
        }

        $userId = Session::get('2fa_user_id');
        $user = User::find($userId);

        if (!$user) {
            Session::forget(['2fa_user_id', '2fa_remember', '2fa_login_time']);
            return redirect()->route('login')->withErrors(['email' => 'Invalid session. Please login again.']);
        }

        // Check if 2FA code exists and is valid
        if (!$user->two_factor_code || !$user->two_factor_expires_at || $user->two_factor_expires_at->isPast()) {
            // Generate new code if expired or missing
            $user->generateTwoFactorCode();
            // Refresh user data to get updated expiration time
            $user->refresh();
        }

        // Get current expiration time for display
        $expiresAt = $user->two_factor_expires_at;

        // Always calculate remaining time for countdown (always show 5 minutes for new/valid codes)
        if ($expiresAt && $expiresAt->isFuture()) {
            $remainingSecondsTotal = $expiresAt->diffInSeconds(now());
            // Ensure we never show negative time
            $remainingSecondsTotal = max(0, $remainingSecondsTotal);
        } else {
            // Default to 5 minutes if there's any issue
            $remainingSecondsTotal = 300; // 5 minutes in seconds
        }

        $remainingMinutes = floor($remainingSecondsTotal / 60);
        $remainingSecondsOnly = $remainingSecondsTotal % 60;
        $userEmail = $user->email;

        return view('Auth.TwoFactor', compact('userEmail', 'remainingMinutes', 'remainingSecondsOnly', 'expiresAt'));
    }

    /**
     * Verify 2FA code and complete login
     */
    public function verify(Request $request)
    {
        $request->validate([
            // Accept digits with optional spaces/hyphens; normalize later
            'code' => 'required|string',
        ]);

        // Check if user is in 2FA session
        if (!Session::has('2fa_user_id')) {
            Log::warning('2FA verification failed: No session ID');
            return back()->withErrors(['code' => 'Session expired. Please login again.']);
        }

        $userId = Session::get('2fa_user_id');
        Log::info('Verifying 2FA code for user: ' . $userId);

        // Ensure we have the freshest values for two_factor_code and expiration
        $user = User::query()->find($userId);
        if ($user) {
            $user->refresh();
        }

        if (!$user) {
            Log::warning('2FA verification failed: User not found - ' . $userId);
            Session::forget(['2fa_user_id', '2fa_remember', '2fa_login_time']);
            return redirect()->route('login')->withErrors(['email' => 'Invalid session. Please login again.']);
        }

        // Normalize input (digits only) before verifying
        $submittedCode = preg_replace('/[^0-9]/', '', (string) $request->code);
        Log::info('2FA submitted code (normalized): ' . $submittedCode);
        Log::info('2FA stored code: ' . $user->two_factor_code);
        Log::info('2FA code expires at: ' . $user->two_factor_expires_at);
        Log::info('2FA current time: ' . now());

        // If code missing or expired, handle before calling validator (prevents losing expired state)
        if (!$user->two_factor_code || !$user->two_factor_expires_at) {
            $failureReason = !$user->two_factor_code && !$user->two_factor_expires_at ? 'missing_2fa_code' : 'missing_2fa_fields';

            LoginAttempt::create([
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'successful' => false,
                'user_agent' => $request->userAgent(),
                'failure_reason' => $failureReason,
                'attempted_at' => now(),
            ]);

            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'failure_reason' => $failureReason
                ])
                ->log('Failed 2FA verification attempt');

            return back()->withErrors(['code' => 'Verification code not available. Please request a new code.'])->withInput();
        }

        if ($user->two_factor_expires_at->isPast()) {
            LoginAttempt::create([
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'successful' => false,
                'user_agent' => $request->userAgent(),
                'failure_reason' => 'expired_2fa_code',
                'attempted_at' => now(),
            ]);

            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'failure_reason' => 'expired_2fa_code'
                ])
                ->log('Failed 2FA verification attempt');

            return back()->withErrors(['code' => 'Verification code has expired. Please request a new code.'])->withInput();
        }

        if (!$user->isTwoFactorCodeValid($submittedCode)) {
            // Check if code is expired specifically
            $errorMessage = 'Invalid verification code.';
            $failureReason = 'invalid_2fa_code';

            // At this point, code is present and not expired. Treat as invalid only.

            // Log failed 2FA attempt
            LoginAttempt::create([
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'successful' => false,
                'user_agent' => $request->userAgent(),
                'failure_reason' => $failureReason,
                'attempted_at' => now(),
            ]);

            // Log activity
            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'failure_reason' => $failureReason
                ])
                ->log('Failed 2FA verification attempt');

            return back()->withErrors(['code' => $errorMessage])->withInput();
        }

        // Complete login using AuthController method
        $authController = new \App\Http\Controllers\AuthController();

        // Clear 2FA code
        $user->clearTwoFactorCode();

        return $authController->complete2FALogin($request, $userId);
    }

    /**
     * Resend 2FA code
     */
    public function resendCode(Request $request)
    {
        // Check if user is in 2FA session
        if (!Session::has('2fa_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'redirect' => route('login')
            ], 400);
        }

        $userId = Session::get('2fa_user_id');
        $user = User::find($userId);

        if (!$user) {
            Session::forget(['2fa_user_id', '2fa_remember', '2fa_login_time']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid session. Please login again.',
                'redirect' => route('login')
            ], 400);
        }

        // Generate new 2FA code
        try {
            $user->generateTwoFactorCode();

            // Log code resend
            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('2FA code resent');

            // Calculate remaining time for countdown
            $expiresAt = $user->fresh()->two_factor_expires_at; // Get fresh data
            if ($expiresAt && $expiresAt->isFuture()) {
                $remainingSecondsTotal = $expiresAt->diffInSeconds(now());
                // Ensure we never send negative time
                $remainingSecondsTotal = max(0, $remainingSecondsTotal);
                $remainingMinutes = floor($remainingSecondsTotal / 60);
                $remainingSeconds = $remainingSecondsTotal % 60;
            } else {
                // Default to 5 minutes for new code if there's any issue
                $remainingMinutes = 5;
                $remainingSeconds = 0;
            }

            return response()->json([
                'success' => true,
                'message' => 'New verification code sent to your email.',
                'expires_at' => $expiresAt->toISOString(),
                'remaining_minutes' => $remainingMinutes,
                'remaining_seconds' => $remainingSeconds
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again later.'
            ], 500);
        }
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'responder' => redirect()->route('responder.dashboard'),
            'citizen' => redirect()->route('incidents.index'),
            default => redirect()->route('incidents.index')
        };
    }
}

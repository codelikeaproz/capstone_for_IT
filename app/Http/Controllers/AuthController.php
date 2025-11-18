<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\LoginAttempt;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('Auth.Login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->only('username'));
        }

        $username = $request->username;
        $password = $request->password;
        $remember = $request->has('remember');
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Determine if input is email or username
        $loginField = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Check for user and account lockout
        $user = User::where($loginField, $username)->first();

        if ($user && $user->isAccountLocked()) {
            $this->logLoginAttempt($username, $ipAddress, $userAgent, false, 'account_locked');
            return back()->withErrors([
                'username' => 'Account is temporarily locked due to too many failed attempts. Please try again later.',
            ])->withInput($request->only('username'));
        }

        // Attempt authentication
        $credentials = [$loginField => $username, 'password' => $password, 'is_active' => true];

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Skip email verification in local environment
            if (!$user->hasVerifiedEmail() && app()->environment('local')) {
                // In development, we'll mark the email as verified for testing
                $user->markEmailAsVerified();
            }

            // Check if email is verified (required in production)
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                $this->logLoginAttempt($username, $ipAddress, $userAgent, false, 'email_not_verified');
                return back()->withErrors([
                    'username' => 'Please verify your email address before logging in. Check your inbox for verification link.',
                ])->withInput($request->only('username'));
            }

            // Reset failed login attempts on successful authentication
            $user->resetFailedLogins();

            // Skip 2FA in local development environment
            if (app()->environment('local')) {
                // Direct login without 2FA in development
                $user->updateLastLogin();
                $this->logLoginAttempt($username, $ipAddress, $userAgent, true, 'completed_login_dev');
                activity('login')
                    ->performedOn($user)
                    ->withProperties(['ip_address' => $ipAddress, 'user_agent' => $userAgent, 'step' => 'completed_login_dev'])
                    ->log('User completed login directly (dev environment - 2FA bypassed)');

                return $this->redirectBasedOnRole($user);
            }

            // Generate and send 2FA code (for production)
            $user->generateTwoFactorCode();

            // Store user ID in session for 2FA verification
            $request->session()->put('2fa_user_id', $user->id);
            $request->session()->put('2fa_remember', $remember);
            $request->session()->put('2fa_login_time', now());

            // Log the initial authentication (not complete login yet)
            $this->logLoginAttempt($username, $ipAddress, $userAgent, true, 'pending_2fa');

            // Log activity
            activity('login')
                ->performedOn($user)
                ->withProperties(['ip_address' => $ipAddress, 'user_agent' => $userAgent, 'step' => 'initial_auth'])
                ->log('User passed initial authentication, pending 2FA');

            // Logout user temporarily until 2FA is verified
            Auth::logout();

            return redirect()->route('2fa.verify')
                ->with('success', '2FA code sent to your email. Please check your inbox.');
        } else {
            // Increment failed login attempts if user exists
            if ($user) {
                $user->incrementFailedLogins();
            }

            // Log failed login
            $this->logLoginAttempt($username, $ipAddress, $userAgent, false, 'invalid_credentials');

            return back()->withErrors([
                'username' => 'Invalid credentials or account is disabled.',
            ])->withInput($request->only('username'));
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log activity
        if ($user) {
            activity('login')
                ->withProperties(['ip_address' => $request->ip()])
                ->log('User logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showRegister()
    {
        // Only admin can register new users
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can register new users.');
        }

        return view('Auth.Register');
    }

    public function register(Request $request)
    {
        // Only admin can register new users
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can register new users.');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,staff,responder,citizen',
            'municipality' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'municipality' => $request->municipality,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'is_active' => true,
        ]);

        // Log activity
        activity()
            ->performedOn($user)
            ->withProperties(['role' => $request->role, 'municipality' => $request->municipality])
            ->log('New user registered');

        return redirect()->route('login')
                        ->with('success', 'User registered successfully. They can now login with their credentials.');
    }

    public function forgotPassword()
    {
        return view('Auth.ForgotPassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('success', 'Password reset link sent to your email.')
                    : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('Auth.ResetPassword', ['token' => $request->token, 'email' => $request->email]);
        }

        return $this->updatePassword($request);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', 'Password reset successfully!')
                    : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Complete login after successful 2FA verification
     */
    public function complete2FALogin(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $remember = $request->session()->get('2fa_remember', false);
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Log the user in
        Auth::login($user, $remember);

        // Update last login timestamp
        $user->updateLastLogin();

        // Clear 2FA session data
        $request->session()->forget(['2fa_user_id', '2fa_remember', '2fa_login_time']);

        // Log successful complete login
        $this->logLoginAttempt($user->email, $ipAddress, $userAgent, true, 'completed_with_2fa');

        // Log activity
        activity('login')
            ->performedOn($user)
            ->withProperties(['ip_address' => $ipAddress, 'user_agent' => $userAgent, 'step' => 'completed_login'])
            ->log('User completed login with 2FA verification');

        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    // Helper Methods
    private function redirectBasedOnRole($user = null)
    {
        $user = $user ?: Auth::user();

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'responder' => redirect()->route('responder.dashboard'),
            'citizen' => redirect()->route('incidents.index'),
            default => redirect()->route('incidents.index')
        };
    }

    private function logLoginAttempt($email, $ipAddress, $userAgent, $successful, $failureReason = null)
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'successful' => $successful,
            'user_agent' => $userAgent,
            'failure_reason' => $failureReason,
            'attempted_at' => now(),
        ]);
    }
}

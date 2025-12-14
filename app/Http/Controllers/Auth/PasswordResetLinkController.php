<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // If the email belongs to a user, create a password reset token and
        // redirect immediately to the reset form (convenience for admin/local use).
        // This still uses the password broker token so the reset process is normal.
        $user = \App\Models\User::where('email', $request->email)->first();

        // Only allow immediate redirect to reset form in non-production (local) environments.
        if ($user && app()->environment('local')) {
            $token = Password::broker()->createToken($user);
            // redirect to the reset form with token and email in querystring
            $url = route('password.reset', $token) . '?email=' . urlencode($request->email);
            return redirect()->to($url);
        }

        // Fallback: behave like default and attempt to send reset link (keeps compatibility)
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}

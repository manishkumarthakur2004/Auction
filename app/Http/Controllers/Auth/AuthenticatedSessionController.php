<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request)
    {
        return response()
            ->view('auth.login', [
                'title'         => 'User Login',
                'action'        => route('user.login.store'),
                'registerRoute' => route('user.register'),
                'heading'       => 'User Login',
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function adminCreate(Request $request)
    {
        return response()
            ->view('auth.login', [
                'title'         => 'Admin Login',
                'action'        => route('admin.login.store'),
                'registerRoute' => null,
                'heading'       => 'Admin Login',
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        return $this->attemptLogin($request, false);
    }

    public function adminStore(Request $request)
    {
        return $this->attemptLogin($request, true);
    }

    private function attemptLogin(Request $request, bool $adminOnly)
    {
        $guard = $adminOnly ? 'admin' : 'web';
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard($guard)->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::guard($guard)->user();

        if ($adminOnly && ! $user->isAdmin()) {
            Auth::guard('admin')->logout();

            return back()->withErrors(['email' => 'This login is only for administrators.'])->onlyInput('email');
        }

        if (! $adminOnly && $user->isAdmin()) {
            Auth::guard('web')->logout();

            return redirect()->route('admin.login')->withErrors(['email' => 'Administrators must use the admin login page.']);
        }

        $dashboardRoute = $adminOnly ? route('admin.dashboard') : route('dashboard');

        return redirect($dashboardRoute);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')->with('success', 'You have been logged out.');
    }

    public function adminDestroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Admin logged out.');
    }
}

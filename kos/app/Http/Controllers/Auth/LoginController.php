<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.auth.signin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Redirect based on role
            if ($user->role === 'super_admin') {
                return redirect()->intended(route('super-admin.dashboard'));
            }

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->role === 'owner') {
                return redirect()->intended(route('owner.dashboard'));
            }

            if ($user->role === 'tenant') {
                return redirect()->intended(route('tenant.dashboard'));
            }

            if ($user->role === 'staff') {
                return redirect()->intended(route('staff.dashboard'));
            }

            if ($user->role === 'manager') {
                return redirect()->intended(route('manager.dashboard'));
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

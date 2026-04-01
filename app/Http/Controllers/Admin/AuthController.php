<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the Login Form UI
     */
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle the Login submission
     */
    public function login(Request $request)
    {
        // 1. Validate the input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt to login
        if (Auth::attempt($credentials)) {
            // Regenerate session for security
            $request->session()->regenerate();

            // 3. CREATE SESSION & SAVE DATA
            $user = Auth::user();
            
            session([
                'admin_user_name' => $user->name,
                'admin_user_email' => $user->email,
                'last_login_at'   => now(),
            ]);

            // 4. SUCCESS: Redirect to Dashboard
            return redirect()->route('admin.index');
        }

        // 5. If login fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the Registration Form
     */
    public function showRegister()
    {
        return view('admin.auth.register');
    }

    /**
     * Handle Registration
     */
    public function register(Request $request)
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Create the User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Log the user in
        Auth::login($user);

        // 4. SAVE TO SESSION (So the navbar shows the name immediately)
        session([
            'admin_user_name' => $user->name,
            'admin_user_email' => $user->email,
            'last_login_at'   => now(),
        ]);

        // 5. Redirect to the dashboard
        return redirect()->route('admin.index');
    }

    /**
     * Handle Logout and Redirect to the First Page
     */
    public function logout(Request $request)
    {
        // 1. Log out the user
        Auth::logout();

        // 2. Invalidate and clear the session
        $request->session()->invalidate();

        // 3. Regenerate CSRF token
        $request->session()->regenerateToken();
        
        // 4. REDIRECT to the first Welcome page (admin.welcome)
        return redirect()->route('admin.welcome')->with('status', 'Logged out successfully');
    }
}
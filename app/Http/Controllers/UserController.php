<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organisation; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // CRITICAL for login

class UserController extends Controller
{

    public function index()
    {
        return view('home');
    }
    // ... your register and store methods ...

public function store(Request $request)
    {
        // 1. Validate the incoming data
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'organisation_id' => 'required|exists:organisations,id',
            'password'        => 'required|min:6',
        ]);

        // 2. Create the user
        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'organisation_id' => $validated['organisation_id'],
            'password'        => Hash::make($validated['password']), // Encrypt the password!
        ]);

        // 3. Redirect with a success message
        return redirect('/login')->with('success', 'Account created successfully, Ramji!');
    }
    /**
     * Show the Login Form
     */
    public function loginForm()
    {
        return view('login'); // Create this blade file next
    }

    /**
     * Process the Login Request
     */
    public function login(Request $request)
    {
        // 1. Validate input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt to log the user in
        // attempt() automatically hashes the 'password' and compares it
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Security: prevent session fixation

            return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
        }

        // 3. If login fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
    public function register()
    {
        // 2. Fetch all organisations from the database
        $organisations = Organisation::all();

        // 3. Return the view and pass the data
        // Change 'auth.register' to the actual name of your blade file
        return view('register', compact('organisations'));
    }
    public function votepage()
    {
        return view('vote');
    }
}
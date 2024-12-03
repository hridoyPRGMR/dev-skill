<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login request with separate request validation
    public function login(LoginRequest $request)
    {
        // Credentials are already validated by LoginRequest
        $credentials = $request->validated(); // Get validated data

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Regenerate session to avoid session fixation attacks
            return redirect()->route('inner.page'); // Redirect to inner page after successful login
        }

        // Return with error if login fails
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    // Handle logout request
    public function logout(Request $request)
    {
        Auth::logout(); // Logout the user
        $request->session()->invalidate(); // Invalidate session data
        $request->session()->regenerateToken(); // Regenerate CSRF token for security

        return redirect()->route('login.form'); // Redirect to login form
    }

    // Show the inner page with all users
    public function innerPage()
    {
        $users = User::all(); // Retrieve all users
        return view('inner-page', compact('users')); // Pass users to the view
    }

    // Store a newly created user with separate request handling
    public function store(StoreUserRequest $request)
    {
        // User data is already validated by StoreUserRequest
        $validated = $request->validated(); // Get validated data

        // Create a new user
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Hash the password
        ]);

        // Redirect to inner page with success message
        return redirect()->route('inner.page')->with('success', 'User created successfully.');
    }
}

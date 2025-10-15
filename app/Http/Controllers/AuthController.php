<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('operator.panel'));
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|in:operator',
            'contact_number' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'company_contact' => 'required|string|max:20',
            'company_email' => 'required|email|max:255',
            'fleet_size' => 'required|integer|min:1',
            'routes_served' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('operators', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'contact_number' => $request->contact_number,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_contact' => $request->company_contact,
            'company_email' => $request->company_email,
            'fleet_size' => $request->fleet_size,
            'routes_served' => $request->routes_served,
            'photo_url' => $photoPath,
        ]);

        Auth::login($user);
        return redirect()->route('operator.panel');
    }

    public function apiLogin(Request $request)
    {

        if (!$request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'API endpoint requires JSON requests'
            ], 400);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $user->createToken('driver_token')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
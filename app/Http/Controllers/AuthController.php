<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('operator.panel');
        }
        return view('login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('operator.panel');
        }
        return view('register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->role !== 'bus_operator') {
                Auth::logout();
                return back()->withErrors(['email' => 'Access denied. Bus operators only.'])->withInput();
            }
            
            return redirect()->intended(route('operator.panel'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255', 
            'middle_initial' => 'nullable|string|max:1', 
            'last_name' => 'required|string|max:255', 
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'terminal' => 'required|in:north,south', 
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'company_contact' => 'required|string|max:20',
            'company_email' => 'required|email|max:255',
            'fleet_size' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $photoPath = $file->storeAs('operators', $filename, 'public');
            }

            $middle = $request->middle_initial ? ' ' . $request->middle_initial . '.' : '';
            $fullName = $request->first_name . $middle . ' ' . $request->last_name;

            $user = User::create([
                'name' => $fullName,
                'first_name' => $request->first_name, 
                'middle_initial' => $request->middle_initial, 
                'last_name' => $request->last_name, 
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'bus_operator', 
                'terminal' => $request->terminal, 
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'company_contact' => $request->company_contact,
                'company_email' => $request->company_email,
                'fleet_size' => $request->fleet_size,
                'photo_url' => $photoPath,
                'status' => 'active',
            ]);

            return redirect()->route('login')->with('success', 'Registration successful! Please login with your credentials.');

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}
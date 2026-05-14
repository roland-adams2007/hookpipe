<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;



class AuthController extends Controller
{

    public function showLogin()
    {
        return view('login');
    }

    public function showReg()
    {
        return view('reg');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            auth()->login($user);

            return redirect()->route('webhook.logs')->with([
                'user' => $user,
                'token' => $token,
                'success' => 'Account created successfully'
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create account: ' . $e->getMessage()])->withInput();
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return redirect()->intended(route('webhook.logs'))->with([
                'user' => $user,
                'token' => $token,
                'success' => 'Logged in successfully'
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $token->delete();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

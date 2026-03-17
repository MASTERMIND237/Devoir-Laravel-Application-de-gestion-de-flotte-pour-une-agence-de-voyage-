<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //register
    public function register(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:6',
    ]);
    User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);
    return redirect()->route('login');
}
//login

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('products.index');
    }
    return back()->withErrors([
        'email' => 'Email ou mot de passe incorrect',
    ]);
}
//logout
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
        try {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->route('products.index');
            }
        } catch (\RuntimeException $e) {
            // Mot de passe stocké non chiffré ou format inattendu — ne pas exposer l'erreur
            \Log::warning('Login RuntimeException: '.$e->getMessage());
        }
}
}

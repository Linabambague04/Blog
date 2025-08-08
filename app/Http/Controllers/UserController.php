<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


use Illuminate\Http\Request;

class UserController extends Controller
{

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Subir el avatar si se proporcionó
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $avatarPath, // <-- guardar la ruta aquí
        ]);

        Auth::login($user);

        return redirect()->route('posts.index');
    }

    public function edit()
    {
        return view('auth.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user->name = $request->name;

    if ($request->hasFile('avatar')) {
        $avatar = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $avatar;
    }

    $user->save();

    return redirect()->route('posts.index')->with('success', 'Perfil actualizado correctamente.');
}



    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended('posts');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->withInput();
    }
}

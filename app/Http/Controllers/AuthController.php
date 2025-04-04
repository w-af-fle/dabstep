<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(){
        return view('auth.signup');
    }

    public function login(){
        return view('auth.login');
    }



    public function registr(Request $request){
        $request->validate([
            'name'=>'required|max:50',
            'email'=>'required|email|unique:App\Models\User',
            'password'=>'required|min:6'
        ]);

       $user = User::create([
            'name'=>$request->name,
            'email'=>request('email'),
            'password'=>Hash::make($request->password)
        ]);
        // $user->remember_token = $user->createToken('MyAppToken')->plainTextToken;
        $user->save();
        return redirect()->route('login');
    }


    public function authenticate(Request $request){
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');

    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
        
}


   
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller

{
    function register(Request $request){
        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
        
        $user = User::create($fields);
        $token = $user->createToken($request->name)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
    
    function login(Request $request){
        
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return [
                'errors' => [
                    'email'=> ['This Provided cradential are incorrect...']
                ]
            ];
            
            // return [
            //     'message' => 'This Provided cradential are incorrect...'
            // ];
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];

    }

    function logout(Request $request){

        $request->user()->tokens()->delete();
        return [
            'message' => 'You are logout...'
        ];

    }
}
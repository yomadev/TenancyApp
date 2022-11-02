<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "name" => "required|string|max:100",
            "phone" => "required|max:20|unique:users,phone",
            "email" => "required|email|unique:users,email",
            "password" => "required|max:20",
        ]);

        if ($validate->fails())
            return $this->setErrorResponse($validate->errors(), 422);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "password" => Hash::make($request->password),
        ]);

        // create token
        $user->token = $user->createToken("AdminToken")->plainTextToken;

        return $this->setResponse($user);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "email" => "required|email|exists:users,email",
            "password" => "required|max:20",
        ]);

        if ($validate->fails())
            return $this->setErrorResponse($validate->errors(), 422);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];


        if (Auth::guard()->attempt($credentials)) {
            $user = auth()->user();
            // create token
            $user->token = $user->createToken("AdminToken")->plainTextToken;
            return $this->setResponse($user);
        } else {
            return $this->setErrorMessage('Unauthorised', 401);
        }
    }

    public function logout(Request $request)
    {
        if ($user = $request->user()) {
            $user->currentAccessToken()->delete();
            return $this->setErrorMessage('', 200);
        }
        return $this->unauthorised();
    }

}

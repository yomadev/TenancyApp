<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request)
    {
        if ($this->hasAuth()) {
            return $this->setResponse(User::all());
        }
        return $this->unauthorised();
    }


    public function show(Request $request, int $id)
    {
        if ($this->hasAuth()) {
            if ($user = $request->user()) {
                $user->token = $request->bearerToken();
                return $this->setResponse($user);

            } else if ($user = User::find($id)) {
                return $this->setResponse($user);
            }
            return $this->setErrorMessage("not found");
        }
        return $this->unauthorised();
    }

    public function update(Request $request, int $id)
    {
        if ($user = $request->user()) {
            $validate = Validator::make($request->all(), [
                "name" => "required|string|max:100",
                "phone" => "required|max:20|unique:users,phone,$user->id",
                "email" => "required|email|unique:users,email,$user->id",
                "password" => "nullable|max:20",
            ]);

            if ($validate->fails())
                return $this->setErrorResponse($validate->errors(), 422);

            $data = [
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
            ];

            if (isset($request->password))
                $data["password"] = Hash::make($request->password);

            $user->update($data);

            return $this->setResponse($user);
        }
        return $this->unauthorised();
    }

}

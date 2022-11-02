<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{


    public function index(Request $request)
    {
        return Tenant::all();
    }

    public function store(Request $request)
    {

        $validate = Validator::make($request->all(), [
            "username" => "required|unique:tenants,id",
            "name" => "required|string|max:100",
            "email" => "required|email",
            "phone" => "required|max:20",
            "password" => "required",
        ]);

        if ($validate->fails())
            return $validate->errors();


        // create tenant
        $tenant1 = Tenant::create(['id' => $request->username]);

        // create tenant domain
        $tenant1->domains()->create(['domain' => $tenant1->id . '.localhost']);

        // create tenant user
        $user = null;
       return $tenant1->run(function ($tenant) use ($request, &$user) {

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "password" => Hash::make($request->password),
            ]);

            error_log("user 1");
            // create token
            $token = $user->createToken("TenantToken")->plainTextToken;

            error_log("user 2");

            return response()->json([
                "tenant" => $tenant,
                "user" => $user ,
                "token" => $token,
                "domain" => $tenant->domains()->first()->domain,
            ]);
        });

    }


    public function show(Request $request, int $id)
    {
        if($request->user()){
            return $request->user();
        }
        return "not found" . $request->bearerToken();
    }
}

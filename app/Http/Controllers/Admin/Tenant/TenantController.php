<?php

namespace App\Http\Controllers\Admin\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        if ($this->hasAuth()) {
            return $this->setResponse(Tenant::all());
        }
        return $this->unauthorised();
    }


    public function show(Request $request, $id)
    {
        if ($this->hasAuth()) {
            if ($request->user()) {
                return $request->user();
            }
            return "not found" . $request->bearerToken();
        }
        return $this->unauthorised();
    }

    public function store(Request $request): JsonResponse
    {
        if ($this->hasAuth()) {
            $validate = Validator::make($request->all(), [
                "subdomain" => "required|min:3|max:20|unique:tenants,id",
                "name" => "required|string|max:100",
                "email" => "required|email",
                "phone" => "required|max:20",
                "password" => "required|max:20",
            ]);

            if ($validate->fails())
                return $this->setErrorResponse($validate->errors(), 422);


            // create tenant
            $tenant = Tenant::create([
                'id' => $request->subdomain
            ]);

            // create tenant domain
            $tenant->domains()->create([
                'domain' => $tenant->id . '.localhost'
            ]);

            // create tenant user
            return $tenant->run(function ($tenant) use ($request) {

                $user = User::create([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                    "password" => Hash::make($request->password),
                ]);

                // create token
                $user->token = $user->createToken("TenantToken")->plainTextToken;

                $tenant->user = $user;
                $tenant->domain = $tenant->domains()->first();

                return $this->setResponse([
                    "tenant" => $tenant,
                    "route_url" => $tenant->domain->domain ?? "",
                ]);
            });
        }
        return $this->unauthorised();
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        if ($request->user()) {
            if ($tenant = Tenant::find($id)) {
                Tenant::withoutEvents(function () use ($tenant) {
                    return $tenant->delete();
                });
//                $tenant->softDelete();
                return $this->setResponse($tenant);
            }
        }
        return $this->unauthorised();
    }
}

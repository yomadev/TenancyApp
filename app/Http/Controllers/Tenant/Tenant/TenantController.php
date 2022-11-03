<?php

namespace App\Http\Controllers\Tenant\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{

    public function show(Request $request, $id)
    {
        if(tenant())
            return $this->setResponse(tenant());

        if ($this->hasAuth()) {
            if ($request->user()) {
                return $request->user();
            }
            return  $this->setErrorMessage("not found");
        }
        return $this->unauthorised();
    }
}

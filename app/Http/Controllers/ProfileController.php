<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return new UserResource($user) ;
    }
}

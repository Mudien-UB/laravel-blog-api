<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $accessToken = auth()->attempt($validated);

        if (!$accessToken) {
            abort(Response::HTTP_UNAUTHORIZED, 'Invalid credentials');
        }

        return BaseResource::respond(Response::HTTP_OK, 'Login Berhasil', new AuthResource($accessToken));
    }

    public function logout()
    {
        auth()->logout();

        return BaseResource::respond(Response::HTTP_OK, 'Logout Berhasil');
    }

    public function getMe()
    {
        $me = auth()->user();

        if (!$me) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        return BaseResource::respond(Response::HTTP_OK, 'Data user ditemukan', new UserResource($me));
    }
}

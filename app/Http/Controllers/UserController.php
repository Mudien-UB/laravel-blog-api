<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Http\Resources\BaseResourcePageable;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return BaseResourcePageable::respond(Response::HTTP_OK, 'Data user ditemukan', UserResource::collection($users));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return BaseResource::respond(Response::HTTP_CREATED, 'Registrasi Berhasil', new UserResource($user));
    }

    public function update(Request $request, String $userId){

        $validated = $request->validate([
            'username' => 'nullable',
            'email'=> 'nullable',
        ]);

        $user = User::find($userId);

        if(empty($user)){
            abort(Response::HTTP_BAD_REQUEST,'user not found');
        }
        $user->username = $validated['username'] ?? $user->username;
        $user->email = $validated['email'] ?? $user->email;
        $user->save();

        return BaseResourcePageable::respond(Response::HTTP_OK,'Data user berhasil diupdate', UserResource::make($user));

    }
    public function uploadImageProfile(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:4096'], // Maksimum 4MB
        ]);

        $user = auth()->user();
        if (!$user) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        $publicId = $user->id_image_profile ?? null;

        try {

            if($publicId){
                $resDeleted = CloudinaryController::deleteImage($publicId);
                if(empty($resDeleted)){
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to delete image');
                }
            }



            $uploadedFile = CloudinaryController::uploadImage($validated['image'], 'profile:'.$user->id, 'profile');
            $urlImage = $uploadedFile['secure_url'];
            $idImage = $uploadedFile['public_id'];
        } catch (\Exception $e) {
            abort(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $user->id_image_profile = $idImage;
        $user->url_image_profile = $urlImage;
        $user->save();

        return BaseResource::respond(Response::HTTP_OK, 'Data user berhasil diupdate', new UserResource($user));
    }


    public function destroy(String $userId){
        $user = User::find($userId);
        if(empty($user)){
            abort(Response::HTTP_BAD_REQUEST,'user not found');
        }
        $user->delete();
        return BaseResource::respond(Response::HTTP_OK,'Data user berhasil dihapus');
    }
}

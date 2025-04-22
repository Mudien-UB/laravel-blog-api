<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class CloudinaryController extends Controller
{
    public static function deleteImage($idImage){
        $response = Cloudinary::uploadApi()->destroy($idImage);
        return $response;
    }

    public static function uploadImage($file, $title,$folder)
    {
        $uploaded = Cloudinary::uploadApi()->upload(
            $file->getRealPath(),
            ['folder' => 'belajar/'.$folder, 'display_name' => $title]
        );
        return $uploaded;
    }
}

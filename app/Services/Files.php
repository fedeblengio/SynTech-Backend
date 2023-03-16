<?php

namespace App\Services;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

class Files
{
    public function getImage($imageName){
        return  base64_encode(Storage::disk('ftp')->get($imageName));
    }
}
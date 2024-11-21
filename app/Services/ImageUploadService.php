<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    public function upload(UploadedFile $image,string $path = 'uploads/',string $disk = 'local') : string
    {
        
        $imageName = time().'.'.$image->extension();

        $image->storeAs($path,$imageName,$disk);

        return $imageName;
    }

    public function delete(string $path,string $disk = 'public') : bool 
    {
        return Storage::disk($disk)->delete($path);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function serve(Upload $upload)
    {
       
        $filePath = $upload->file_name;
        if (Storage::disk('local')->exists($filePath)) {
            return response()->file(Storage::disk('local')->path($filePath));
        }

        if (Storage::disk('public')->exists($filePath)) {
            return response()->file(Storage::disk('public')->path($filePath));
        }

        abort(404);
    }
}

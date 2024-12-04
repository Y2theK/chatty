<?php

namespace App\Services;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function upload(UploadedFile $file, string $path = 'uploads/', string $disk = 'local'): ?int
    {
        $type = [
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'svg' => 'image',
            'webp' => 'image',
            'gif' => 'image',
            'mp4' => 'video',
            'mpg' => 'video',
            'mpeg' => 'video',
            'webm' => 'video',
            'ogg' => 'video',
            'avi' => 'video',
            'mov' => 'video',
            'flv' => 'video',
            'swf' => 'video',
            'mkv' => 'video',
            'wmv' => 'video',
            'wma' => 'audio',
            'aac' => 'audio',
            'wav' => 'audio',
            'mp3' => 'audio',
            'zip' => 'archive',
            'rar' => 'archive',
            '7z' => 'archive',
            'doc' => 'document',
            'txt' => 'document',
            'docx' => 'document',
            'pdf' => 'document',
            'csv' => 'document',
            'xml' => 'document',
            'ods' => 'document',
            'xlr' => 'document',
            'xls' => 'document',
            'xlsx' => 'document',
        ];

        $extension = $file->extension();
        if (isset($type[$extension])) {
            $imageName = time().'.'.$extension;

            $upload = new Upload;
            $upload->file_name = $path.$imageName;
            $upload->file_original_name = $file->getClientOriginalName();
            $upload->extension = $extension;
            $upload->user_id = auth()->id();
            $upload->file_size = $file->getSize(); //in bytes
            $upload->type = $type[$extension];
            $upload->save();

            $file->storeAs($path, $imageName, $disk);

            return $upload->id;
        }

        return null;

    }

    public function delete(Upload $upload, string $disk = 'public'): bool
    {
        Storage::disk($disk)->delete($upload->file_name);

        return $upload->delete();
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public static function upload($file, $path = 'uploads/others', $disk = 'public')
    {
        $file_name = time() . '_' . rand() . '_' . (auth()->id() ?? '') . '_' . $file->getClientOriginalName();
        $filename_dir = trim($path, "/") . "/" . $file_name;

        $path = Storage::disk($disk)->putFileAs('', $file, $filename_dir);

        return (($disk == 'public' ? 'storage/' : '') . $filename_dir) ?? '';
    }

    public static function delete($path = '')
    {
        $path = storage_path("app/public/" . $path);
        if (file_exists($path)) unlink($path);
    }
}

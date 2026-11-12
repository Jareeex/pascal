<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesUploads
{
    protected function saveImage(UploadedFile $file, string $prefix = ''): string
    {
        $name = ($prefix ? $prefix.'_' : '').Str::uuid().'.'.$file->extension();
        $file->storeAs('uploads', $name, 'public');

        return $name;
    }

    protected function deleteImage(?string $name): void
    {
        if (! $name || basename($name) !== $name) {
            return;
        }

        Storage::disk('public')->delete('uploads/'.$name);

        $legacyPath = public_path('assets/uploads/'.$name);
        if (is_file($legacyPath)) {
            unlink($legacyPath);
        }
    }
}

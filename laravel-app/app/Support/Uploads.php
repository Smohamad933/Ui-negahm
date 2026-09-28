<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Stores uploaded files directly under public/uploads/... (a real folder,
 * not a storage:link symlink) so this works unmodified on shared IIS
 * hosting where symlinks are often unavailable or restricted.
 */
class Uploads
{
    public const ALLOWED_IMAGE_MIMES = ['image/png', 'image/jpeg', 'image/webp', 'image/gif', 'image/svg+xml'];
    public const ALLOWED_VIDEO_MIMES = ['video/mp4', 'video/webm'];
    public const ALLOWED_FONT_EXTENSIONS = ['woff2', 'woff', 'ttf', 'otf'];
    public const MAX_IMAGE_BYTES = 12 * 1024 * 1024; // 12MB

    /**
     * @param  string  $folder  images|fonts (relative to public/uploads)
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $filename = date('Ymd_His') . '_' . Str::random(10) . '.' . $extension;

        $destination = public_path('uploads/' . trim($folder, '/'));
        if (! is_dir($destination)) {
            mkdir($destination, 0775, true);
        }

        $file->move($destination, $filename);

        return '/uploads/' . trim($folder, '/') . '/' . $filename;
    }

    public static function deletePublicPath(?string $publicPath): void
    {
        if (! $publicPath || ! str_starts_with($publicPath, '/uploads/')) {
            return;
        }

        $full = public_path(ltrim($publicPath, '/'));
        if (is_file($full)) {
            @unlink($full);
        }
    }
}

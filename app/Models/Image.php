<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = ['path'];

    public function url(?string $fallback = null): string
    {
        if (! $this->path) {
            return $fallback ?? asset('images/static/destination-sa-pa.jpg');
        }

        $path = ltrim($this->path, '/');

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            $storagePath = substr($path, strlen('storage/'));

            return Storage::disk('public')->exists($storagePath)
                ? asset($path)
                : ($fallback ?? asset('images/static/destination-sa-pa.jpg'));
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return Storage::disk('public')->exists($path)
            ? asset('storage/' . $path)
            : ($fallback ?? asset('images/static/destination-sa-pa.jpg'));
    }

    public function existsOnPublicDisk(): bool
    {
        if (! $this->path) {
            return false;
        }

        $path = ltrim($this->path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return Storage::disk('public')->exists($path);
    }

    public function imageable()
    {
        return $this->morphTo();
    }
}

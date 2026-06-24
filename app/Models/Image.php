<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = ['path'];

    public function url(): string
    {
        if (! $this->path) {
            return '';
        }

        $path = ltrim($this->path, '/');

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }

    public function existsOnPublicDisk(): bool
    {
        return $this->path && Storage::disk('public')->exists(ltrim($this->path, '/'));
    }

    public function imageable()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id', 'name', 'slug', 'location', 'description', 'price',
        'duration_days', 'duration_nights', 'capacity', 'itinerary',
        'highlights', 'included', 'policies', 'is_active', 'status',
    ];

    // Tự động chuyển đổi dữ liệu JSON từ database thành Mảng (Array) trong PHP
    protected $casts = [
        'itinerary' => 'array',
        'highlights' => 'array',
        'included' => 'array',
        'policies' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'capacity' => 'integer',
    ];

    // Mối quan hệ: 1 Tour thuộc về 1 Host (User)
    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

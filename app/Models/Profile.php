<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'description', // Short Description  
        'about',
        'vision',
        'mission',
        'established_year',
        'principal_name',
        'featured_image',
        'gallery',
        'sections',
        'achievements',
        'facilities',
        'address',
        'phone',
        'email',
        'website',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'gallery' => 'array',
        'sections' => 'array',
        'achievements' => 'array',
        'facilities' => 'array',
    ];

    /**
     * Get the featured image URL attribute.
     */
    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
        );
    }
}

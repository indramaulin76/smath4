<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'image',
        'gallery',
        'links',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
        'gallery' => 'array',
        'links' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Get the image URL attribute.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? asset('storage/' . $this->image) : null,
        );
    }

    /**
     * Get the published date with fallback to created_at.
     */
    protected function publishedDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->published_at ?: $this->created_at,
        );
    }

    /**
     * Scope for published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function($q) {
                $q->whereNotNull('published_at')
                  ->where('published_at', '<=', now())
                  ->orWhereNull('published_at');
            });
    }
}

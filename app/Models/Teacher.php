<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'photo',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the photo URL attribute.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->photo) {
                    return null;
                }
                
                // For production, ensure the file exists
                $storagePath = storage_path('app/public/' . $this->photo);
                if (file_exists($storagePath)) {
                    return asset('storage/' . $this->photo);
                }
                
                // Fallback for development or if file doesn't exist
                return asset('storage/' . $this->photo);
            }
        );
    }
}

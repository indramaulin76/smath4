<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;

class TestImageController extends Controller
{
    public function test()
    {
        $profile = Profile::first();
        
        if (!$profile) {
            return response()->json(['error' => 'No profile found']);
        }
        
        $paths = [];
        
        // Test different path methods for featured image
        if ($profile->featured_image) {
            $paths['featured_image'] = [
                'raw_path' => $profile->featured_image,
                'asset_storage' => asset('storage/' . $profile->featured_image),
                'storage_url' => \Storage::url($profile->featured_image),
                'public_path_exists' => file_exists(public_path('storage/' . $profile->featured_image)),
                'storage_path_exists' => file_exists(storage_path('app/public/' . $profile->featured_image)),
            ];
        }
        
        // Test gallery paths
        if ($profile->gallery) {
            $paths['gallery'] = [];
            foreach ($profile->gallery as $index => $image) {
                $paths['gallery'][$index] = [
                    'raw_path' => $image,
                    'asset_storage' => asset('storage/' . $image),
                    'storage_url' => \Storage::url($image),
                    'public_path_exists' => file_exists(public_path('storage/' . $image)),
                    'storage_path_exists' => file_exists(storage_path('app/public/' . $image)),
                ];
            }
        }
        
        return response()->json($paths, JSON_PRETTY_PRINT);
    }
}

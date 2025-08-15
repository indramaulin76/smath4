<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Livewire Asset URL
    |--------------------------------------------------------------------------
    |
    | This value sets the path that Livewire will serve its JavaScript assets 
    | from. If you're using a CDN or serving assets from a different domain,
    | you can set this value to the full URL.
    |
    */

    'asset_url' => env('LIVEWIRE_ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Livewire App URL
    |--------------------------------------------------------------------------
    |
    | This value should be set to the URL of your application. This value is
    | used when the framework needs to place the application's URL in a
    | notification or any other location as required by the application.
    |
    */

    'app_url' => env('APP_URL', config('app.url')),

    /*
    |--------------------------------------------------------------------------
    | Livewire Middleware Group
    |--------------------------------------------------------------------------
    |
    | This value sets the middleware group that will be applied to all Livewire
    | requests. You can create your own middleware group if you need to.
    |
    */

    'middleware_group' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Livewire Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire handles file uploads by storing uploads in a temporary directory
    | before they are moved to their final destination. All file uploads are
    | directed to a global endpoint for temporary storage. The configuration
    | below determines where this temporary directory exists.
    |
    */

    'temporary_file_upload' => [
        'disk' => 'public',        // Example: 'local', 's3'              
        'rules' => 'file|max:12288', // Example: ['file', 'mimes:png,jpg,pdf', 'max:1024']
        'directory' => 'livewire-tmp',
        'middleware' => null,          // Example: 'throttle:5,1'
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5, // Max upload time in minutes.
    ],

    /*
    |--------------------------------------------------------------------------
    | Manifest File Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path to the Livewire manifest file.
    | The default should work for most cases (which is
    | "<app_root>/bootstrap/cache/livewire-components.php"), but for specific
    | cases like when hosting on Laravel Vapor, it could be set to a different value.
    |
    | Example: for Laravel Vapor, it would be "/tmp/storage/bootstrap/cache/livewire-components.php"
    |
    */

    'manifest_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Legacy Model Binding Behavior
    |--------------------------------------------------------------------------
    |
    | Legacy model binding behavior uses the `findOrFail` method to retrieve
    | models when the Route Model Binding key is not found. If disabled,
    | non-existing Route Model Binding keys will result in null instead
    | of throwing ModelNotFoundException.
    |
    */

    'legacy_model_binding' => true,

    /*
    |--------------------------------------------------------------------------
    | Inject Assets
    |--------------------------------------------------------------------------
    |
    | This value determines whether Livewire will automatically inject its
    | JavaScript and CSS into your application's HTML. If disabled, you
    | will need to manually include @livewireStyles and @livewireScripts
    | in your application's HTML.
    |
    */

    'inject_assets' => true,

    /*
    |--------------------------------------------------------------------------
    | Navigate (SPA mode)
    |--------------------------------------------------------------------------
    |
    | By default, Livewire will navigate programmatically (window.history.pushState)
    | to the URL returned by Laravel's "redirect()" function. If you want this
    | behavior to be disabled, set 'navigate' to 'false'.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTML Morph Markers
    |--------------------------------------------------------------------------
    |
    | Livewire will inject HTML comment markers to track changes in most
    | cases, this should be left alone, but if you'd like to disable
    | this behavior for any reason, you can do so by setting this to false.
    |
    */

    'inject_morph_markers' => true,

    /*
    |--------------------------------------------------------------------------
    | Pagination Theme
    |--------------------------------------------------------------------------
    |
    | When creating a pagination view (using either "view()" or "simplePaginate()")
    | Livewire will use the following pagination theme by default.
    |
    */

    'pagination_theme' => 'tailwind',
];

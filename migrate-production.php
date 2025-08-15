<?php
/**
 * Emergency Migration Script for Production
 * WARNING: Remove this file after use for security!
 * Access: yoursite.com/migrate-production.php?key=smath2025
 */

// Security key - change this!
$security_key = 'smath2025';

if (!isset($_GET['key']) || $_GET['key'] !== $security_key) {
    die('Unauthorized access');
}

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Boot the application
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<h2>🚀 Running Production Migration</h2>";
    
    // Run migrations
    echo "<p>Running migrations...</p>";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    
    if ($exitCode === 0) {
        echo "<p style='color: green;'>✅ Migration completed successfully!</p>";
        
        // Show migration status
        echo "<h3>Migration Status:</h3>";
        echo "<pre>";
        Artisan::call('migrate:status');
        echo Artisan::output();
        echo "</pre>";
        
        // Clear caches
        echo "<p>Clearing caches...</p>";
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        echo "<p style='color: green;'>✅ Caches cleared!</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Migration failed!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>⚠️ IMPORTANT: Delete this file after use for security!</strong></p>";
echo "<p>File location: " . __FILE__ . "</p>";
?>

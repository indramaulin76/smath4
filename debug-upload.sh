#!/bin/bash

# Debug Upload Issues Specifically
echo "🔍 Debugging upload functionality..."

# Colors
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}=== UPLOAD DEBUGGING ===${NC}"

# 1. Test if storage is accessible
echo -e "${YELLOW}📁 Testing storage access...${NC}"
if [ -f "public/storage/teachers/test-access.txt" ]; then
    echo -e "${GREEN}✅ Storage accessible${NC}"
    echo "Content: $(cat public/storage/teachers/test-access.txt)"
else
    echo -e "${RED}❌ Storage not accessible${NC}"
fi

# 2. Check PHP upload settings
echo -e "${YELLOW}⚙️ Checking PHP upload settings...${NC}"
php -r "
echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;
echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;
echo 'max_file_uploads: ' . ini_get('max_file_uploads') . PHP_EOL;
echo 'memory_limit: ' . ini_get('memory_limit') . PHP_EOL;
"

# 3. Test Filament upload manually
echo -e "${YELLOW}🧪 Testing manual upload...${NC}"

# Create a test image
echo -e "${YELLOW}Creating test image...${NC}"
php -r "
\$image = imagecreate(100, 100);
\$bg = imagecolorallocate(\$image, 255, 255, 255);
\$text = imagecolorallocate(\$image, 0, 0, 0);
imagestring(\$image, 5, 20, 40, 'TEST', \$text);
imagejpeg(\$image, 'test-teacher.jpg');
imagedestroy(\$image);
echo 'Test image created: test-teacher.jpg';
"

# Move test image to storage
if [ -f "test-teacher.jpg" ]; then
    cp test-teacher.jpg storage/app/public/teachers/test-teacher.jpg
    cp test-teacher.jpg public/storage/teachers/test-teacher.jpg
    chmod 644 storage/app/public/teachers/test-teacher.jpg
    chmod 644 public/storage/teachers/test-teacher.jpg
    echo -e "${GREEN}✅ Test image moved to storage${NC}"
    rm test-teacher.jpg
else
    echo -e "${RED}❌ Failed to create test image${NC}"
fi

# 4. Test if Laravel can access storage
echo -e "${YELLOW}🔧 Testing Laravel storage disk...${NC}"
php artisan tinker --execute="
try {
    \$disk = Storage::disk('public');
    echo 'Storage disk: ' . (\$disk ? 'OK' : 'FAILED') . PHP_EOL;
    
    // Test file exists
    if (\$disk->exists('teachers/test-teacher.jpg')) {
        echo 'Test image exists in storage: YES' . PHP_EOL;
    } else {
        echo 'Test image exists in storage: NO' . PHP_EOL;
    }
    
    // Test URL generation
    \$url = \$disk->url('teachers/test-teacher.jpg');
    echo 'Generated URL: ' . \$url . PHP_EOL;
    
} catch (Exception \$e) {
    echo 'Storage test failed: ' . \$e->getMessage() . PHP_EOL;
}
"

# 5. Check current teachers in database
echo -e "${YELLOW}📊 Checking teachers in database...${NC}"
php artisan tinker --execute="
\$teachers = App\\Models\\Teacher::all();
echo 'Total teachers: ' . \$teachers->count() . PHP_EOL;
foreach (\$teachers as \$teacher) {
    echo 'Teacher: ' . \$teacher->name . ' | Photo: ' . (\$teacher->photo ?: 'NO PHOTO') . PHP_EOL;
    if (\$teacher->photo) {
        echo '  Photo URL: ' . asset('storage/' . \$teacher->photo) . PHP_EOL;
        echo '  File exists: ' . (file_exists(storage_path('app/public/' . \$teacher->photo)) ? 'YES' : 'NO') . PHP_EOL;
    }
}
"

# 6. Create a simple upload test
echo -e "${YELLOW}📝 Creating simple upload test...${NC}"
cat > upload-test.php << 'EOF'
<?php
// Simple upload test
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h2>Upload Test</h2>";

if ($_POST && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    
    echo "<p>File info:</p>";
    echo "<pre>";
    print_r($file);
    echo "</pre>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = 'upload-test-' . time() . '.jpg';
        $targetPath = storage_path('app/public/teachers/' . $fileName);
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo "<p style='color: green;'>✅ Upload successful!</p>";
            echo "<p>File saved to: " . $targetPath . "</p>";
            
            // Copy to public storage
            copy($targetPath, public_path('storage/teachers/' . $fileName));
            
            echo "<p>Access URL: <a href='/storage/teachers/$fileName' target='_blank'>/storage/teachers/$fileName</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Upload failed!</p>";
        }
    } else {
        echo "<p style='color: red;'>Upload error: " . $file['error'] . "</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <p>Test Upload:</p>
    <input type="file" name="photo" accept="image/*" required>
    <button type="submit">Upload Test</button>
</form>
EOF

echo -e "${GREEN}✅ Upload debugging completed!${NC}"
echo -e "${YELLOW}📋 Test files created:${NC}"
echo "1. Test image: storage/app/public/teachers/test-teacher.jpg"
echo "2. Upload test page: upload-test.php"
echo ""
echo -e "${YELLOW}📋 Next steps:${NC}"
echo "1. Access: https://smatunasharapan.site/upload-test.php"
echo "2. Try uploading an image through the test form"
echo "3. Check if test image is accessible: https://smatunasharapan.site/storage/teachers/test-teacher.jpg"
echo "4. If upload works, try admin panel again"
echo ""
echo -e "${YELLOW}📋 Manual verification:${NC}"
echo "ls -la storage/app/public/teachers/"
echo "ls -la public/storage/teachers/"

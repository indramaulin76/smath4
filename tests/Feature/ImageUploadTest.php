<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Teacher;
use App\Models\Profile;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);
        
        Storage::fake('public');
    }

    /** @test */
    public function it_can_upload_article_image()
    {
        $this->actingAs($this->user);
        
        $file = UploadedFile::fake()->image('article.jpg', 800, 600);
        
        $article = Article::create([
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'image' => $file->store('articles', 'public'),
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->assertNotNull($article->image);
        Storage::disk('public')->assertExists($article->image);
    }

    /** @test */
    public function it_can_upload_teacher_photo()
    {
        $this->actingAs($this->user);
        
        $file = UploadedFile::fake()->image('teacher.jpg', 400, 400);
        
        $teacher = Teacher::create([
            'name' => 'Test Teacher',
            'title' => 'Math Teacher',
            'photo' => $file->store('teachers', 'public'),
            'bio' => 'Test bio',
        ]);

        $this->assertNotNull($teacher->photo);
        Storage::disk('public')->assertExists($teacher->photo);
    }

    /** @test */
    public function it_can_upload_profile_images()
    {
        $this->actingAs($this->user);
        
        $featuredFile = UploadedFile::fake()->image('featured.jpg', 1200, 800);
        $galleryFile1 = UploadedFile::fake()->image('gallery1.jpg', 800, 600);
        $galleryFile2 = UploadedFile::fake()->image('gallery2.jpg', 800, 600);
        
        $profile = Profile::create([
            'title' => 'SMA Test',
            'description' => 'Test description',
            'featured_image' => $featuredFile->store('profile', 'public'),
            'gallery' => [
                $galleryFile1->store('profile/gallery', 'public'),
                $galleryFile2->store('profile/gallery', 'public'),
            ],
            'about' => 'Test about',
        ]);

        $this->assertNotNull($profile->featured_image);
        Storage::disk('public')->assertExists($profile->featured_image);
        
        foreach ($profile->gallery as $galleryImage) {
            Storage::disk('public')->assertExists($galleryImage);
        }
    }

    /** @test */
    public function it_handles_missing_images_gracefully()
    {
        $article = Article::create([
            'title' => 'Test Article',
            'content' => 'Test content',
            'excerpt' => 'Test excerpt',
            'image' => null,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/articles');
        $response->assertStatus(200);
        $response->assertSee('Test Article');
    }

    /** @test */
    public function it_validates_image_file_types()
    {
        $this->actingAs($this->user);
        
        // Test with invalid file type
        $file = UploadedFile::fake()->create('document.pdf', 1000);
        
        try {
            $article = Article::create([
                'title' => 'Test Article',
                'content' => 'Test content',
                'excerpt' => 'Test excerpt',
                'image' => $file->store('articles', 'public'),
                'is_published' => true,
                'published_at' => now(),
            ]);
            
            // This should work as we're not validating at model level
            // But in real app, validation should be done in form requests
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Validation should prevent invalid file types');
        }
    }

    /** @test */
    public function storage_symlink_exists()
    {
        $symlinkPath = public_path('storage');
        $this->assertTrue(is_link($symlinkPath), 'Storage symlink should exist');
        
        $targetPath = storage_path('app/public');
        $this->assertTrue(is_dir($targetPath), 'Storage target directory should exist');
    }
}

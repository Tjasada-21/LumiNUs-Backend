<?php

namespace Tests\Feature;

use App\Models\Alumni;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumni_can_create_a_post_with_images(): void
    {
        Storage::fake('public');

        $alumni = Alumni::create([
            'first_name' => 'Jane',
            'middle_name' => null,
            'last_name' => 'Doe',
            'date_of_birth' => '1995-01-01',
            'sex' => 'Female',
            'year_graduated' => '2017-01-01',
            'student_id_number' => 'STU-001',
            'email' => 'jane.doe@example.com',
            'phone_number' => null,
            'password_hash' => Hash::make('password'),
            'alumni_photo' => null,
            'alumni_bio' => null,
        ]);

        Sanctum::actingAs($alumni);

        $response = $this->post('/api/posts', [
            'caption' => 'First post',
            'images' => [UploadedFile::fake()->image('post.jpg')],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('post.caption', 'First post');
        $response->assertJsonCount(1, 'post.images');

        $this->assertDatabaseHas('posts', [
            'alumni_id' => $alumni->id,
            'caption' => 'First post',
            'moderation_status' => 'approved',
        ]);

        $this->assertDatabaseCount('images_posts', 1);

        $postImagePath = $response->json('post.images.0.image_path');

        $this->assertNotNull($postImagePath);
        $this->assertStringContainsString('/storage/posts/', $postImagePath);
    }
}
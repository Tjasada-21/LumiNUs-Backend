<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\ImagesPost;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumni_can_retrieve_the_feed_posts(): void
    {
        $alumni = Alumni::create([
            'first_name' => 'Jane',
            'middle_name' => null,
            'last_name' => 'Doe',
            'date_of_birth' => '1995-01-01',
            'sex' => 'Female',
            'year_graduated' => '2017-01-01',
            'student_id_number' => 'STU-002',
            'email' => 'jane.doe2@example.com',
            'phone_number' => null,
            'password_hash' => Hash::make('password'),
            'alumni_photo' => 'https://example.com/avatar.jpg',
            'alumni_bio' => null,
        ]);

        Sanctum::actingAs($alumni);

        $post = Post::create([
            'alumni_id' => $alumni->id,
            'caption' => 'Feed post',
            'moderation_status' => 'approved',
        ]);

        ImagesPost::create([
            'post_id' => $post->id,
            'image_path' => 'https://example.com/post-image.jpg',
        ]);

        $response = $this->get('/api/posts');

        $response->assertOk();
        $response->assertJsonPath('posts.0.caption', 'Feed post');
        $response->assertJsonPath('posts.0.alumni.first_name', 'Jane');
        $response->assertJsonPath('posts.0.images.0.image_path', 'https://example.com/post-image.jpg');
    }
}
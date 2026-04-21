<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostRepostTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumni_can_toggle_reposts_and_see_feed_state(): void
    {
        $alumni = Alumni::create([
            'first_name' => 'Jane',
            'middle_name' => null,
            'last_name' => 'Doe',
            'date_of_birth' => '1995-01-01',
            'sex' => 'Female',
            'year_graduated' => '2017-01-01',
            'student_id_number' => 'STU-003',
            'email' => 'jane.doe3@example.com',
            'phone_number' => null,
            'password_hash' => Hash::make('password'),
            'alumni_photo' => null,
            'alumni_bio' => null,
        ]);

        Sanctum::actingAs($alumni);

        $post = Post::create([
            'alumni_id' => $alumni->id,
            'caption' => 'Repostable post',
            'moderation_status' => 'approved',
        ]);

        $feedResponse = $this->get('/api/posts');

        $feedResponse->assertOk();
        $feedResponse->assertJsonPath('posts.0.repost_count', 0);
        $feedResponse->assertJsonPath('posts.0.my_repost', false);

        $repostResponse = $this->post('/api/posts/' . $post->id . '/reposts');

        $repostResponse->assertOk();
        $repostResponse->assertJsonPath('repost_count', 1);
        $repostResponse->assertJsonPath('my_repost', true);

        $feedAfterRepost = $this->get('/api/posts');

        $feedAfterRepost->assertOk();
        $feedAfterRepost->assertJsonPath('posts.0.repost_count', 1);
        $feedAfterRepost->assertJsonPath('posts.0.my_repost', true);

        $removeResponse = $this->post('/api/posts/' . $post->id . '/reposts');

        $removeResponse->assertOk();
        $removeResponse->assertJsonPath('repost_count', 0);
        $removeResponse->assertJsonPath('my_repost', false);
    }
}
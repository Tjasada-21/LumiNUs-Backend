<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Reaction;
use App\Models\Repost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniProfileController extends Controller
{
    private function resolveStorageUrl(Request $request, string $path): string
    {
        $normalizedPath = ltrim(trim($path), '/');

        $publicBaseUrl = rtrim((string) config('filesystems.disks.s3.url', ''), '/');
        $bucketName = trim((string) config('filesystems.disks.s3.bucket', ''), '/');

        if ($publicBaseUrl !== '' && $bucketName !== '') {
            return $publicBaseUrl . '/' . $bucketName . '/' . $normalizedPath;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/') . '/' . $normalizedPath;
    }

    private function alumniWithStats($alumni): array
    {
        $payload = $alumni->toArray();
        $payload['posts_count'] = Post::query()
            ->where('alumni_id', $alumni->id)
            ->where('moderation_status', 'approved')
            ->count();

        return $payload;
    }

    private function buildPostPayload(Post $post): array
    {
        return [
            'id' => $post->id,
            'feed_id' => sprintf('post-%d', $post->id),
            'feed_type' => 'post',
            'caption' => $post->caption,
            'created_at' => $post->created_at,
            'alumni' => [
                'id' => $post->alumni?->id,
                'first_name' => $post->alumni?->first_name,
                'last_name' => $post->alumni?->last_name,
                'alumni_photo' => $post->alumni?->alumni_photo,
            ],
            'comment_count' => $post->comments_count ?? 0,
            'reaction_count' => $post->reactions_count ?? 0,
            'repost_count' => $post->reposts_count ?? 0,
            'my_reaction' => null,
            'my_repost' => false,
            'images' => $post->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                ];
            })->values(),
        ];
    }

    private function buildRepostPayload(Repost $repost): ?array
    {
        $originalPost = $repost->post;

        if (!$originalPost) {
            return null;
        }

        return [
            'id' => $originalPost->id,
            'feed_id' => sprintf('repost-%d', $repost->id),
            'feed_type' => 'repost',
            'caption' => $repost->caption,
            'created_at' => $repost->created_at,
            'alumni' => [
                'id' => $repost->alumni?->id,
                'first_name' => $repost->alumni?->first_name,
                'last_name' => $repost->alumni?->last_name,
                'alumni_photo' => $repost->alumni?->alumni_photo,
            ],
            'comment_count' => $originalPost->comments()->count(),
            'reaction_count' => $originalPost->reactions()->count(),
            'repost_count' => $originalPost->reposts()->count(),
            'my_reaction' => null,
            'my_repost' => false,
            'images' => $originalPost->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                ];
            })->values(),
            'original_post' => [
                'id' => $originalPost->id,
                'caption' => $originalPost->caption,
                'created_at' => $originalPost->created_at,
                'alumni' => [
                    'id' => $originalPost->alumni?->id,
                    'first_name' => $originalPost->alumni?->first_name,
                    'last_name' => $originalPost->alumni?->last_name,
                    'alumni_photo' => $originalPost->alumni?->alumni_photo,
                ],
            ],
        ];
    }

    private function resolveImageUrl(Request $request, string $imagePath): string
    {
        $trimmedPath = trim($imagePath);

        if ($trimmedPath === '') {
            return $trimmedPath;
        }

        if (preg_match('/^https?:\/\//i', $trimmedPath)) {
            if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?/i', $trimmedPath)) {
                $parsedPath = parse_url($trimmedPath, PHP_URL_PATH) ?: '';
                $parsedQuery = parse_url($trimmedPath, PHP_URL_QUERY);

                $resolvedUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/' . ltrim($parsedPath, '/');

                if ($parsedQuery) {
                    $resolvedUrl .= '?' . $parsedQuery;
                }

                return $resolvedUrl;
            }

            return $trimmedPath;
        }

        $normalizedPath = ltrim($trimmedPath, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        return rtrim($request->getSchemeAndHttpHost(), '/') . '/storage/' . ltrim($normalizedPath, '/');
    }

    public function show(Request $request)
    {
        $alumni = $request->user();

        return response()->json([
            'alumni' => $this->alumniWithStats($alumni),
        ]);
    }

    public function view(Alumni $alumni)
    {
        return response()->json([
            'alumni' => $this->alumniWithStats($alumni),
        ]);
    }

    public function update(Request $request)
    {
        $alumni = $request->user();

        $validated = $request->validate([
            'first_name' => 'sometimes|filled|string|max:255',
            'middle_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|filled|string|max:255',
            'phone_number' => 'sometimes|nullable|string|max:50',
            'email' => 'sometimes|filled|string|email|max:255|unique:alumnis,email,' . $alumni->id,
            'date_of_birth' => 'sometimes|filled|date',
            'sex' => 'sometimes|filled|string|max:50',
            'alumni_photo' => 'sometimes|nullable|string|max:2048',
            'alumni_bio' => 'sometimes|nullable|string|max:10000',
        ]);

        $alumni->update($validated);

        $freshAlumni = $alumni->fresh();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'alumni' => $this->alumniWithStats($freshAlumni),
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $alumni = $request->user();

        $validated = $request->validate([
            'photo' => 'required|image|max:5120', // max 5MB
        ]);

        $file = $request->file('photo');
        $path = Storage::disk('s3')->putFile('alumni_photos', $file);

        if (!$path) {
            return response()->json([
                'message' => 'Unable to store uploaded photo.',
            ], 500);
        }

        $url = $this->resolveStorageUrl($request, $path);

        $alumni->alumni_photo = $url;
        $alumni->save();

        $freshAlumni = $alumni->fresh();

        return response()->json([
            'message' => 'Photo uploaded',
            'url' => $url,
            'alumni' => $this->alumniWithStats($freshAlumni),
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
            ]);
        }

        $searchTerm = "%{$query}%";
        
        $results = Alumni::query()
            ->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(first_name) like LOWER(?)', [$searchTerm])
                  ->orWhereRaw('LOWER(middle_name) like LOWER(?)', [$searchTerm])
                  ->orWhereRaw('LOWER(last_name) like LOWER(?)', [$searchTerm]);
            })
            ->limit(20)
            ->get()
            ->map(fn($alumni) => [
                'id' => $alumni->id,
                'first_name' => $alumni->first_name,
                'middle_name' => $alumni->middle_name,
                'last_name' => $alumni->last_name,
                'alumni_photo' => $alumni->alumni_photo,
            ])
            ->toArray();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function posts(Request $request, Alumni $alumni)
    {
        $currentAlumniId = $request->user()?->id;

        $posts = Post::with([
            'alumni:id,first_name,last_name,alumni_photo',
            'images:id,post_id,image_path',
        ])
            ->withCount(['reactions', 'comments', 'reposts'])
            ->where('alumni_id', $alumni->id)
            ->where('moderation_status', 'approved')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Post $post) => $this->buildPostPayload($post));

        $reposts = Repost::with([
            'alumni:id,first_name,last_name,alumni_photo',
            'post.alumni:id,first_name,last_name,alumni_photo',
            'post.images:id,post_id,image_path',
        ])
            ->where('alumni_id', $alumni->id)
            ->where('moderation_status', 'approved')
            ->whereHas('post', function ($query) {
                $query->where('moderation_status', 'approved');
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Repost $repost) => $this->buildRepostPayload($repost))
            ->filter()
            ->values();

        $feedItems = $posts
            ->concat($reposts)
            ->sortByDesc(function (array $item) {
                return $item['created_at'];
            })
            ->take(50)
            ->values();

        if ($currentAlumniId) {
            $currentReactionMap = Reaction::query()
                ->whereIn('post_id', $feedItems->pluck('id')->unique()->values())
                ->where('alumni_id', $currentAlumniId)
                ->pluck('reaction', 'post_id');

            $currentRepostPostIds = Repost::query()
                ->whereIn('post_id', $feedItems->pluck('id')->unique()->values())
                ->where('alumni_id', $currentAlumniId)
                ->pluck('post_id')
                ->map(function ($postId) {
                    return (int) $postId;
                })
                ->all();

            $feedItems = $feedItems->map(function (array $item) use ($currentReactionMap, $currentRepostPostIds) {
                $item['my_reaction'] = $currentReactionMap->get($item['id']);
                $item['my_repost'] = in_array((int) $item['id'], $currentRepostPostIds, true);

                return $item;
            });
        }

        $feedItems = $feedItems->map(function (array $item) use ($request) {
            $item['images'] = collect($item['images'])->map(function (array $image) use ($request) {
                $image['image_url'] = $this->resolveImageUrl($request, $image['image_path']);

                return $image;
            })->values();

            return $item;
        });

        return response()->json([
            'posts' => $feedItems,
        ]);
    }

    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', 
            'alumni_id' => 'required|exists:alumnis,id'
        ]);

        if ($request->hasFile('photo')) {
            // Save to the 'alumni_photos' folder on the 'supabase' disk we set up
            $path = $request->file('photo')->store('alumni_photos', 'supabase'); 

            // Find the specific user and update their photo column
            $alumni = Alumni::find($request->alumni_id);
            $alumni->alumni_photo = $path; 
            $alumni->save();

            return response()->json([
                'message' => 'Profile photo successfully updated!',
                'path' => $path
            ]);
        }

        return response()->json(['message' => 'No file provided'], 400);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\ImagesPost;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
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

    public function index(Request $request)
    {
        $currentAlumniId = $request->user()?->id;

        $posts = Post::with([
            'alumni:id,first_name,last_name,alumni_photo',
            'images:id,post_id,image_path',
        ])
            ->withCount(['reactions', 'comments'])
            ->where('moderation_status', 'approved')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function (Post $post) use ($currentAlumniId) {
                return [
                    'id' => $post->id,
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
                    'my_reaction' => null,
                    'images' => $post->images->map(function (ImagesPost $image) {
                        return [
                            'id' => $image->id,
                            'image_path' => $image->image_path,
                        ];
                    })->values(),
                ];
            });

        if ($currentAlumniId) {
            $currentReactionMap = Reaction::query()
                ->whereIn('post_id', $posts->pluck('id'))
                ->where('alumni_id', $currentAlumniId)
                ->pluck('reaction', 'post_id');

            $posts = $posts->map(function (array $post) use ($currentReactionMap) {
                $post['my_reaction'] = $currentReactionMap->get($post['id']);

                return $post;
            });
        }

        $posts = $posts->map(function (array $post) use ($request) {
            $post['images'] = collect($post['images'])->map(function (array $image) use ($request) {
                $image['image_url'] = $this->resolveImageUrl($request, $image['image_path']);

                return $image;
            })->values();

            return $post;
        });

        return response()->json([
            'posts' => $posts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'caption' => 'nullable|string|max:10000',
            'images' => 'sometimes|array|max:10',
            'images.*' => 'image|max:5120',
        ]);

        $post = DB::transaction(function () use ($request, $validated) {
            $caption = isset($validated['caption']) ? trim($validated['caption']) : null;

            if ($caption === '') {
                $caption = null;
            }

            $post = Post::create([
                'alumni_id' => $request->user()->id,
                'caption' => $caption,
                'moderation_status' => 'approved',
            ]);

            foreach ($request->file('images', []) as $imageFile) {
                $storedPath = $imageFile->store('posts', 'public');

                ImagesPost::create([
                    'post_id' => $post->id,
                    'image_path' => $storedPath,
                ]);
            }

            return $post->load('images');
        });

        $images = $post->images->map(function (ImagesPost $image) use ($request) {
            return [
                'id' => $image->id,
                'image_path' => $image->image_path,
                'image_url' => $this->resolveImageUrl($request, $image->image_path),
            ];
        })->values();

        return response()->json([
            'message' => 'Post created successfully.',
            'post' => [
                'id' => $post->id,
                'caption' => $post->caption,
                'created_at' => $post->created_at,
                'alumni' => [
                    'id' => $post->alumni_id,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                    'alumni_photo' => $request->user()->alumni_photo,
                ],
                'comment_count' => 0,
                'reaction_count' => 0,
                'my_reaction' => null,
                'images' => $images,
            ],
        ], 201);
    }

    public function react(Request $request, Post $post)
    {
        $validated = $request->validate([
            'reaction' => 'required|in:like',
        ]);

        $alumniId = $request->user()->id;
        $reactionType = $validated['reaction'];

        $savedReaction = DB::transaction(function () use ($alumniId, $post, $reactionType) {
            $existingReaction = Reaction::query()
                ->where('alumni_id', $alumniId)
                ->where('post_id', $post->id)
                ->first();

            if ($existingReaction && $existingReaction->reaction === $reactionType) {
                $existingReaction->delete();

                return null;
            }

            return Reaction::updateOrCreate(
                [
                    'alumni_id' => $alumniId,
                    'post_id' => $post->id,
                ],
                [
                    'reaction' => $reactionType,
                ]
            );
        });

        $reactionCount = Reaction::query()
            ->where('post_id', $post->id)
            ->count();

        return response()->json([
            'message' => $savedReaction ? 'Reaction saved.' : 'Reaction removed.',
            'reaction_count' => $reactionCount,
            'my_reaction' => $savedReaction?->reaction,
        ]);
    }

    public function comment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:10000',
        ]);

        $comment = DB::transaction(function () use ($request, $post, $validated) {
            return Comment::create([
                'alumni_id' => $request->user()->id,
                'post_id' => $post->id,
                'comment' => trim($validated['comment']),
                'moderation_status' => 'approved',
            ]);
        });

        $commentCount = Comment::query()
            ->where('post_id', $post->id)
            ->count();

        return response()->json([
            'message' => 'Comment saved.',
            'comment' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at,
                'comment_count' => $commentCount,
            ],
        ], 201);
    }

    public function comments(Request $request, Post $post)
    {
        $comments = Comment::with(['alumni:id,first_name,last_name,alumni_photo'])
            ->where('post_id', $post->id)
            ->where('moderation_status', 'approved')
            ->orderBy('created_at')
            ->get()
            ->map(function (Comment $comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at,
                    'alumni' => [
                        'id' => $comment->alumni?->id,
                        'first_name' => $comment->alumni?->first_name,
                        'last_name' => $comment->alumni?->last_name,
                        'alumni_photo' => $comment->alumni?->alumni_photo,
                    ],
                ];
            });

        return response()->json([
            'comments' => $comments,
        ]);
    }
}
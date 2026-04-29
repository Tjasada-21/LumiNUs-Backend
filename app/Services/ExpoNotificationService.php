<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for sending push notifications via Expo
 * 
 * Handles sending notifications to mobile app users
 * through Expo's push notification service
 */
class ExpoNotificationService
{
    const EXPO_API_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send notifications to multiple tokens
     */
    public static function send(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return;
        }

        $messages = array_map(function ($token) use ($title, $body, $data) {
            return [
                'to' => $token,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'badge' => 1,
            ];
        }, $tokens);

        try {
            Http::post(self::EXPO_API_URL, $messages);
            Log::info('Push notifications sent', ['count' => count($tokens)]);
        } catch (\Exception $e) {
            Log::error('Failed to send Expo notifications', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification to specific user
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        $tokens = $user->deviceTokens()
            ->pluck('push_token')
            ->toArray();

        self::send($tokens, $title, $body, $data);
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToUsers($users, string $title, string $body, array $data = [])
    {
        if (is_array($users)) {
            $users = collect($users);
        }

        $tokens = $users
            ->pluck('deviceTokens')
            ->flatten()
            ->pluck('push_token')
            ->toArray();

        self::send($tokens, $title, $body, $data);
    }

    /**
     * Send notification to all users
     */
    public static function sendToAll(string $title, string $body, array $data = [])
    {
        $tokens = \DB::table('device_tokens')
            ->pluck('push_token')
            ->toArray();

        self::send($tokens, $title, $body, $data);
    }

    /**
     * Send event notification
     */
    public static function sendEventNotification(object $event, array $userIds = null)
    {
        $title = "New Event: {$event->title}";
        $body = $event->description ?: 'A new event has been created';
        
        $data = [
            'type' => 'event',
            'eventId' => (string) $event->id,
            'screen' => 'EventDetailsScreen',
        ];

        if ($userIds) {
            $users = User::whereIn('id', $userIds)->get();
            self::sendToUsers($users, $title, $body, $data);
        } else {
            self::sendToAll($title, $body, $data);
        }
    }

    /**
     * Send announcement notification
     */
    public static function sendAnnouncementNotification(object $announcement, array $userIds = null)
    {
        $title = 'New Announcement';
        $body = $announcement->content ?: 'You have a new announcement';
        
        $data = [
            'type' => 'announcement',
            'announcementId' => (string) $announcement->id,
            'screen' => 'HomeScreen',
        ];

        if ($userIds) {
            $users = User::whereIn('id', $userIds)->get();
            self::sendToUsers($users, $title, $body, $data);
        } else {
            self::sendToAll($title, $body, $data);
        }
    }

    /**
     * Send message notification
     */
    public static function sendMessageNotification(object $message, User $receiver, User $sender)
    {
        $title = "New Message from {$sender->first_name}";
        $body = $message->content ?: 'You have a new message';
        
        $data = [
            'type' => 'message',
            'messageId' => (string) $message->id,
            'conversationId' => (string) $sender->id,
            'screen' => 'ConvoScreen',
        ];

        self::sendToUser($receiver, $title, $body, $data);
    }

    /**
     * Send perk notification
     */
    public static function sendPerkNotification(object $perk, array $userIds = null)
    {
        $title = 'New Perk Available';
        $body = $perk->title ?: 'A new perk is available';
        
        $data = [
            'type' => 'perk',
            'perkId' => (string) $perk->id,
            'screen' => 'PerksScreen',
        ];

        if ($userIds) {
            $users = User::whereIn('id', $userIds)->get();
            self::sendToUsers($users, $title, $body, $data);
        } else {
            self::sendToAll($title, $body, $data);
        }
    }

    /**
     * Send reaction notification
     */
    public static function sendReactionNotification(
        object $reaction,
        User $postOwner,
        User $reactor,
        object $post
    ) {
        $title = "{$reactor->first_name} reacted to your post";
        $body = "{$reaction->reaction} {$post->content}";
        
        $data = [
            'type' => 'reaction',
            'reactionId' => (string) $reaction->id,
            'postId' => (string) $post->id,
            'screen' => 'UserFeedScreen',
        ];

        self::sendToUser($postOwner, $title, $body, $data);
    }

    /**
     * Send comment notification
     */
    public static function sendCommentNotification(
        object $comment,
        User $postOwner,
        User $commenter,
        object $post
    ) {
        $title = "{$commenter->first_name} commented on your post";
        $body = substr($comment->content ?? '', 0, 50);
        
        $data = [
            'type' => 'comment',
            'commentId' => (string) $comment->id,
            'postId' => (string) $post->id,
            'screen' => 'UserFeedScreen',
        ];

        self::sendToUser($postOwner, $title, $body, $data);
    }

    /**
     * Send repost notification
     */
    public static function sendRepostNotification(
        object $repost,
        User $postOwner,
        User $reposter,
        object $post
    ) {
        $title = "{$reposter->first_name} reposted your post";
        $body = substr($post->content ?? '', 0, 50);
        
        $data = [
            'type' => 'repost',
            'repostId' => (string) $repost->id,
            'postId' => (string) $post->id,
            'screen' => 'UserFeedScreen',
        ];

        self::sendToUser($postOwner, $title, $body, $data);
    }
}

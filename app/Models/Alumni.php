<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Upgraded from standard Model
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Allows token generation

class Alumni extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = [
        'bio',
    ];

    // Explicitly define the table name
    protected $table = 'alumnis'; 

    // Allow these fields to be filled via API requests
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'sex',
        'year_graduated',
        'program',
        'alumni_photo',
        'alumni_bio',
        'student_id_number',
        'email',
        'phone_number',
        'password_hash',
        'card_photo',
        'verification_status',
    ];

    // Hide the password when returning JSON data
    protected $hidden = [
        'password_hash',
    ];

    // Tell Laravel to use your custom 'password_hash' column instead of the default 'password'
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getBioAttribute(): ?string
    {
        return $this->alumni_bio;
    }

    public function getAlumniPhotoAttribute($value): ?string
    {
        $trimmedValue = trim((string) $value);

        if ($trimmedValue === '') {
            return $trimmedValue;
        }

        if (preg_match('/^https?:\/\//i', $trimmedValue)) {
            $resolvedUrl = $trimmedValue;
        } else {
            $parsedPath = parse_url($trimmedValue, PHP_URL_PATH);
            $normalizedPath = ltrim((string) ($parsedPath ?: $trimmedValue), '/');
            $bucketName = trim((string) config('filesystems.disks.supabase.bucket', ''), '/');
            $publicBaseUrl = rtrim((string) config('filesystems.disks.supabase.url', ''), '/');

            if ($bucketName !== '' && str_starts_with($normalizedPath, $bucketName . '/')) {
                $normalizedPath = substr($normalizedPath, strlen($bucketName) + 1);
            }

            if ($publicBaseUrl !== '' && $bucketName !== '') {
                $resolvedUrl = $publicBaseUrl . '/' . $bucketName . '/' . ltrim($normalizedPath, '/');
            } else {
                $resolvedUrl = '/' . ltrim($normalizedPath, '/');
            }
        }

        $version = $this->updated_at?->timestamp;
        if (!$version) {
            return $resolvedUrl;
        }

        $separator = str_contains($resolvedUrl, '?') ? '&' : '?';

        return $resolvedUrl . $separator . 'v=' . $version;
    }
}
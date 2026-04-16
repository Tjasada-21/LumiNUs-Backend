<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Upgraded from standard Model
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Allows token generation

class Alumni extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'alumni_photo',
        'alumni_bio',
        'student_id_number',
        'email',
        'phone_number',
        'password_hash',
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
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'invited_by',
        'expires_at',
        'accepted_at'
    ];

    protected $dates = [
        'expires_at',
        'accepted_at'
    ];

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
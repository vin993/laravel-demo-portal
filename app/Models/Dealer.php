<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function media()
    {
        return $this->morphToMany(MediaFile::class, 'accessible', 'media_access');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaView extends Model
{
    protected $fillable = ['media_file_id', 'user_id', 'ip_address'];

    public function media()
    {
        return $this->belongsTo(MediaFile::class, 'media_file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
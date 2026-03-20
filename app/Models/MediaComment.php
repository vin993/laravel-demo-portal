<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaComment extends Model
{
    use SoftDeletes;

    protected $fillable = ['media_file_id', 'user_id', 'comment', 'is_approved'];

    public function media()
    {
        return $this->belongsTo(MediaFile::class, 'media_file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAccess extends Model
{
    protected $fillable = ['media_file_id', 'accessible_type', 'accessible_id', 'access_type'];

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class);
    }

    public function accessible()
    {
        return $this->morphTo();
    }
}
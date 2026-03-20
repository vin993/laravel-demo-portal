<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function media()
    {
        return $this->morphToMany(MediaFile::class, 'accessible', 'media_access');
    }
}
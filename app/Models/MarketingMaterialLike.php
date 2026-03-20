<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingMaterialLike extends Model
{
    use SoftDeletes;

    protected $fillable = ['marketing_material_id', 'user_id'];

    public function material()
    {
        return $this->belongsTo(MarketingMaterial::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
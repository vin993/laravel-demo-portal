<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingMaterialView extends Model
{
    protected $table = 'mm_views';
    protected $fillable = ['marketing_material_id', 'user_id', 'ip_address'];

    public function material()
    {
        return $this->belongsTo(MarketingMaterial::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingMaterialTag extends Model
{
    use SoftDeletes;

    protected $table = 'mm_tags';  
    protected $fillable = ['name'];

    public function materials()
    {
        return $this->belongsToMany(MarketingMaterial::class, 'mm_tag_pivot', 'tag_id', 'material_id');
    }
}
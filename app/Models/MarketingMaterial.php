<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingMaterial extends Model {
	use SoftDeletes;

	protected $fillable = [
		'title',
		'description',
		'file_path',
		'thumbnail_path',
		'medium_path',
		'file_type',
		'mime_type',
		'size',
		'thumbnail_size',
		'width',
		'height',
		'is_featured',
		'uploaded_by',
		'created_by'
	];

	protected $casts = [
		'metadata' => 'array',
		'is_featured' => 'boolean'
	];

	public function groups() {
		return $this->belongsToMany(MarketingMaterialGroup::class, 'mm_group_pivot', 'material_id', 'group_id');
	}

	public function tags() {
		return $this->belongsToMany(MarketingMaterialTag::class, 'mm_tag_pivot', 'material_id', 'tag_id');
	}

	public function industries() {
		return $this->belongsToMany(Industry::class, 'mm_industry_pivot', 'material_id', 'industry_id');
	}

	public function dealers() {
		return $this->belongsToMany(Dealer::class, 'mm_dealer_pivot', 'material_id', 'dealer_id');
	}

	public function manufacturers() {
		return $this->belongsToMany(Manufacturer::class, 'mm_manufacturer_pivot', 'material_id', 'manufacturer_id');
	}

	public function companies() {
		return $this->belongsToMany(Company::class, 'mm_company_pivot', 'material_id', 'company_id');
	}

	public function likes() {
		return $this->hasMany(MarketingMaterialLike::class, 'material_id');
	}

	public function views() {
		return $this->hasMany(MarketingMaterialView::class, 'material_id');
	}
}

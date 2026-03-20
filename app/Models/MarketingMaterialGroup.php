<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingMaterialGroup extends Model {
	use SoftDeletes;

	protected $fillable = ['name', 'description', 'created_by'];

	public function materials() {
		return $this->belongsToMany(MarketingMaterial::class, 'mm_group_pivot', 'group_id', 'material_id')->withTimestamps();
	}

	public function files() {
		return $this->belongsToMany(MarketingMaterial::class, 'mm_group_pivot', 'group_id', 'material_id')->withTimestamps();
	}
	public function creator() {
		return $this->belongsTo(User::class, 'created_by');
	}

	public function industries() {
		return $this->belongsToMany(Industry::class, 'mm_industry_pivot');
	}

	public function dealers() {
		return $this->belongsToMany(Dealer::class, 'mm_dealer_pivot');
	}

	public function manufacturers() {
		return $this->belongsToMany(Manufacturer::class, 'mm_manufacturer_pivot');
	}
}

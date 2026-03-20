<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaGroup extends Model {
	protected $fillable = ['name', 'description', 'created_by'];

	public function files() {
		return $this->belongsToMany(MediaFile::class, 'media_file_group')->withTimestamps();
	}

	public function creator() {
		return $this->belongsTo(User::class, 'created_by');
	}
	public function industries() {
		return $this->belongsToMany(Industry::class, 'media_file_industry');
	}

	public function dealers() {
		return $this->belongsToMany(Dealer::class, 'media_file_dealer');
	}

	public function manufacturers() {
		return $this->belongsToMany(Manufacturer::class, 'media_file_manufacturer');
	}

}

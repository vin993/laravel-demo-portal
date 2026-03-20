<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model {
	use HasFactory;

	protected $fillable = [
		'title',
		'image_path',
		'status',
		'published_at'
	];

	protected $casts = [
		'published_at' => 'datetime',
		'status' => 'boolean'
	];

	public function industries() {
		return $this->belongsToMany(Industry::class);
	}

	public function dealers() {
		return $this->belongsToMany(Dealer::class);
	}

	public function manufacturers() {
		return $this->belongsToMany(Manufacturer::class);
	}

	public function users() {
		return $this->belongsToMany(User::class);
	}
}

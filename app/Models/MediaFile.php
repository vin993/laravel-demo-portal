<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFile extends Model {
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
		'uploaded_by',
		'is_featured',
		'sort_order',
		'metadata'
	];

	protected $casts = [
		'is_featured' => 'boolean',
		'metadata' => 'array',
		'size' => 'integer',
		'sort_order' => 'integer'
	];

	public function groups() {
		return $this->belongsToMany(MediaGroup::class, 'media_file_group')->withTimestamps();
	}
	public function group() {
		return $this->belongsTo(MediaGroup::class, 'group_id');
	}


	public function tags() {
		return $this->belongsToMany(MediaTag::class, 'media_file_tag')->withTimestamps();
	}

	public function industries() {
		return $this->belongsToMany(Industry::class, 'media_file_industry')->withTimestamps();
	}

	public function dealers() {
		return $this->belongsToMany(Dealer::class, 'media_file_dealer')->withTimestamps();
	}

	public function manufacturers() {
		return $this->belongsToMany(Manufacturer::class, 'media_file_manufacturer')->withTimestamps();
	}

	public function uploader() {
		return $this->belongsTo(User::class, 'uploaded_by');
	}
	public function companies() {
		return $this->belongsToMany(Company::class, 'media_file_company')->withTimestamps();
	}

	public function comments() {
		return $this->hasMany(MediaComment::class);
	}

	public function likes() {
		return $this->hasMany(MediaLike::class);
	}

	public function views() {
		return $this->hasMany(MediaView::class);
	}

	public function isLikedByUser($userId) {
		return $this->likes()->where('user_id', $userId)->exists();
	}

	public function viewCount() {
		return $this->views()->count();
	}

	public function uniqueViewCount() {
		return $this->views()->distinct('user_id')->count();
	}
}

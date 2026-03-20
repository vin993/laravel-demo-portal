<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
	use HasApiTokens, Notifiable, HasFactory;

	protected $fillable = [
		'name',
		'email',
		'password',
		'role_id',
		'is_approved',
		'is_active',
		'is_primary',
		'primary_user_since',
		'primary_set_by',
		'invited_by',
		'last_login_at'

	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $casts = [
		'email_verified_at' => 'datetime',
		'password' => 'hashed',
		'is_approved' => 'boolean',
		'is_active' => 'boolean',
		'primary_user_since' => 'datetime',
		'last_login_at' => 'datetime',
	];

	public function checkInactivity() {
		if ($this->last_login_at && $this->last_login_at->addDays(90)->isPast()) {
			$this->is_active = false;
			$this->save();
			return true;
		}
		return false;
	}

	public function userDetail() {
		return $this->hasOne(UserDetail::class);
	}

	public function role() {
		return $this->belongsTo(Role::class);
	}

	public function approvedBy() {
		return $this->belongsTo(User::class, 'approved_by');
	}

	public function isSuperAdmin() {
		return $this->role && $this->role->name === 'Super Admin';
	}

	public function isAdmin() {
		return $this->role && $this->role->name === 'Admin';
	}

	public function isUser() {
		return $this->role && $this->role->name === 'User';
	}

	public function canLogin() {
		return $this->is_approved && $this->is_active;
	}

	public function industries() {
		return $this->belongsToMany(Industry::class);
	}

	public function dealers() {
		return $this->belongsToMany(Dealer::class);
	}

	public function manufacturers() {
		return $this->belongsToMany(Manufacturer::class);
	}

	public function companies() {
		return $this->belongsToMany(Company::class);
	}

	public function primarySetBy() {
		return $this->belongsTo(User::class, 'primary_set_by');
	}

	public function invitedBy() {
		return $this->belongsTo(User::class, 'invited_by');
	}

	public function secondaryUsers() {
		return $this->hasMany(User::class, 'invited_by');
	}
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code'
    ];

    protected $casts = [
        'country' => 'string',
        'state' => 'string',
        'city' => 'string'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_name', 'id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city');
    }
}
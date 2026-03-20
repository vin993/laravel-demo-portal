<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'legal_name',
        'tax_number',
        'registration_number',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'logo',
        'currency',
        'timezone',
        'about'
    ];
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
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
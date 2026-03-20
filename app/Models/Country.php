<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'code', 'phone_code', 'currency_code', 'currency_name', 'currency_symbol'];

    public function states()
    {
        return $this->hasMany(State::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    // Your DB table is named 'cities_tabel'
    protected $table = 'cities_tabel';

    protected $fillable = ['name', 'state_id'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'city_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    // Your DB table is named 'state'
    protected $table = 'states';

    protected $fillable = ['name'];

    public function cities()
    {
        return $this->hasMany(City::class, 'state_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'state_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = ['name', 'slug'];

    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    protected $table='contactus_data';
     protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table='payments_data';
      protected $fillable = [
        'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature',
        'amount', 'currency', 'status',
        'season_id', 'player1_id', 'player2_id', 'registration_type',
    ];
    
}

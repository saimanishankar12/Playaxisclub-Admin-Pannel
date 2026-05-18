<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $table = 'players';

    protected $fillable = [
        'player_id',
        'season_id',
        'name',
        'email',
        'phone',
        'state_id',
        'address',
        'city_id',
        'age',
        'sport',
        'gender',
        'tshirt_size',
        'payment_status',
        'aadhar_proof',
        'profile_photo',
        'mode',           // 'singles' or 'doubles'
    ];

    /* ── Relationships ── */

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function doublesPairAsPlayer1()
    {
        return $this->hasOne(DoublesPair::class, 'player1_id');
    }

    public function doublesPairAsPlayer2()
    {
        return $this->hasOne(DoublesPair::class, 'player2_id');
    }

    /* ── Accessors ── */

    // Returns full public URL for profile photo
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : '';
    }

    // Returns full public URL for aadhar proof
    public function getAadharProofUrlAttribute(): string
    {
        return $this->aadhar_proof
            ? asset('storage/' . $this->aadhar_proof)
            : '';
    }
}
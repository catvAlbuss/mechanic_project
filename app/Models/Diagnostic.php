<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    protected $fillable = [
        'id_user',
        'id_reservation',
        'description',
        'registration_date',
        'cost_aprox',
    ];
    
    public function user(){
        return $this->belongsTo(User::class,'id_user');
    }

    public function reservation(){
        return $this->belongsTo(Reservation::class,'id_user');
    }
    
}

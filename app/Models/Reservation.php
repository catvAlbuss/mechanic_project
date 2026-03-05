<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    //
    protected $fillable = [
        'id_vehicle',
        'reservation_date',
        'description',
        'state'
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class, 'id_vehicle');
    }
}

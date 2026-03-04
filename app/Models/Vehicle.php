<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
     protected $fillable = [
     'id_user',
     'plate',
     'tipe',
     'vin',
     'engine',
     'color',
     'brand',
     'year',
     'mileage',
     'model',
     'state',

 ];
 public function user(){
    return $this->belongsTo(User::class, 'id_user');
 }
}

 


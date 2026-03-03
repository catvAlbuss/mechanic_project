<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
        'id_provider',
        'name',
        'brand',
        'description',
        'state'
    ];

    public function provider(){
        return $this->belongsTo(Provider::class, 'id_provider');
    }
}

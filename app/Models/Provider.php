<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    //
    protected $fillable = [
        'id_company',
        'ruc',
        'company_name',
        'address',
        'email',
        'contact',
        'state',
        'registration_date',
    ];

    public function company(){
        return $this->belongsTo(Company::class,'id_company');
    }
}

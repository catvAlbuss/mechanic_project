<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $fillable = [
        'avatar',
        'ruc',
        'company_name',
        'address',
        'district',
        'province',
        'department',
        'state',
        'registration_date',
        'config'
    ];
}

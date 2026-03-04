<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'id',
        'id_diagnostic',
        'id_user',
        'orden_service',
        'check_in_date',
        'check_out_date',
    ];
    public function diagnostic(){
        return $this->belongsTo(Diagnostic::class, 'id_diagnostic');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }
}

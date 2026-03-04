<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class Sale extends Model
{
    protected $fillable = [
        'id_user',
        'id_component',
        'id_service',
        'num_voucher',
        'type_voucher',
        'payment_method',
        'payment_state',
        'quantity',
        'igv',
        'total',
        'registration_date',
        'descuento',
    ];
    
    public function user(){
        return $this->belongsTo(User::class,'id_user');
    }

    public function component(){
        return $this->belongsTo(Component::class,'id_component');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'id_service');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
   use HasFactory,SoftDeletes;

    protected $table = "payments";
    protected $fillable=[
        'payment_id',
        'type',
        'reference_id',
        'amount',
        'fees',
        'total',
        'payment_mode',
        'status',
        'appointment_id',
        'consultation_fees',
        'booking_fees',
        'total_amount'
    ];

    protected $dates = [ 'deleted_at' ];
    
}

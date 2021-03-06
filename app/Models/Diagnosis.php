<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnosis extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "diagnoses";
    protected $fillable=[
        'name',
        'instruction',
        'status',
        'appointment_id',
    ];

    protected $dates = [ 'deleted_at' ];
}

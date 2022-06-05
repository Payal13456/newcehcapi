<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EyeDetail  extends Model
{
    use HasFactory;

    protected $table = "eye_details";
    protected $fillable=[
        'prescription_id',
        'dsph',
        'dcyl',
        'axis',
        'va',
        'eye_details',
        'type'
    ];
}

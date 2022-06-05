<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Medicine;
use App\Models\EyeDetail;

class Prescription extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "prescriptions";
    protected $fillable=[
        'medicine_type',
        'medicine_id',
        'days',
        'timing',
        'instruction',
        'status',
        'appointment_id',
        'food',
        'mg_ml',
        'duration'
    ];

    protected $dates = [ 'deleted_at' ];

    public function medicine(){
        return $this->belongsTo(Medicine::class,'medicine_id','id')->select('id','medicine_name','generic_name');
    }

    public function eye_details(){
        return $this->hasMany(EyeDetail::class,'prescription_id','id');
    }
}

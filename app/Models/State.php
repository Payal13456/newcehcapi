<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserInfomation;
use App\Models\Hospital;
use App\Models\Patient;

class State extends Model
{
    use HasFactory;
    protected $table="state";

    protected $fillable = ['state_name'];

      public function userInfo(){
        return $this->hasOne(UserInfomation::class,'state_id','id');
    }

      public function hospital(){
        return $this->hasOne(Hospital::class,'state_id','id');
    }
     public function patient(){
        return $this->hasOne(Patient::class,'state_id','id');
    }
}

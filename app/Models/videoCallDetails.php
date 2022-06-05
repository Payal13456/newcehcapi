<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class videoCallDetails extends Model
{
    use HasFactory;
    protected $table ="video_call_details";
    protected $fillable=['appointment_id','token','channel_name'];
}

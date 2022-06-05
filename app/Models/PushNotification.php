<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserInfomation;
use App\Models\Patient;

class PushNotification extends Model
{
    use HasFactory;

    protected $table = "push_notification";
    protected $fillable=[
        'sender_id',
        'receiver_id',
        'post_id',
        'ref_table',
        'notification_type',
        'message',
        'is_read'
    ];

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                	'sender_id'=> 'required',
			        'receiver_id'=> 'required',
			        'post_id'=> 'required',
			        'ref_table'=> 'required',
			        'notification_type'=> 'required',
                    'message'=> 'required'  
                ];
                break;
            case 'update':
                $params = [
                	'sender_id'=> 'required',
			        'receiver_id'=> 'required',
			        'post_id'=> 'required',
			        'ref_table'=> 'required',
			        'notification_type'=> 'required',
                    'message'=> 'required'    
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function patient(){
     return $this->hasOne(Patient::class,'id','patient_id');
    }

    public function doctor(){
        return $this->hasOne(UserInfomation::class,'user_id','doctor_id');
    }
    
}

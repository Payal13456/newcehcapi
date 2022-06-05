<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserInfomation;

class Schedule extends Model
{
    use HasFactory;

    use HasFactory,SoftDeletes;

    protected $table = "schedules";
    protected $fillable=[
        'doctor_id',
        'day',
        'start_time',
        'end_time',
        'break',
        'type',
        'appointment_mode',
        'is_disable',
        'status'
    ];

     protected $dates = [ 'deleted_at' ];

     public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                    'doctor_id'=> 'required',
			        'day'=> 'required',
			        'start_time'=> 'required',
			        'end_time'=> 'required',
			        'break'=> 'required',
                    'type'=> 'required',
                    'is_disable' => 'required',
                    'appointment_mode' => 'required'
                ];
                break;
            case 'update':
                $params = [
                    'doctor_id'=> 'required',
                    'day'=> 'required',
                    'start_time'=> 'required',
                    'end_time'=> 'required',
                    'break'=> 'required',
                    'type'=> 'required',
                    'is_disable' => 'required',
                    'appointment_mode' => 'required'
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $faq){
        
        $preData['doctor_id'] = array_key_exists('doctor_id', $data) ? $data['doctor_id'] : $faq['doctor_id'];
        $preData['day'] = array_key_exists('day', $data) ? $data['day'] : $faq['day'];
        $preData['start_time'] = array_key_exists('start_time', $data) ? $data['start_time'] :  $faq['start_time'];
        $preData['end_time'] = array_key_exists('end_time', $data) ? $data['end_time'] : $faq['end_time'];
        $preData['break'] = array_key_exists('break', $data) ? $data['break'] : $faq['break'];
        $preData['type'] = array_key_exists('type', $data) ? $data['type'] : $faq['type'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] :  $faq['status'];
        $preData['is_disable'] = array_key_exists('is_disable', $data) ? $data['is_disable'] :  $faq['is_disable'];
        $preData['appointment_mode'] = array_key_exists('appointment_mode', $data) ? $data['appointment_mode'] :  $faq['appointment_mode'];
       
        return $preData;
    }

     public function prepareCreateData(array $data)
     {
        $preData['doctor_id'] =  $data['doctor_id'] ;
        $preData['day'] =  $data['day'] ;
        $preData['start_time'] =  $data['start_time'] ;
        $preData['end_time'] =  $data['end_time'] ;
        $preData['break'] =  $data['break'] ;
        $preData['type'] =  $data['type'];
        $preData['is_disable'] =  $data['is_disable'];
        $preData['appointment_mode'] = $data['appointment_mode'];
        return $preData;
    }

    public function userInfo(){
        return $this->hasOne(UserInfomation::class,'user_id','doctor_id');
    }
}

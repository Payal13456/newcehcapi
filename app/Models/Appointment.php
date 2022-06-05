<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserInfomation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\CaseSummary;
use App\Models\Diagnosis;
use App\Models\Specialization;
use App\Models\Schedule;
use App\Models\Payment;
use App\Models\EyeDetail;

class Appointment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "appointments";
    protected $fillable=[
        'patient_id',
        'doctor_id',
        'schedule_id',
        'schedule_date',
        'specification_id',
        'slot_timing',
        'type',
        'is_cancel',
        'status',
        'description',
        'cancelled_by',
        'is_finished',
        'is_attended'
    ];

     protected $dates = [ 'deleted_at' ];
     public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                	'patient_id'=> 'required',
			        'doctor_id'=> 'required',
			        'schedule_date'=> 'required' ,
			        'specification_id'=> 'required' ,
			        'slot_timing'=> 'required' ,
			        'type'=> 'required' ,
                    'description' => ''
                ];
                break;
            case 'update':
                $params = [
                    'patient_id'=> 'required',
			        'doctor_id'=> 'required',
			        'schedule_date'=> 'required' ,
			        'specification_id'=> 'required' ,
			        'slot_timing'=> 'required' ,
			        'type'=> 'required' ,
                    'description' => ''
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $faq){
        
        $preData['patient_id'] = array_key_exists('patient_id', $data) ? $data['patient_id'] : $faq['patient_id'];
        $preData['doctor_id'] = array_key_exists('doctor_id', $data) ? $data['doctor_id'] : $faq['doctor_id'];
        $preData['schedule_date'] = array_key_exists('schedule_date', $data) ? $data['schedule_date'] : $faq['schedule_date'];
        $preData['specification_id'] = array_key_exists('specification_id', $data) ? $data['specification_id'] : $faq['specification_id'];
         $preData['slot_timing'] = array_key_exists('slot_timing', $data) ? date("H:i:00",strtotime($data['slot_timing'])) : $faq['slot_timing'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] :  $faq['status'];
        $preData['type'] = array_key_exists('type', $data) ? $data['type'] :  $faq['type'];
        $preData['description'] = array_key_exists('description', $data) ? $data['description'] :  $faq['description'];
        return $preData;
    }

     public function prepareCreateData(array $data)
     {
        $preData['patient_id'] =  $data['patient_id'] ;
        $preData['doctor_id'] =  $data['doctor_id'] ;
        $preData['schedule_date'] =  $data['schedule_date'] ;
        $preData['specification_id'] =  $data['specification_id'] ;
        $preData['slot_timing'] =  date("H:i:00",strtotime($data['slot_timing'])) ;
        $preData['type'] =  $data['type'] ;
        $preData['description'] =  $data['description'] ;
        return $preData;
    }

    public function patient(){
     return $this->hasOne(Patient::class,'id','patient_id');
    }

    public function doctor(){
        return $this->hasOne(UserInfomation::class,'user_id','doctor_id');
    }

    public function specialization(){
        return $this->hasOne(Specialization::class,'id','specification_id');
    }

    public function schedule(){
        return $this->hasOne(Schedule::class,'id','schedule_id');
    }

    public function payment(){
        return $this->hasOne(Payment::class,'id','payment_id');
    }

    public function notes(){
        return $this->hasOne(CaseSummary::class,'appointment_id');
    }
    public function prescription(){
        return $this->hasMany(Prescription::class,'appointment_id');
    }
    public function diagnosis(){
        return $this->hasMany(Diagnosis::class,'appointment_id');
    }
    public function optics(){
        return $this->hasMany(EyeDetail::class,'appointment_id');
    }
    public function cancelledBy(){
    	return $this->hasOne(User::class,'id','cancelled_by');
    }
};

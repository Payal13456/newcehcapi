<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\BlogCategory;
use Validator;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\BlogFiles;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\UserInfomation;
use DB;
use App\Models\Payment;
use App\Http\Controllers\API\PushNotificationController;
use Mail;
use Illuminate\Support\Facades\Log;

class AppointmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        try{
            $blogs = Appointment::with('patient','patient.user','doctor','doctor.user','specialization','payment','notes','prescription','diagnosis','prescription.medicine','optics','cancelledBy','cancelledBy.role')->where(function($query){
             $query->where('schedule_date','>=',date("Y-m-d"));
             
            })->where('is_finished',0)->orderBy('schedule_date','ASC')->orderBy('slot_timing','ASC')->get();

            
            if($blogs->count() > 0){
                foreach ($blogs as $key => $value) {
                    if($value->patient->type_of_patient == 1){
                $value->payment_status = "Free Patient";
            }else{
                if($value->payment_id == 0){
                    $value->payment_status = "Pending";
                } elseif($value->payment != null){
                    if($value->payment->is_refund == 1){
                        $value->payment_status = "Refunded";
                    }else{
                        $value->payment_status = "Paid";
                    }
                }
            }
                }
                return $this->sendResponse($blogs,"List of all Appointment.");
            }
            return $this->sendError('Appointment is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function appointmentlist($id)
    {
        try{
            $blogs = Appointment::with('patient','patient.user','doctor','doctor.user','specialization','payment','notes','prescription','diagnosis','prescription.medicine','optics','cancelledBy','cancelledBy.role')->where('doctor_id',$id)->where(function($query){
             $query->where('schedule_date','>=',date("Y-m-d"));
             
            })->where('is_finished',0)->orderBy('schedule_date','ASC')->orderBy('slot_timing','ASC')->get();

            if($blogs->count() > 0){
                foreach ($blogs as $key => $value) {
                    if($value->patient->type_of_patient == 1){
                $value->payment_status = "Free Patient";
            }else{
                if($value->payment_id == 0){
                    $value->payment_status = "Pending";
                } elseif($value->payment != null){
                    if($value->payment->is_refund == 1){
                        $value->payment_status = "Refunded";
                    }else{
                        $value->payment_status = "Paid";
                    }
                }
            }
                }
                return $this->sendResponse($blogs,"List of all Appointment.");
            }
             return $this->sendError('Appointment is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function appointmentHistory($id)
    {
        try{
            $blogs = Appointment::with('patient','patient.user','doctor','doctor.user','specialization','payment','notes','prescription','diagnosis','prescription.medicine','optics','cancelledBy','cancelledBy.role')->where('doctor_id',$id)
            ->where(function($query){
             $query->where('schedule_date','<',date("Y-m-d"));
             $query->orWhere('is_finished',1);
            })->orderBy('schedule_date','DESC')->orderBy('slot_timing','ASC')->get();
            if($blogs->count() > 0){
                foreach ($blogs as $key => $value) {
                    if($value->patient->type_of_patient == 1){
                $value->payment_status = "Free Patient";
            }else{
                if($value->payment_id == 0){
                    $value->payment_status = "Pending";
                } elseif($value->payment != null){
                    if($value->payment->is_refund == 1){
                        $value->payment_status = "Refunded";
                    }else{
                        $value->payment_status = "Paid";
                    }
                }
            }
                }
                return $this->sendResponse($blogs,"List of all Appointment History.");
            }
            
            return $this->sendError('Appointment is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{

            $user_id = auth('api')->user()->id;
            $inputs = $request->input();
            $Appointment = new Appointment();
            $rulesParams = $Appointment->requiredRequestParams('create');
            Log::info("LOG INFO".$inputs['slot_timing']);
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }

            $check = Appointment::where('schedule_date',$inputs['schedule_date'])->where("slot_timing",$inputs['slot_timing'])->where("doctor_id",$inputs['doctor_id'])->where("is_cancel",0)->where('status',0)->first();

            if(empty($check)){
                $createData = $Appointment->prepareCreateData($inputs);
                $AppointmentData = $Appointment->create($createData);
                
                $patient = Patient::find($inputs['patient_id']);
                
                if($patient->type_of_patient != 1 && isset($inputs['razorpay_payment_id'])){
                    $RazorPaymentController = new RazorPaymentController;
                    $RazorPaymentController->paymentForApp($AppointmentData->id,$inputs['type'],$inputs['razorpay_payment_id']);
                }
                
                $user = User::find($patient->user_id);
                $doctor = User::where('id',$inputs['doctor_id'])->first();
                $msg = $patient->first_name." ".$patient->last_name ." booked an appointment for ". date("d M Y",strtotime($inputs['schedule_date'])). " @ ".date("H:i A",strtotime($inputs['slot_timing']));

                $notification = [
                    "sender_id" => $inputs['patient_id'],
                    "receiver_id" => $inputs['doctor_id'],
                    "post_id" => $AppointmentData->id,
                    "ref_table" => 'appointments',
                    "notification_type" => "create_appointment",
                    "message" => $msg
                ];

                PushNotification::create($notification);

                $notification1 = [
                    "sender_id" => $inputs['doctor_id'],
                    "receiver_id" => $inputs['patient_id'],
                    "post_id" => $AppointmentData->id,
                    "ref_table" => 'appointments',
                    "notification_type" => "create_appointment",
                    "message" => $msg
                ];

                PushNotification::create($notification1);

                //$user = ['name'=>$doctor->name, 'message' => "Appointment is created with ".$patient->first_name." ".$patient->last_name . ' on '. date("d M Y",strtotime($inputs['schedule_date'])). " @ ".date("H:i A",strtotime($inputs['slot_timing']))];
         $doctor = UserInfomation::where('user_id',$inputs['doctor_id'])->first();
                $subject = "CEHC - Online Video Consultation";
                $to = $patient->email_address;
                $data = ['patient'=>$patient,'doctor'=>$doctor,'date'=>$inputs['schedule_date'],'time'=>$inputs['slot_timing']];
                Mail::send('emails.appointment', $data, function($message) use ($to, $subject){
                    $message->from('appointment@cehcchennai.com', "CEHC - Online Video Consultation");
                    $message->subject($subject);
                    $message->to($to);                
                });

                $PushNotificationController = new PushNotificationController;
                $PushNotificationController->sendPush('CEHC Appointment' , $msg , [$inputs['doctor_id'],$inputs['patient_id']]);

                if($AppointmentData){
                    return $this->sendResponse($AppointmentData,"Appointment is created successfull");
                }
            } else{
                return $this->sendResponse([],"This slot has been already booked , Please check other available timings");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            if(!empty($id)){
                if(Appointment::where('id',$id)->exists()){

                    $blog = Appointment::with('patient','patient.user','doctor','doctor.user','specialization','payment','notes','prescription','diagnosis','prescription.medicine','optics','cancelledBy','cancelledBy.role')->where('id',$id)->first();
                    // echo "<pre>";print_r($blog);die;
                    if(!empty($blog)){
                            if($blog->patient->type_of_patient == 1){
                                $blog->payment_status = "Free Patient";
                            }else{
                                if($blog->payment_id == 0){
                                    $blog->payment_status = "Pending";
                                } elseif($blog->payment != null){
                                    if($blog->payment->is_refund == 1){
                                        $blog->payment_status = "Refunded";
                                    }else{
                                        $blog->payment_status = "Paid";
                                    }
                                }
                            }
                         return $this->sendResponse($blog, 'Appointment Details'); 
                    }else{
                        return $this->sendError('No Appointment Available');               
                    }
                }else{
                    return $this->sendError('Appointment-id is not found');               
                }
            }else{
                return $this->sendError('Appointment-id is empty');                
            }
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            if(!empty($id)){
                if(Appointment::where('id',$id)->exists()){
                    $inputs = $request->input();
                    $Appointment = Appointment::find($id);
                    //  return $this->sendResponse($request->all(),"Appointment request");
                    $rulesParams = $Appointment->requiredRequestParams('update',$id);

                    $validator = Validator::make($inputs,$rulesParams);
                    if($validator->fails()){
                        return $this->sendError($validator->getMessageBag()->first(),[]);
                    }

                    $check = Appointment::where('schedule_date',$inputs['schedule_date'])->where("slot_timing",$inputs['slot_timing'])->where("doctor_id",$inputs['doctor_id'])->where("is_cancel",0)->first();

                    if(empty($check)){
                        $createData = $Appointment->prepareUpdateData($inputs,$Appointment->toArray());
                        $AppointmentData = $Appointment->update($createData);

                        $patient = Patient::find($inputs['patient_id']);

                        PushNotification::where('post_id',$id)->where("sender_id",$inputs['patient_id'])->where("receiver_id", $inputs['doctor_id'])->delete();
                    
                        PushNotification::where('post_id',$id)->where("sender_id",$inputs['doctor_id'])->where("receiver_id", $inputs['patient_id'])->delete();

                        $msg = " Rescheduled appointment for ".$patient->first_name." ".$patient->last_name ." to ". date("d M Y",strtotime($inputs['schedule_date'])). " @ ".date("H:i A",strtotime($inputs['slot_timing']));

                        $notification = [
                            "sender_id" => $inputs['patient_id'],
                            "receiver_id" => $inputs['doctor_id'],
                            "post_id" => $id,
                            "ref_table" => 'appointments',
                            "notification_type" => "create_appointment",
                            "message" => $msg
                        ];

                        PushNotification::create($notification);

                        $notification1 = [
                            "sender_id" => $inputs['doctor_id'],
                            "receiver_id" => $inputs['patient_id'],
                            "post_id" => $id,
                            "ref_table" => 'appointments',
                            "notification_type" => "create_appointment",
                            "message" => $msg
                        ];

                        PushNotification::create($notification1);
                        
                       $doctor = UserInfomation::where('user_id',$inputs['doctor_id'])->first();
                $subject = "CEHC - Online Video Consultation";
                $to = $patient->email_address;
                $data = ['patient'=>$patient,'doctor'=>$doctor,'date'=>$inputs['schedule_date'],'time'=>$inputs['slot_timing']];
                Mail::send('emails.reschedule_appointment', $data, function($message) use ($to, $subject){
                    $message->from('appointment@cehcchennai.com', "CEHC - Online Video Consultation");
                    $message->subject($subject);
                    $message->to($to);                
                });

                        $PushNotificationController = new PushNotificationController;
                        $PushNotificationController->sendPush('CEHC Appointment' , $msg , [$inputs['doctor_id'],$inputs['patient_id']]);

                        if($AppointmentData){
                            return $this->sendResponse($AppointmentData,"Appointment is Rescheduled successfully");
                        }
                    } else{
                        return $this->sendResponse([],"This slot has been already booked , Please check other available timings");
                    }
                }else{
                    return $this->sendError('No Appointment Available');               
                }
            }else{
                return $this->sendError('Appointment-id is empty');                
            }
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cancelBooking(Request $request, $id){
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                $user = auth()->guard('api')->user();
                //echo "<pre>";print_r($user->id);die;

                if(Appointment::where('id',$id)->exists()){
                    
                    $Appointment = Appointment::where('id',$id)->first();
                    if($Appointment->payment_id != 0){
                        $RazorPaymentController = new RazorPaymentController;
                        $RazorPaymentController->refund($Appointment->payment_id);
                    }
                    $ScheduleInfoUpdate = $Appointment->update($inputs);
                    if(!empty($user)){
                        $ScheduleInfoUpdate = $Appointment->update(["cancelled_by"=>$user->id]);
                    }
                    $msg = "Appointment Cancelled";

                    $patient = Patient::find($Appointment->patient_id);

                    PushNotification::where('post_id',$id)->where("sender_id",$Appointment->patient_id)->where("receiver_id", $Appointment->doctor_id)->delete();
                    
                    PushNotification::where('post_id',$id)->where("sender_id",$Appointment->doctor_id)->where("receiver_id", $Appointment->patient_id)->delete();

                    $message = " Cancelled appointment for ".$patient->first_name." ".$patient->last_name ." to ". date("d M Y",strtotime($Appointment->schedule_date)). " @ ".date("H:i A",strtotime($Appointment->slot_timing));

                    $notification = [
                        "sender_id" => $Appointment->patient_id,
                        "receiver_id" => $Appointment->doctor_id,
                        "post_id" => $id,
                        "ref_table" => 'appointments',
                        "notification_type" => "cancel_appointment",
                        "message" => $message
                    ];

                    PushNotification::create($notification);

                    $notification1 = [
                        "sender_id" => $Appointment->doctor_id,
                        "receiver_id" => $Appointment->patient_id,
                        "post_id" => $id,
                        "ref_table" => 'appointments',
                        "notification_type" => "cancel_appointment",
                        "message" => $message
                    ];

                    PushNotification::create($notification1);
        
                    $doctor = UserInfomation::where('user_id',$Appointment->doctor_id)->first();
                    $subject = "CEHC - Online Video Consultation";
                    $to = $patient->email_address;
                    $data = ['patient'=>$patient,'doctor'=>$doctor,'date'=>$Appointment->schedule_date,'time'=>$Appointment->slot_timing];
                    Mail::send('emails.cancel_appointment', $data, function($message) use ($to, $subject){
                        $message->from('appointment@cehcchennai.com', "CEHC - Online Video Consultation");
                        $message->subject($subject);
                        $message->to($to);                
                    });
            
                    $PushNotificationController = new PushNotificationController;
                    $PushNotificationController->sendPush('CEHC Appointment' , $msg , [$Appointment->doctor_id,$Appointment->patient_id]);

                    if($ScheduleInfoUpdate){
                        $message = "Your appointment is cancelled. Your refund will reflect in your bank account in 5-6 Business Days";
                        return $this->sendResponse([], $message); 
                    }else{
                         return $this->sendError("Appointment is not updated.");
                    }
                }else{
                  return $this->sendError("No Appointment Available");   
                }
            }else{
                return $this->sendError("Appointment-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function dashboardCount(){

         $user = auth()->guard('api')->user();
        // echo "<pre>";print_r($user);die;
        if(!empty($user) && $user->role_id == 1){
            $appointmentCount = Appointment::where('is_finished',0)->where('doctor_id',$user->id)->where('status',0)->count();

            $cancelledAppointmentCount = Appointment::where('is_finished',0)->where('doctor_id',$user->id)->where('status',1)->count();
        } else{

            $appointmentCount = Appointment::where('is_finished',0)->where('status',0)->count();

            $cancelledAppointmentCount = Appointment::where('is_finished',0)->where('status',1)->count();
        }

        $amountCollected = Payment::select(DB::raw('SUM(total) as total_qty'))->where('status',0)->get();

        $amountRefund = Payment::select(DB::raw('SUM(total) as total_qty'))->where('status',1)->get();


        $data = ['appointmentCount' => $appointmentCount, 'cancelledAppointmentCount' => $cancelledAppointmentCount, 'amountCollected' => $amountCollected[0]->total_qty ,'amountRefund' => $amountRefund[0]->total_qty];

        return $this->sendResponse($data, 'Dashboard Count'); 
    }

    public function appointmentScheduleDoctor(Request $request){
         try{  
            $inputs = $request->input();
            $ScheduleInfo = Appointment::with('patient','patient.user','doctor','doctor.user','specialization','payment','notes','prescription','diagnosis','prescription.medicine','optics','cancelledBy','cancelledBy.role')->where('doctor_id',$inputs['doctor_id'])->where('schedule_date',$inputs['date'])->where(function($query){
             $query->where('schedule_date','>=',date("Y-m-d"));
             
            })->orderBy('schedule_date','ASC')->orderBy('slot_timing','ASC')->where('status',0)->where('is_finished',0)->get();
            if($ScheduleInfo->count() > 0){
                foreach ($ScheduleInfo as $key => $value) {
                    if($value->patient->type_of_patient == 1){
                $value->payment_status = "Free Patient";
            }else{
                if($value->payment_id == 0){
                    $value->payment_status = "Pending";
                } elseif($value->payment != null){
                    if($value->payment->is_refund == 1){
                        $value->payment_status = "Refunded";
                    }else{
                        $value->payment_status = "Paid";
                    }
                }
            }
                }
                return $this->sendResponse($ScheduleInfo, 'Appointment list for doctor'); 
            }else{
                return $this->sendError("Appointment is not found.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function patientList($id){
        try{  
            $ScheduleInfo = Appointment::with('patient')->where('doctor_id',$id)->groupBy('patient_id')->orderBy('schedule_date','DESC')->orderBy('slot_timing','ASC')->get();
            if($ScheduleInfo->count() > 0){
                $patient = [];
                foreach ($ScheduleInfo as $key => $value) {
                    if($value->patient != NULL){
                        $value->patient->consult = Appointment::where('doctor_id',$id)->where('patient_id',$value->patient_id)->count();
                        $value->patient->last_consult_date = Appointment::where('doctor_id',$id)->where('patient_id',$value->patient_id)->pluck('schedule_date')->first();
                        $patient [] = $value->patient;

                    }
                }
                return $this->sendResponse($patient, 'patient list for doctor'); 
            }else{
                return $this->sendError("No Patient Available");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function bookingHistory($id){
        try{  
            $user = User::find(auth()->user()->id);
            if($user->role_id == 1){
                $ScheduleInfo = Appointment::with('doctor','doctor.user','notes','prescription','diagnosis','prescription.medicine','optics','payment','specialization','patient','cancelledBy','cancelledBy.role')->where('patient_id',$id)->where(function($query){
             $query->where('schedule_date','<',date("Y-m-d"));
             $query->orWhere('is_finished',1);
            })->where('doctor_id',$user->id)->orderBy('schedule_date','DESC')->orderBy('slot_timing','ASC')->get();
            }else{
                $ScheduleInfo = Appointment::with('doctor','notes','prescription','diagnosis','prescription.medicine','optics','payment','specialization','patient','cancelledBy','cancelledBy.role')->where('patient_id',$id)->where(function($query){
             $query->where('schedule_date','<',date("Y-m-d"));
             $query->orWhere('is_finished',1);
            })->orderBy('schedule_date','DESC')->get();
            }
            if($ScheduleInfo->count() > 0){
                return $this->sendResponse($ScheduleInfo, 'Patient History'); 
            }else{
                return $this->sendError("Appointment is not found.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function upcommingAppointment($id){
        try{  
            // $user = User::find(auth()->user()->id);
                $ScheduleInfo = Appointment::with('doctor','doctor.user','notes','prescription','diagnosis','prescription.medicine','optics','payment','specialization','patient','cancelledBy','cancelledBy.role')->where('patient_id',$id)->where(function($query){
             $query->where('schedule_date','>=',date("Y-m-d"));
             
            })->where('is_finished',0)->orderBy('schedule_date','ASC')->orderBy('slot_timing','ASC')->get();
            if($ScheduleInfo->count() > 0){
                 foreach ($ScheduleInfo as $key => $value) {
                    if($value->patient->type_of_patient == 1){
                $value->payment_status = "Free Patient";
            }else{
                if($value->payment_id == 0){
                    $value->payment_status = "Pending";
                } elseif($value->payment != null){
                    if($value->payment->is_refund == 1){
                        $value->payment_status = "Refunded";
                    }else{
                        $value->payment_status = "Paid";
                    }
                }
            }
                }
                return $this->sendResponse($ScheduleInfo, 'Upcomming Appointments'); 
            }else{
                return $this->sendError("Appointment is not found.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function patientAppointment($id, $doctor_id){
        try{  
        Log::info($id."===".$doctor_id);
            $user = User::find(auth()->user()->id);
                $ScheduleInfo = Appointment::with('doctor','doctor.user','notes','prescription','diagnosis','prescription.medicine','optics','payment','specialization','patient','cancelledBy','cancelledBy.role')->where('patient_id',$id)->where('doctor_id',$doctor_id)->orderBy('schedule_date','DESC')->orderBy('slot_timing','ASC')->get();
            if($ScheduleInfo->count() > 0){
                 foreach ($ScheduleInfo as $key => $value) {
                    if($value->patient->type_of_patient == 1){
                $value->payment_status = "Free Patient";
            }else{
                if($value->payment_id == 0){
                    $value->payment_status = "Pending";
                } elseif($value->payment != null){
                    if($value->payment->is_refund == 1){
                        $value->payment_status = "Refunded";
                    }else{
                        $value->payment_status = "Paid";
                    }
                }
            }
                }
                return $this->sendResponse($ScheduleInfo, 'Patient Appointments'); 
            }else{
                return $this->sendError("Appointment is not found.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function cancelledAppointment($id){
        try{
            $user = User::find($id);
            $cancelledAppointmentCount = Appointment::with('doctor','doctor.user','notes','prescription','diagnosis','prescription.medicine','optics','payment','specialization','patient','cancelledBy','cancelledBy.role')->where(function($query){
             $query->where('schedule_date','>=',date("Y-m-d"));
             
            })->where('status',1)->where('doctor_id',$user->id)->get();
            if($cancelledAppointmentCount->count() > 0){
                foreach ($cancelledAppointmentCount as $key => $value) {
                    if($value->patient->type_of_patient == 1){
                $value->payment_status = "Free Patient";
            }else{
                if($value->payment_id == 0){
                    $value->payment_status = "Pending";
                } elseif($value->payment != null){
                    if($value->payment->is_refund == 1){
                        $value->payment_status = "Refunded";
                    }else{
                        $value->payment_status = "Paid";
                    }
                }
            }
                }
                return $this->sendResponse($cancelledAppointmentCount, 'Cancelled Appointments'); 
            }else{
                return $this->sendError("Appointment is not found.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function twodaycron(){
        $date = date('Y-m-d',strtotime('2 day'));
        $check = Appointment::where('schedule_date',$date)->where('status',0)->get();

        if(count($check) > 0){
            foreach ($check as $key => $value) {
                $Appointment = Appointment::where('id',$value->id)->first();
                $ScheduleInfoUpdate = $Appointment->update($inputs);
                $msg = "Appointment Reminder";

                $patient = Patient::find($Appointment->patient_id);

                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->patient_id)->where("receiver_id", $Appointment->doctor_id)->delete();
                
                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->doctor_id)->where("receiver_id", $Appointment->patient_id)->delete();

                $message = "Hi ".$patient->first_name." ".$patient->last_name .", You have appointment on ". date("d M Y",strtotime($Appointment->schedule_date)). " @ ".date("H:i A",strtotime($Appointment->slot_timing));

                $notification1 = [
                    "sender_id" => $Appointment->doctor_id,
                    "receiver_id" => $Appointment->patient_id,
                    "post_id" => $id,
                    "ref_table" => 'appointments',
                    "notification_type" => "appointment_reminder_2day",
                    "message" => $message
                ];

                PushNotification::create($notification1);

                $PushNotificationController = new PushNotificationController;

                $PushNotificationController->sendPush('CEHC Appointment' , $msg , [$Appointment->patient_id]);
            }
        }
    }

    public function onedaycron(){
        $date = date('Y-m-d',strtotime('1 day'));
        $check = Appointment::where('schedule_date',$date)->where('status',0)->get();

        if(count($check) > 0){
            foreach ($check as $key => $value) {
                $Appointment = Appointment::where('id',$value->id)->first();
                $ScheduleInfoUpdate = $Appointment->update($inputs);
                $msg = "Appointment Reminder";

                $patient = Patient::find($Appointment->patient_id);

                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->patient_id)->where("receiver_id", $Appointment->doctor_id)->delete();
                
                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->doctor_id)->where("receiver_id", $Appointment->patient_id)->delete();

                $message = "Hi ".$patient->first_name." ".$patient->last_name .", You have appointment tomorrow @ ".date("H:i A",strtotime($Appointment->slot_timing));

                $notification1 = [
                    "sender_id" => $Appointment->doctor_id,
                    "receiver_id" => $Appointment->patient_id,
                    "post_id" => $id,
                    "ref_table" => 'appointments',
                    "notification_type" => "appointment_reminder_1day",
                    "message" => $message
                ];

                PushNotification::create($notification1);

                $PushNotificationController = new PushNotificationController;
                
                $PushNotificationController->sendPush('CEHC Appointment' , $msg , [$Appointment->patient_id]);
            }
        }
    }

    public function fivehourcron(){
        $date = date('Y-m-d');
        $time = date("H:i:00", strtotime('+5 hours'));
        $check = Appointment::where('schedule_date',$date)->where('slot_timing',$time)->where('status',0)->get();

        if(count($check) > 0){
            foreach ($check as $key => $value) {
                $Appointment = Appointment::where('id',$value->id)->first();
                $ScheduleInfoUpdate = $Appointment->update($inputs);
                $msg = "Appointment Reminder";

                $patient = Patient::find($Appointment->patient_id);

                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->patient_id)->where("receiver_id", $Appointment->doctor_id)->delete();
                
                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->doctor_id)->where("receiver_id", $Appointment->patient_id)->delete();

                $message = "Hi ".$patient->first_name." ".$patient->last_name .", You have appointment today @ ".date("H:i A",strtotime($Appointment->slot_timing));

                $notification1 = [
                    "sender_id" => $Appointment->doctor_id,
                    "receiver_id" => $Appointment->patient_id,
                    "post_id" => $id,
                    "ref_table" => 'appointments',
                    "notification_type" => "appointment_reminder_1day",
                    "message" => $message
                ];

                PushNotification::create($notification1);

                $PushNotificationController = new PushNotificationController;
                
                $PushNotificationController->sendPush('CEHC Appointment' , $msg , [$Appointment->patient_id]);
            }
        }
    }

    public function twohourcron(){
        $date = date('Y-m-d');
        $time = date("H:i:00", strtotime('+2 hours'));
        $check = Appointment::where('schedule_date',$date)->where('slot_timing',$time)->where('status',0)->get();

        if(count($check) > 0){
            foreach ($check as $key => $value) {
                $Appointment = Appointment::where('id',$value->id)->first();
                $ScheduleInfoUpdate = $Appointment->update($inputs);
                $msg = "Appointment Reminder";

                $patient = Patient::find($Appointment->patient_id);

                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->patient_id)->where("receiver_id", $Appointment->doctor_id)->delete();
                
                PushNotification::where('post_id',$id)->where("sender_id",$Appointment->doctor_id)->where("receiver_id", $Appointment->patient_id)->delete();

                $message = "Hi ".$patient->first_name." ".$patient->last_name .", You have appointment today @ ".date("H:i A",strtotime($Appointment->slot_timing));

                $notification1 = [
                    "sender_id" => $Appointment->doctor_id,
                    "receiver_id" => $Appointment->patient_id,
                    "post_id" => $id,
                    "ref_table" => 'appointments',
                    "notification_type" => "appointment_reminder_1day",
                    "message" => $message
                ];

                PushNotification::create($notification1);

                $PushNotificationController = new PushNotificationController;
                
                $PushNotificationController->sendPush('CEHC Appointment' , $msg , [$Appointment->patient_id]);
            }
        }
    }
    
    public function endConsultation($id){
        try{  
            $check = Appointment::find($id);
            if(!empty($check)){
                $check->is_finished = 1;
                $check->save();
                return $this->sendResponse([], 'Ended Consultant'); 
            }else{
                return $this->sendError("Appointment is not found.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

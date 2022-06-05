<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
use App\Http\Controllers\API\RazorPaymentController;
use Mail;

class CancelAppointment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:appointment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = date('Y-m-d');
        //$time = date("H:i:00", strtotime('+2 hours'));
        $check = Appointment::where('schedule_date','<=',$date)->where('is_attended',0)->where('status',0)->get();

        if(count($check) > 0){
            foreach ($check as $key => $value) {
                $Appointment = Appointment::where('id',$value->id)->first();
                $ScheduleInfoUpdate = $Appointment->update(['is_attended'=>1]);
                    if($Appointment->payment_id != 0){
                        $RazorPaymentController = new RazorPaymentController;
                        $RazorPaymentController->refund($Appointment->payment_id);
                    }
                    $ScheduleInfoUpdate = $Appointment->update($inputs);
                    if(!empty($user)){
                        $ScheduleInfoUpdate = $Appointment->update(["cancelled_by"=>0]);
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
            }
        }
        return true;
    }
}

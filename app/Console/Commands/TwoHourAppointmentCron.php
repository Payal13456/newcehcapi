<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\API\PushNotificationController;
use App\Models\PushNotification;
use App\Models\Appointment;
use App\Models\Patient;

class TwoHourAppointmentCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twohour:appointment';

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
        return true;
    }
}

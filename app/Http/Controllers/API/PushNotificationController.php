<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PushNotification;
use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;


class PushNotificationController extends BaseController
{
    public function index(){
    	try{
            $notification = [];
	    	$user_id = auth('api')->user()->id;
	    	$notification = PushNotification::where('receiver_id',$user_id)->orderBy('created_at','DESC')->get();
	    	if($notification->count() > 0){
                return $this->sendResponse($notification,"List of all Notification.");
            }
            return $this->sendError('Notification is not found');
        } catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function readNotification($id){
        try{
            $notification = [];
            if(PushNotification::find($id)){
                PushNotification::where('id',$id)->update(['is_read'=>1]);
                return $this->sendResponse([],"Notification readed");
            }
            return $this->sendError('Notification is not found');
            
        } catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function notificationCount(){
        try{
            $notification = [];
            $user_id = auth('api')->user()->id;
            $notification = PushNotification::where('receiver_id',$user_id)->where('is_read',0)->get();
            if($notification->count() > 0){
                return $this->sendResponse($notification->count(),"Notification count");
            }
            return $this->sendResponse(0,'Notification count');
        } catch(Exception $e){  
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function sendPush($title , $message , $ids)
    {
        $firebaseToken = User::whereNotNull('fcm_token')->whereIn('id',$ids)->pluck('fcm_token')->all();
          
        $SERVER_API_KEY = "AAAAmBdnMSw:APA91bGpPMdiKvQg1KoKHSl8nwm4TIcu6aYSsUTvMUDArIeImTVz7gGyn3yFeFy3DEDpzStIQ9zFfbmzgbXEar5P10-r3EVhyw0Pbpq3oBskl23Be8gBSlbSR_yLlNuWhAfenkkz4DrK";
  
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $message,  
            ]
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
  	//echo "<pre>";print_r($response);die;
        return true;
    }
    
    public function sendVideoCallPush($title , $appointment_id , $channel_name, $name , $ids)
    {
        $firebaseToken = User::whereNotNull('fcm_token')->whereIn('id',$ids)->pluck('fcm_token')->all();
          
       // $SERVER_API_KEY = env('FIREBASE_SERVER_KEY');
       $SERVER_API_KEY = "AAAAmBdnMSw:APA91bGpPMdiKvQg1KoKHSl8nwm4TIcu6aYSsUTvMUDArIeImTVz7gGyn3yFeFy3DEDpzStIQ9zFfbmzgbXEar5P10-r3EVhyw0Pbpq3oBskl23Be8gBSlbSR_yLlNuWhAfenkkz4DrK";
  
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => '',
                "appointment_id" => $appointment_id,
                "channel_name" => $channel_name,
                "name" => $name  
            ]
        ];
        $arrayToSend = [
		 'registration_ids' => $firebaseToken, 
		 
		    'priority' => 'high',
		    'data' =>  [
			"title" => $title,
			"body" => '',
			"appointment_id" => $appointment_id,
			"channel_name" => $channel_name,
			"name" => $name  
		    ], 
		    'content_available' => true,
		    "click_action"=> "FLUTTER_NOTIFICATION_CLICK"
	];

        $dataString = json_encode($arrayToSend);
        
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);

        return true;
    }
}

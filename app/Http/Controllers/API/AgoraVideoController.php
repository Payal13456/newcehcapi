<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Events\MakeAgoraCall;
use App\Models\Appointment;
use App\Models\videoCallDetails;
use App\Models\Patient;
use App\Events\VideoCall;
use App\Http\Controllers\API\PushNotificationController;

class AgoraVideoController extends BaseController
{
    public function index(Request $request)
    {
        // fetch all users apart from the authenticated user
        $users = User::where('id', '<>', Auth::id())->get();
        return view('admin.agora-chat', ['users' => $users]);
    }

    public function token(Request $request)
    {
        $appID = "ed0139b770d54fd885118259621ac8b3";
        $appCertificate = "79e60e68920b48f29b2e67ae8991e29a";
        $blog = Appointment::find($request->id);
        if(!empty($blog)){
	    	$channelName = "Appointment_".$blog->id."_".$blog->type."_".date('dmy',strtotime($blog->schedule_date));
		$user = Auth::user()->name;
		// echo "<pre>";print_r($user);die;
		$role = RtcTokenBuilder::RoleAttendee;
		$expireTimeInSeconds = 3600;
		$currentTimestamp = now()->getTimestamp();
		$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
		$token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);

		$d = ['appointment_id'=>$request->id, 'token'=>$token,'channel_name'=>$channelName];
		
		$data['token'] = $token;
		$data['name'] = $user;
		$data['channel_name'] = $channelName;
		
		$patient = Patient::find($blog->patient_id);
		if(!empty($patient)){
		    $patientid = $patient->user_id;
		}else{
		    $patientid = $blog->patient_id;
		}

		if(Auth::user()->id == $blog->doctor_id){
		    Appointment::where('id',$request->id)->update(["is_attended"=>1]);
		    $PushNotificationController = new PushNotificationController;
		    $PushNotificationController->sendVideoCallPush('Incomming Call from '.$user, $request->id , $channelName, $user , [$patientid]);
		    // event(new VideoCall($user, $channelName, $patientid));
		}

		return $this->sendResponse($data, 'Token Data');
	}else{
		return $this->sendError("Appointment not found");    
	}
    }

    public function callUser(Request $request)
    {
        $data['userToCall'] = $request->user_to_call;
        $data['channelName'] = $request->channel_name;
        $data['from'] = Auth::id();
        return $data;
        // broadcast(new MakeAgoraCall($data))->toOthers();
    }
}

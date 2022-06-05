<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\BlogFiles;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\UserInfomation;
use DB;
use App\Models\Payment;
use App\Http\Controllers\API\PushNotificationController;
use Validator;
use Mail;
use Illuminate\Support\Facades\Log;

class PatientController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $aadhar = request('adhar_card','');
            $phone_number = request('phone_number','');
            $patient = Patient::with('state')->get();
            if(!empty($aadhar)){
                $patient = $patient->where('adhar_card',$aadhar);
            }
            if(!empty($phone_number)){
                $patient = $patient->where('phone_number_primary',$phone_number);
            }
            if($patient->count() > 0){
                return $this->sendResponse($patient,"List of all patient.");
            }
             return $this->sendError('patient is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
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
            $inputs = $request->input();
            $patient = new Patient();
            $rulesParams = $patient->requiredRequestParams('create');
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
                return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $check = User::where('phonenumber','LIKE',$inputs['phone_number_primary'])->where('email',$inputs['email_address'])->first();
            if(empty($check)){
                $data = [
                    "email" => $inputs['email_address'],
                    "password" => $inputs['password'],
                    "first_name" => $inputs['first_name'],
                    "phonenumber"=> $inputs['phone_number_primary'],
                    "role_id" => 3
                ];
                
                $user = new user();
                
                $rulesParams = $user->requiredRequestParams('create');
                $validator = Validator::make($data,$rulesParams);

                if($validator->fails()){
                   return $this->sendError($validator->getMessageBag()->first(),[]);
                }
                $createUserData = $user->prepareCreateData($data);
                
                $user = $user->create($createUserData); 
            } else{
                $user = $check;
            }
            $inputs['uhid'] = substr($inputs['first_name'],0,3).'_'.mt_rand(100000,999999);
	     if(array_key_exists('image',$inputs)){
                $img = $patient->createImage($inputs['image']);
                $images = $img;
            }elseif($inputs['gender'] == 1){
               $images = 'profile/unsplash_8ig-SzHpqDw-2.png';
            }else{
                $images = 'profile/unsplash_va0YmklFtPA-2.png';
            }

            $createData = $patient->prepareCreateData($inputs);
            $createData['upload_of_picture'] = $images;
            $createData['user_id'] = $user->id;
            $createData['ip_address']=request()->ip();
            $patientData = $patient->create($createData);

            if($patientData){
                return $this->sendResponse([],"Patient is created successfull");
            }
            return $this->sendError("Patient is not created");              
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
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
                if(Patient::where('id',$id)->exists()){
                    $patient = Patient::with('state')->where('id',$id)->first();
                    if(!empty($patient)){
                        return $this->sendResponse($patient, 'Patient is found.');
                    }
                    return $this->sendError("Patient is not found.");   
                }else{
                   return $this->sendError( 'Patient  is not found.');  
               }
            }else{
              return $this->sendError("Patient-id is empty.");   
            }
           
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }         
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
                
                $inputs = $request->input();
                if(Patient::where('id',$id)->exists()){
                    $patient = Patient::where('id',$id)->first();
                    if(!empty($patient)){
                            
                        $rulesParams = $patient->requiredRequestParams('update');
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                           return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        
                        $prepareData = $patient->prepareUpdateData($inputs,$patient->toArray());
                        if(strpos($patient->upload_of_picture , 'profile') == false){
                            if($inputs['gender'] == 1){
                               $images = 'profile/unsplash_8ig-SzHpqDw-2.png';
                            }else{
                                $images = 'profile/unsplash_va0YmklFtPA-2.png';
                            }
                            $prepareData['upload_of_picture'] = $images;
                        }

                        // if(array_key_exists('upload_of_picture',$inputs) && !empty($inputs['upload_of_picture'])){
                        //     $prepareData['upload_of_picture'] = $patient->uploadImageFile($inputs['upload_of_picture']);
                        // }

                        $isUpdated = $patient->update($prepareData);
                        if($isUpdated){
                        return $this->sendResponse([], 'Patient details update successfully.');

                        }
                        return $this->sendError("Patient details not updated.");     
                    }
                    return $this->sendError("Patient is not found.");   
                }else{
                   return $this->sendError( 'Patient  is not found.');  
               }
            }else{
              return $this->sendError("Patient-id is empty.");   
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
        try{  
            if(!empty($id)){
                if(Patient::where('id',$id)->exists()){
                    $patient = Patient::where('id',$id)->first();
                    if(!empty($patient)){
                        $patient->delete();
                        return $this->sendResponse([],'Patient remove successfully.');
                    }
                    return $this->sendError("Patient is not found.");   
                }else{
                   return $this->sendError( 'Patient  is not found.');  
               }
            }else{
              return $this->sendError("Patient-id is empty.");   
            }
           
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }         
    }

         /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function patientStatusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $user = auth()->guard('api')->user();
                $inputs = $request->input();
                if(Patient::where('id',$id)->exists()){
                    $patientInfo = Patient::where('id',$id)->first();
                    $patientInfoUpdate = $patientInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Patient is in-active.";
                                break;
                            case '1':
                                $msg = "Patient is active.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($patientInfoUpdate){
                        if($inputs['status'] == 0){
                            $appointments = Appointment::where('patient_id',$id)->where('schedule_date','>=',date("Y-m-d"))->where('is_finished',0)->where('status',0)->get();

                            foreach($appointments as $app){
                                $Appointment = Appointment::where('id',$app->id)->first();
                                // if($Appointment->payment_id != 0){
                                //     $RazorPaymentController = new RazorPaymentController;
                                //     $RazorPaymentController->refund($Appointment->payment_id);
                                // }
                                $ScheduleInfoUpdate = $Appointment->update(["status"=>1]);
                                if(!empty($user)){
                                    $ScheduleInfoUpdate = $Appointment->update(["cancelled_by"=>$user->id]);
                                }
                                $message = "Appointment Cancelled";

                                $doctor = UserInfomation::where('user_id',$Appointment->doctor_id)->first();
                                $subject = "CEHC - Online Video Consultation";
                                $to = $patientInfo->email_address;
                                $data = ['patient'=>$patientInfo,'doctor'=>$doctor,'date'=>$Appointment->schedule_date,'time'=>$Appointment->slot_timing];
                                Mail::send('emails.cancel_appointment', $data, function($message) use ($to, $subject){
                                    $message->from('appointment@cehcchennai.com', "CEHC - Online Video Consultation");
                                    $message->subject($subject);
                                    $message->to($to);                
                                });
                        
                                $PushNotificationController = new PushNotificationController;
                                $PushNotificationController->sendPush('CEHC Appointment' , $message , [$Appointment->doctor_id,$Appointment->patient_id]);

                            }
                            return $this->sendResponse([], $msg); 
                        }else{
                            return $this->sendResponse([], $msg); 
                        }
                    }else{
                         return $this->sendError("Patient is not updated.");
                    }
                }else{
                  return $this->sendError("Patient is not found.");   
                }
            }else{
                return $this->sendError("Patient-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function addPatient(Request $request){
         try{
         
            $inputs = $request->input();
            
            $patient = new Patient();
            $rulesParams = $patient->requiredRequestParams('register');
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $check = User::where('phonenumber','LIKE',$inputs['phonenumber'])->orWhere('email',$inputs['email'])->first();
            if(empty($check)){
                $data = [
                    "email" => $inputs['email'],
                    'password' => random_int(100000, 999999),
                    "first_name" => $inputs['first_name'],
                    "phonenumber"=> $inputs['phonenumber'],
                    "role_id" => 3
                ];

                $user = new user();
                
                $rulesParams = $user->requiredRequestParams('create');
                $validator = Validator::make($data,$rulesParams);

                if($validator->fails()){
                    return $this->sendError($validator->getMessageBag()->first(),[]);
                }
                $createUserData = $user->prepareCreateData($data);
                $createUserData['is_approved'] = 1;
                $user = $user->create($createUserData); 
                // $userid = $user->id;
            } else{
                $user = $check;
                // $userid = $check->user_id;
            }
            // echo "<pre>";print_r($user);die;
            if($inputs['gender'] == 1){
               $images = 'profile/unsplash_8ig-SzHpqDw-2.png';
            }else{
                $images = 'profile/unsplash_va0YmklFtPA-2.png';
            }
            
            $createData = $patient->prepareRegisterData($inputs);
            $createData['user_id'] = $user->id;
            $createData['uhid'] = substr($inputs['first_name'],0,3).'_'.mt_rand(100000,999999);
            $createData['upload_of_picture']=$images;
            $createData['ip_address']=request()->ip();
            $patientData = $patient->create($createData);
            
            if($patientData){
                return $this->sendResponse($patientData,"Patient is added successfull");
            }
            return $this->sendError("Patient is not added");              
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }  
    }

    public function getPatients($id){
        try{
            $patient = Patient::where('parent_id',$id)->where('status',1)->get();
            if($patient){
                return $this->sendResponse($patient,"Family Member List");
            }
            return $this->sendError("Family member not available"); 
        } catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function updatePatient(Request $request, $id){
        try{  
            if(!empty($id)){
            
                $inputs = $request->input();
                Log::info(json_encode($inputs));
                if(Patient::where('id',$id)->exists()){
                    $patient = Patient::where('id',$id)->first();
                    if(!empty($patient)){
                        $rulesParams = $patient->requiredRequestParams('profile');
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                            return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        
                        $prepareData = $patient->prepareProfileData($inputs,$patient->toArray());
                        
                        if(array_key_exists('image',$inputs)){
		                $img = $patient->createImage($inputs['image']);
		                $prepareData['upload_of_picture'] = $img;
		        }elseif(str_contains($patient->upload_of_picture , 'profile') == false){
                            if($inputs['gender'] == 1){
                               $images = 'profile/unsplash_8ig-SzHpqDw-2.png';
                            }else{
                                $images = 'profile/unsplash_va0YmklFtPA-2.png';
                            }
                            $prepareData['upload_of_picture'] = $images;
                        }

                        $isUpdated = $patient->update($prepareData);
                        if($isUpdated){
                        $data = [];
                        $user = User::find($patient->user_id);
                        if(!empty($user)){
                        	$userinfo = Patient::where('user_id',$user->id)->first();
                        	$user->userinfo = $userinfo;
                        }
                        $data['user'] = $user;
                        return $this->sendResponse($data, 'Patient details update successfully.');

                        }
                        return $this->sendError("Patient details not updated.");     
                    }
                    return $this->sendError("Patient is not found.");   
                }else{
                   return $this->sendError( 'Patient  is not found.');  
               }
            }else{
              return $this->sendError("Patient-id is empty.");   
            }
           
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }  
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Notification;
use App\Models\User;
use Validator;
use Larafirebase;

class NotificationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $faq = Notification::all();
            if($faq->count() > 0){
                return $this->sendResponse($faq,"List of all Notification.");
            }
            return $this->sendError('Notification is not found');
        } catch(Exception $e){
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
            $faq = new Notification();
            $rulesParams = $faq->requiredRequestParams('create');
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
                return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $faq->prepareCreateData($inputs);
            $faqData = $faq->create($createData);
            
            $url = 'https://fcm.googleapis.com/fcm/send';
            $FcmToken = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
              
            //$serverKey = env('FIREBASE_SERVER_KEY');
      	    $serverKey = "AAAAmBdnMSw:APA91bGpPMdiKvQg1KoKHSl8nwm4TIcu6aYSsUTvMUDArIeImTVz7gGyn3yFeFy3DEDpzStIQ9zFfbmzgbXEar5P10-r3EVhyw0Pbpq3oBskl23Be8gBSlbSR_yLlNuWhAfenkkz4DrK";
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" => $request->title,
                    "body" => $request->description,  
                ]
            ];
            $encodedData = json_encode($data);
        
            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
          
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

            // Execute post
            $result = curl_exec($ch);

            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }        
            // Close connection
            curl_close($ch);

            if($faqData){
                return $this->sendResponse($result,"Notification is created successfully");
            }
            return $this->sendError("Notification is not created");
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
                if(Notification::where('id',$id)->exists()){
                    $faq = Notification::where('id',$id)->first();
                    if(!empty($faq)){
                        return $this->sendResponse($faq, 'Notification is found.');
                    }
                    return $this->sendError("Notification is not found.");   
                }else{
                   return $this->sendError( 'Notification  is not found.');  
               }
            }else{
              return $this->sendError("Notification-id is empty.");   
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
                if(Notification::where('id',$id)->exists()){
                    $faq = Notification::where('id',$id)->first();
                    if(!empty($faq)){
                            
                        $rulesParams = $faq->requiredRequestParams('update');
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                            return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        $prepareData = $faq->prepareUpdateData($inputs,$faq->toArray());
                        $isUpdated = $faq->update($prepareData);
                        if($isUpdated){
                        return $this->sendResponse([], 'Notification details update successfully.');
                        }
                        return $this->sendError("Notification details not updated.");     
                    }
                    return $this->sendError("Notification is not found.");   
                }else{
                   return $this->sendError( 'Notification  is not found.');  
               }
            }else{
              return $this->sendError("Notification-id is empty.");   
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
                if(Notification::where('id',$id)->exists()){
                    $faq = Notification::where('id',$id)->first();
                    if(!empty($faq)){
                        $faq->delete();
                        return $this->sendResponse([],'Notification remove successfully.');
                    }
                    return $this->sendError("Notification is not found.");   
                }else{
                   return $this->sendError( 'Notification  is not found.');  
               }
            }else{
              return $this->sendError("Notification-id is empty.");   
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
    public function faqStatusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(Notification::where('id',$id)->exists()){
                    $faqInfo = Notification::where('id',$id)->first();
                    $faqInfoUpdate = $faqInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Notification is active.";
                                break;
                            case '1':
                                $msg = "Notification is inactive.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($faqInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Notification is not updated.");
                    }
                }else{
                  return $this->sendError("Notification is not found.");   
                }
            }else{
                return $this->sendError("Notification-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

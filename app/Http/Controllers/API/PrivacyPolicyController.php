<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\PrivacyPolicy;
use Validator;

class PrivacyPolicyController extends BaseController
{
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            
            $privacyPolicy = PrivacyPolicy::all();
            if($privacyPolicy->count() > 0){
                return $this->sendResponse($privacyPolicy,"List of all Privacy&Policy.");
            }
             return $this->sendError('Privacy&Policy is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }
    
    public function privacyPolicyDoctor()
    {
        try{
            $description = "";
            $privacyPolicy = PrivacyPolicy::first();
            if(!empty($privacyPolicy)){
                    $description .= $privacyPolicy->description;
                return $this->sendResponse($description,"Privacy Policy Details");
            }
             return $this->sendError('Privacy&Policy is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }
    public function privacyPolicyPatient()
    {
        try{
            $description = "";
            $privacyPolicy = PrivacyPolicy::orderBy('id','DESC')->first();
            if(!empty($privacyPolicy)){
                    $description .= $privacyPolicy->description;
                return $this->sendResponse($description,"Privacy Policy Details");
            }
             return $this->sendError('Privacy&Policy is not found');
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
            $privacyPolicy = new PrivacyPolicy();
            $rulesParams = $privacyPolicy->requiredRequestParams('create');
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $privacyPolicy->prepareCreateData($inputs);
            $createData['ip_address']=request()->ip();          
            $privacyPolicyData = $privacyPolicy->create($createData);
            if($privacyPolicyData){
                return $this->sendResponse([],"Privacy&Policy is created successfully");
            }
            return $this->sendError("Privacy & Policy is not created");              
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
                if(PrivacyPolicy::where('id',$id)->exists()){
                    $privacyPolicy = PrivacyPolicy::where('id',$id)->first();
                    if(!empty($privacyPolicy)){
                        return $this->sendResponse($privacyPolicy, 'Privacy&Policy is found.');
                    }
                    return $this->sendError("Privacy&Policy is not found.");   
                }else{
                   return $this->sendError('Privacy&Policy  is not found.');  
               }
            }else{
              return $this->sendError("Privacy&Policy-id is empty.");   
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
                if(privacyPolicy::where('id',$id)->exists()){
                    $privacyPolicy = privacyPolicy::where('id',$id)->first();
                    if(!empty($privacyPolicy)){
                            
                        $rulesParams = $privacyPolicy->requiredRequestParams('update');
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                           return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        $prepareData = $privacyPolicy->prepareUpdateData($inputs,$privacyPolicy->toArray());
                        $isUpdated = $privacyPolicy->update($prepareData);
                        if($isUpdated){
                        return $this->sendResponse([], 'PrivacyPolicy details update successfully.');
                        }
                        return $this->sendError("PrivacyPolicy details not updated.");     
                    }
                    return $this->sendError("privacyPolicy is not found.");   
                }else{
                   return $this->sendError( 'privacyPolicy  is not found.');  
               }
            }else{
              return $this->sendError("privacyPolicy-id is empty.");   
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
                if(PrivacyPolicy::where('id',$id)->exists()){
                    $privacyPolicy = PrivacyPolicy::where('id',$id)->first();
                    if(!empty($privacyPolicy)){
                        $privacyPolicy->delete();
                        return $this->sendResponse([],'Privacy&Policy remove successfully.');
                    }
                    return $this->sendError("Privacy&Policy is not found.");   
                }else{
                   return $this->sendError( 'Privacy&Policy  is not found.');  
               }
            }else{
              return $this->sendError("Privacy&Policy-id is empty.");   
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
    public function policiesStatusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(PrivacyPolicy::where('id',$id)->exists()){
                    $planInfo = PrivacyPolicy::where('id',$id)->first();
                    $planInfoUpdate = $planInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Policy is active.";
                                break;
                            case '1':
                                $msg = "Policy is inactive.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($planInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Policy is not updated.");
                    }
                }else{
                  return $this->sendError("Policy is not found.");   
                }
            }else{
                return $this->sendError("Policy-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

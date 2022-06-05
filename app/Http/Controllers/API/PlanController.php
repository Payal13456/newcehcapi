<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Plan;
use Exception;

class PlanController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $plans = Plan::all();
            if($plans->count() > 0){
                return $this->sendResponse($plans,"List of all Plan.");
            }
             return $this->sendError('Plans is not found');
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
            // print_r($inputs);die();
            $plan = new Plan();
            $rulesParams = $plan->requiredRequestParams('create');

            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $plan->prepareCreateData($inputs);
             $createData['ip_address']=request()->ip();  
            $planData = $plan->create($createData);
            if($planData){
                return $this->sendResponse([],"Plan is created successfull");
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
                if(Plan::where('id',$id)->exists()){
                    $plan = Plan::where('id',$id)->first();
                    if(!empty($plan)){
                         return $this->sendResponse($plan, 'Plan details is found.'); 
                    }else{
                        return $this->sendError('Plan details is not found');               
                    }
                }else{
                    return $this->sendError('Plan-id is not found');               
                }
            }else{
                return $this->sendError('Plan-id is empty');                
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
                if(Plan::where('id',$id)->exists()){
                    $plan = Plan::where('id',$id)->first();
                    if(!empty($plan)){
                        $inputs = $request->input();
                        $rulesParams = $plan->requiredRequestParams('update',$id);
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                           return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        $updateData= $plan->prepareUpdateData($inputs,$plan->toArray());
                        $isUpdated = $plan->update($updateData);
                        if($isUpdated){
                            return $this->sendResponse([],"Plan details is updated successfull");
                        }
                         return $this->sendError('Plan details is not updated'); 
                    }else{
                        return $this->sendError('Plan details is not found');               
                    }
                }else{
                    return $this->sendError('Plan-id is not found');               
                }
            }else{
                return $this->sendError('Plan-id is empty');                
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),500);
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
                if(Plan::where('id',$id)->exists()){
                    $plan = Plan::where('id',$id)->first();
                    if(!empty($plan)){
                        $plan->delete();
                        return $this->sendResponse([], 'Plan details is deleted successfully.'); 
                    }else{
                        return $this->sendError('Plan details is not found');               
                    }
                }else{
                    return $this->sendError('Plan-id is not found');               
                }
            }else{
                return $this->sendError('Plan-id is empty');                
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
    public function planStatusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(Plan::where('id',$id)->exists()){
                    $planInfo = Plan::where('id',$id)->first();
                    $planInfoUpdate = $planInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Plan is active.";
                                break;
                            case '1':
                                $msg = "Plan is inactive.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($planInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Plan is not updated.");
                    }
                }else{
                  return $this->sendError("Plan is not found.");   
                }
            }else{
                return $this->sendError("Plan-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Hospital;
use Exception;


class HospitalController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         try{
            $hospitals = Hospital::with('user')->get();
            if($hospitals->count() > 0){
                return $this->sendResponse($hospitals,"List of all Hospital.");
            }
             return $this->sendError('Hospitals is not found');
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
            $hospital = new Hospital();
            $rulesParams = $hospital->requiredRequestParams('create');

            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $hospital->prepareCreateData($inputs);
             $createData['ip_address']=request()->ip();  
            $hospitalData = $hospital->create($createData);
            if($hospitalData){
                return $this->sendResponse($hospitalData,"Hospital details is created successfully");
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
                if(Hospital::where('id',$id)->exists()){
                    $hospital = Hospital::with('state')->where('id',$id)->first();
                    if(!empty($hospital)){
                         return $this->sendResponse($hospital, 'Hospital is found.'); 
                    }else{
                        return $this->sendError('Hospital is not found');               
                    }
                }else{
                    return $this->sendError('Hospital-id is not found');               
                }
            }else{
                return $this->sendError('Hospital-id is empty');                
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
                if(Hospital::where('id',$id)->exists()){
                    $hospital = Hospital::where('id',$id)->first();
                    if(!empty($hospital)){
                        $inputs = $request->input();
                        $rulesParams = $hospital->requiredRequestParams('update',$id);
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                           return $this->sendError($validator->getMessageBag()->first(),[]);
                        }

                        $updateData= $hospital->prepareUpdateData($inputs,$hospital->toArray());
                        $isUpdated = $hospital->update($updateData);
                        if($isUpdated){
                            return $this->sendResponse($hospital,"Hospital details is updated successfull");
                        }
                         return $this->sendError('Hospital details is not updated'); 
                    }else{
                        return $this->sendError('Hospital details is not found');               
                    }
                }else{
                    return $this->sendError('Hospital-id is not found');               
                }
            }else{
                return $this->sendError('Hospital-id is empty');                
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
                if(Hospital::where('id',$id)->exists()){
                    $hospital = Hospital::where('id',$id)->first();
                    if(!empty($hospital)){
                        $hospital->delete();
                        return $this->sendResponse([], 'Hospital is deleted successfull.'); 
                    }else{
                        return $this->sendError('Hospital is not found');               
                    }
                }else{
                    return $this->sendError('Hospital-id is not found');               
                }
            }else{
                return $this->sendError('Hospital-id is empty');                
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
    public function statusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(Hospital::where('id',$id)->exists()){
                    $hospitalInfo = Hospital::where('id',$id)->first();
                    $hospitalInfoUpdate = $hospitalInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Hospital is active.";
                                break;
                            case '1':
                                $msg = "Hospital is inactive.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($hospitalInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Hospital is not updated.");
                    }
                }else{
                  return $this->sendError("Hospital is not found.");   
                }
            }else{
                return $this->sendError("Hospital-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

 /**
     * User block and unblock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function hospitalBlockUnblock(Request $request, $id){
        try{
            if(!empty($id)){
                $inputs = $request->input();
                if(Hospital::where('id',$id)->exists()){
                    $hospitalInfo = Hospital::where('id',$id)->first();
                    $hospitalInfoUpdate = $hospitalInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['is_block']){
                            case '1':
                                $msg = "Hospital is unblock.";
                                break;
                            case '2':
                                $msg = "Hospital is block.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($hospitalInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Hospital is not updated.");
                    }
                }else{
                  return $this->sendError("Hospital is not found.");   
                }
            }else{
                return $this->sendError("Hospital-id is empty.."); 
            }

        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

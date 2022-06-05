<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Exception;
use App\Models\Specialization;
use Validator;
use DB;
class SpecializationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $specialization = Specialization::with('user')->get();
            if($specialization->count() > 0){
                return $this->sendResponse($specialization, 'List of all Specializations.');
            }
            return $this->sendError('Specializations is not found');
        }catch(Exception $ex){
            return $this->sendError($ex->getMessage(),'',500);
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
            $inputs= $request->input();
            $specialization = new Specialization();
            $requiredParams = $specialization->requiredRequestParams('create');
            $validator = Validator::make($inputs, $requiredParams);
            if ($validator->fails()) {
                return $this->sendError($validator->getMessageBag()->first(),[]);
            } 
            $inputs['ip_address']=request()->ip();           
            $createData = $specialization->prepareCreateData($inputs);
            $checkIsAvailable = $specialization->create($createData);   
            return $this->sendResponse([], 'Specializations is created.');
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
                if(Specialization::where('id',$id)->exists()){
                    $specialization = Specialization::where('id',$id)->first();
                    if(!empty($specialization)){
                        return $this->sendResponse($specialization, 'Specializations is found.');
                    }  
                    return $this->sendError('Specializations is found.');     
                }else{
                   return $this->sendError('Specializations is found.');  
                }
            }else{
              return $this->sendError("Specializations-id is empty.");   
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
            if (!Specialization::where('id',$id)->exists()) {
                 return $this->sendError("Specializations is not found.");
            }
            $inputs = $request->input();
            $specialization = Specialization::where('id',$id)->first();
            $requiredParams = $specialization->requiredRequestParams('update');
            $validator = Validator::make($inputs, $requiredParams);
            if ($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $updateData = $specialization->prepareUpdateData($inputs,$specialization->toArray()); 
            $updatespecialization = $specialization->update($updateData);
            if($updatespecialization){
                return $this->sendResponse([],'Specializations is updated successfull.');
            }
            return $this->sendError("Specializations is Not Updated.");
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
                if(Specialization::where('id',$id)->exists()){
                    $specialization = Specialization::where('id',$id)->first();
                    if(!empty($specialization)){
                        $specialization->delete();
                        return $this->sendResponse([], 'Specialization details is deleted successfully.'); 
                    }else{
                        return $this->sendError('Specialization details is not found');               
                    }
                }else{
                    return $this->sendError('Specialization-id is not found');               
                }
            }else{
                return $this->sendError('Specialization-id is empty');                
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }
}

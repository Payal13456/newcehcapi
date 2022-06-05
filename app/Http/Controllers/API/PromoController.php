<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Promocode;
use Validator;

class PromoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $Promocode = Promocode::all();
            if($Promocode->count() > 0){
                return $this->sendResponse($Promocode,"List of all Promocode.");
            }
            return $this->sendError('Promocode is not found');
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
        // try{
            $inputs = $request->input();
            $Promocode = new Promocode();
            $rulesParams = $Promocode->requiredRequestParams('create');
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $Promocode->prepareCreateData($inputs);
            // $createData['ip_address']=request()->ip();          
            $Promocode = $Promocode->create($createData);
            if($Promocode){
                return $this->sendResponse([],"Promocode is created successfully");
            }
            return $this->sendError("Promocode is not created");              
        // }catch(Exception $e){
        //      return $this->sendError($e->getMessage(),'',500);
        // }   
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
                if(Promocode::where('id',$id)->exists()){
                    $Promocode = Promocode::where('id',$id)->first();
                    if(!empty($Promocode)){
                        return $this->sendResponse($Promocode, 'Promocode is found.');
                    }
                    return $this->sendError("Promocode is not found.");   
                }else{
                   return $this->sendError( 'Promocode  is not found.');  
               }
            }else{
              return $this->sendError("Promocode-id is empty.");   
            }
           
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
                if(Promocode::where('id',$id)->exists()){
                    $Promocode = Promocode::where('id',$id)->first();
                    if(!empty($Promocode)){
                            
                        $rulesParams = $Promocode->requiredRequestParams('update');
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                           return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        $prepareData = $Promocode->prepareUpdateData($inputs,$Promocode->toArray());
                        $isUpdated = $Promocode->update($prepareData);
                        if($isUpdated){
                        return $this->sendResponse([], 'Promocode details update successfully.');
                        }
                        return $this->sendError("Promocode details not updated.");     
                    }
                    return $this->sendError("Promocode is not found.");   
                }else{
                   return $this->sendError( 'Promocode  is not found.');  
               }
            }else{
              return $this->sendError("Promocode-id is empty.");   
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
                if(Promocode::where('id',$id)->exists()){
                    $Promocode = Promocode::where('id',$id)->first();
                    if(!empty($Promocode)){
                        $Promocode->delete();
                        return $this->sendResponse([],'Promocode remove successfully.');
                    }
                    return $this->sendError("Promocode is not found.");   
                }else{
                   return $this->sendError( 'Promocode  is not found.');  
               }
            }else{
              return $this->sendError("Promocode-id is empty.");   
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
    public function promocodeStatusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(Promocode::where('id',$id)->exists()){
                    $PromocodeInfo = Promocode::where('id',$id)->first();
                    $PromocodeInfoUpdate = $PromocodeInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Promocode is inactive.";
                                break;
                            case '1':
                                $msg = "Promocode is active.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($PromocodeInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Promocode is not updated.");
                    }
                }else{
                  return $this->sendError("Promocode is not found.");   
                }
            }else{
                return $this->sendError("Promocode-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

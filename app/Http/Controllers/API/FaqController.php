<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Faq;
use Validator;

class FaqController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{

            $faq = Faq::with('category')->get();
            if($faq->count() > 0){
                return $this->sendResponse($faq,"List of all faq.");
            }
            return $this->sendError('Faq is not found');
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
            $faq = new Faq();
            $rulesParams = $faq->requiredRequestParams('create');
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $faq->prepareCreateData($inputs);
            $createData['ip_address']=request()->ip();          
            $faqData = $faq->create($createData);
            if($faqData){
                return $this->sendResponse($faqData,"Faq is created successfully");
            }
            return $this->sendError("Faq is not created");              
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
                if(Faq::where('id',$id)->exists()){
                    $faq = Faq::with('category')->where('id',$id)->first();
                    if(!empty($faq)){
                        return $this->sendResponse($faq, 'Faq is found.');
                    }
                    return $this->sendError("Faq is not found.");   
                }else{
                   return $this->sendError( 'Faq  is not found.');  
               }
            }else{
              return $this->sendError("Faq-id is empty.");   
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
                if(Faq::where('id',$id)->exists()){
                    $faq = Faq::where('id',$id)->first();
                    if(!empty($faq)){
                            
                        $rulesParams = $faq->requiredRequestParams('update');
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                           return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        $prepareData = $faq->prepareUpdateData($inputs,$faq->toArray());
                        $isUpdated = $faq->update($prepareData);
                        if($isUpdated){
                        return $this->sendResponse([], 'Faq details update successfully.');
                        }
                        return $this->sendError("Faq details not updated.");     
                    }
                    return $this->sendError("Faq is not found.");   
                }else{
                   return $this->sendError( 'Faq  is not found.');  
               }
            }else{
              return $this->sendError("Faq-id is empty.");   
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
                if(Faq::where('id',$id)->exists()){
                    $faq = Faq::where('id',$id)->first();
                    if(!empty($faq)){
                        $faq->delete();
                        return $this->sendResponse([],'Faq remove successfully.');
                    }
                    return $this->sendError("Faq is not found.");   
                }else{
                   return $this->sendError( 'Faq  is not found.');  
               }
            }else{
              return $this->sendError("Faq-id is empty.");   
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
                if(Faq::where('id',$id)->exists()){
                    $faqInfo = Faq::where('id',$id)->first();
                    $faqInfoUpdate = $faqInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Faq is inactive.";
                                break;
                            case '1':
                                $msg = "Faq is active.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($faqInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Faq is not updated.");
                    }
                }else{
                  return $this->sendError("Faq is not found.");   
                }
            }else{
                return $this->sendError("Faq-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

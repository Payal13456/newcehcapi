<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Diagnosis;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;

class DiagnosisController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

            $Diagnosis = new Diagnosis();
            $rulesParams = [
                    'name'=> 'required',
                    'instruction'=> 'required',
                    'appointment_id'=>'required'    
            ];

            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
                return $this->sendError($validator->getMessageBag()->first(),[]);
            }

            $data = ['name' => $inputs['name'],'instruction' => $inputs['instruction'],'appointment_id'=>$inputs['appointment_id']];

            $DiagnosisData = $Diagnosis->create($data);

            if($DiagnosisData){
                return $this->sendResponse([],"Diagnosis is created successfull");
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
                if(Diagnosis::where('id',$id)->exists()){
                    $Diagnosis = Diagnosis::where('id',$id)->first();
                    if(!empty($Diagnosis)){
                         return $this->sendResponse($Diagnosis, 'Diagnosis is found.'); 
                    }else{
                        return $this->sendError('Diagnosis is not found');               
                    }
                }else{
                    return $this->sendError('Diagnosis-id is not found');               
                }
            }else{
                return $this->sendError('Diagnosis-id is empty');                
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
                if(Diagnosis::where('id',$id)->exists()){
                    $Diagnosis = Diagnosis::where('id',$id)->first();
                    if(!empty($Diagnosis)){
                        $inputs = $request->input();
                        $rulesParams = [
                                'name'=> 'required',
                                'instruction'=> 'required',
                                'appointment_id'=>'required'    
                        ];

                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                            return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        $updateData = ['name' => $inputs['name'],'instruction' => $inputs['instruction'],'appointment_id'=>$inputs['appointment_id']];
                        $isUpdated = $Diagnosis->update($updateData);
                        if($isUpdated){
                            return $this->sendResponse([],"Diagnosis details is updated successfull");
                        }
                         return $this->sendError('Diagnosis details is not updated'); 
                    }else{
                        return $this->sendError('Diagnosis details is not found');               
                    }
                }else{
                    return $this->sendError('Diagnosis-id is not found');               
                }
            }else{
                return $this->sendError('Diagnosis-id is empty');                
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
                if(Diagnosis::where('id',$id)->exists()){
                    $Diagnosis = Diagnosis::where('id',$id)->first();
                    if(!empty($Diagnosis)){
                       $Diagnosis->delete();
                         return $this->sendResponse([], 'Diagnosis is deleted successfull.'); 
                    }else{
                        return $this->sendError('Diagnosis is not found');               
                    }
                }else{
                    return $this->sendError('Diagnosis-id is not found');               
                }
            }else{
                return $this->sendError('Diagnosis-id is empty');                
            }
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function diagnosisList($appointment_id){
        try{
            $summary = Diagnosis::where('appointment_id',$appointment_id)->get();
            if($summary->count() > 0){
                return $this->sendResponse($summary,"Diagnosis List of appointment");
            }
            return $this->sendError('Diagnosis is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function createDiagnosis(Request $request){
        try{
            $data = []; $ids = []; $appointment_id = "";
            $inputs = $request->input();
            
            if(count($inputs['diagnosis']) > 0){
                foreach($inputs['diagnosis'] as $k=>$diagnosis){
                    if(isset($diagnosis['id']) && $diagnosis['id'] != 0){
                        $ids [] = $diagnosis['id'];
                        $appointment_id = $diagnosis['appointment_id'];
                        $update = [
                            'name'=> $diagnosis['name'],
                            'instruction'=> $diagnosis['instruction'],
                            'appointment_id'=>$diagnosis['appointment_id']    
                        ];
                        // echo "<pre>";print_r($update);die;

                        Diagnosis::where('id',$diagnosis['id'])->update($update);
                    }else{
                        $data[] = [
                            'name'=> $diagnosis['name'],
                            'instruction'=> $diagnosis['instruction'],
                            'appointment_id'=>$diagnosis['appointment_id']    
                        ];
                          // echo "<pre>";print_r($data);die;
                    }
                }

                if(count($ids) > 0 && !empty($appointment_id)){
                    Diagnosis::whereNotIn('id',$ids)->where('appointment_id',$appointment_id)->delete();
                }

                Diagnosis::insert($data);
            }
            return $this->sendResponse([],"Diagnosis is created successfull");
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Medicine;
use App\Models\EyeDetail;
class PrescriptionController extends BaseController
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
            $Prescription = new Prescription();
            $rulesParams = [
                    'medicine_name'=> 'required',
                    'medicine_type'=> 'required',
                    'dose'=> 'required',
                    'timing'=> 'required',
                    'appointment_id'=>'required',
                    'food'=> 'required',
                    'mg_ml' => 'required',    
                    'days' => 'required'
            ];
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
                return $this->sendError($validator->getMessageBag()->first(),[]);
            }

            $medicine_name = $inputs['medicine_name'];

            $check = Medicine::where('medicine_name','LIKE',$medicine_name)->first();

            if(!empty($check)){
                $medicine_id = $check->id;
            }else{
                $medicine_id = Medicine::insertGetId(['medicine_name'=>$medicine_name, 'generic_name' => $inputs['generic_medicine_name']]);
            }

            $data = [
                'medicine_id'=> $medicine_id,
                'medicine_type'=> $inputs['medicine_type'],
                'dose'=> $inputs['dose'],
                'timing'=> $inputs['timing'],
                'food'=> $inputs['food'],
                'mg_ml' => $inputs['mg_ml'],
                'duration' => $inputs['days'],
                'instruction'=> isset($inputs['instruction'])? $inputs['instruction']:'',
                'appointment_id'=>$inputs['appointment_id']    
            ];

            $PrescriptionData = $Prescription->create($data);
            if($PrescriptionData){
                return $this->sendResponse([],"Prescription is created successfull");
            }
        } catch(Exception $e){
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
            $Prescription = Prescription::with('medicine')->where('id',$id)->first();
            if(!empty($Prescription)){
                return $this->sendResponse($Prescription,"Prescription details");
            }
            return $this->sendError('Prescription is not found');
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
            $inputs = $request->input();
            $Prescription = Prescription::find($id);
            $rulesParams = [
                'medicine_name'=> 'required',
                'medicine_type'=> 'required',
                'dose'=> 'required',
                'timing'=> 'required',
                'appointment_id'=>'required',
                'food'=> 'required',
                'mg_ml' => 'required',    
                'dose' => 'required'   
            ];
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }

            $medicine_name = $inputs['medicine_name'];

            $check = Medicine::where('medicine_name','LIKE',$medicine_name)->first();

            if(!empty($check)){
                $medicine_id = $check->id;
            }else{
                $medicine_id = Medicine::insertGetId(['medicine_name'=>$medicine_name , 'generic_name' => $inputs['generic_medicine_name']]);
            }

            $data = [
                'medicine_id'=> $medicine_id,
                'medicine_type'=> $inputs['medicine_type'],
                'dose'=> $inputs['dose'],
                'timing'=> $inputs['timing'],
                'food'=> $inputs['food'],
                'mg_ml' => $inputs['mg_ml'],
                'duration' => $inputs['days'],
                'instruction'=> isset($inputs['instruction'])? $inputs['instruction']:'',
                'appointment_id'=>$inputs['appointment_id']    
            ];

            $PrescriptionData = $Prescription->update($data);

            if($PrescriptionData){
                return $this->sendResponse([],"Prescription is updated successfully");
            }
        } catch(Exception $e){
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
                if(Prescription::where('id',$id)->exists()){
                    $Prescription = Prescription::where('id',$id)->first();
                    if(!empty($Prescription)){
                       $Prescription->delete();
                         return $this->sendResponse([], 'Prescription is deleted successfull.'); 
                    }else{
                        return $this->sendError('Prescription is not found');               
                    }
                }else{
                    return $this->sendError('Prescription-id is not found');               
                }
            }else{
                return $this->sendError('Prescription-id is empty');                
            }
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function prescriptionDetails($appointment_id){
        try{
            $Prescription = Prescription::with('medicine')->where('appointment_id',$appointment_id)->get();
            // echo '<pre>';print_r($Prescription);die;
            if($Prescription->count() > 0){
                return $this->sendResponse($Prescription,"Prescription List of appointment");
            }
            return $this->sendError('Prescription is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function searchMedicine($keyword){
        try{
            $medicine = Medicine::where('medicine_name','LIKE',"%".$keyword."%")->get();
            // echo "<pre>";print_r($medicine);die;
            if($medicine->count() > 0){
                return $this->sendResponse($medicine,"Medicine List");
            }
            return $this->sendError('Medicine is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function getMedicine(){
        try{
            $medicine = Medicine::all();
            // echo "<pre>";print_r($medicine);die;
            if($medicine->count() > 0){
                return $this->sendResponse($medicine,"Medicine List");
            }
            return $this->sendError('Medicine is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }


    public function createPrescription(Request $request){
        try{
            $data = []; $ids = []; $appointment_id = "";
            $inputs = $request->input();
                if(count($inputs['medicine']) > 0){
                    foreach($inputs['medicine'] as $k=>$medicine){
                        // echo "<pre>";print_r($medicine['medicine_name']);die;
                        $check = Medicine::where('medicine_name','LIKE',$medicine['medicine_name'])->first();
                        if(!empty($check)){
                            $medicine_id = $check->id;
                        }else{
                            $medicine_id = Medicine::insertGetId(['medicine_name'=>$medicine['medicine_name']]);
                        }
                        if(isset($medicine['id']) && $medicine['id'] != 0){
                            $ids [] = $medicine['id'];
                            $appointment_id = $medicine['appointment_id'];
                            $update = [
                                'medicine_id'=> $medicine_id,
                                'medicine_type'=> $medicine['medicine_type'],
                                'duration'=> $medicine['duration'],
                                'dose'=> $medicine['dose'],
                                'timing'=> $medicine['timing'],
                                'food'=> $medicine['food'],
                                'mg_ml' => $medicine['mg_ml'],
                                'instruction'=> isset($medicine['instruction'])? $medicine['instruction']:'',
                                'appointment_id'=>$medicine['appointment_id']    
                            ];
                            // echo "<pre>";print_r($update);die;

                            Prescription::where('id',$medicine['id'])->update($update);
                        }else{
                            $data[] = [
                                'medicine_id'=> $medicine_id,
                                'medicine_type'=> $medicine['medicine_type'],
                                'duration'=> $medicine['duration'],
                                'dose'=> $medicine['dose'],
                                'timing'=> $medicine['timing'],
                                'food'=> $medicine['food'],
                                'mg_ml' => $medicine['mg_ml'],
                                'instruction'=> isset($medicine['instruction'])? $medicine['instruction']:'',
                                'appointment_id'=>$medicine['appointment_id']    
                            ];
                              // echo "<pre>";print_r($data);die;
                        }
                    }

                    if(count($ids) > 0 && !empty($appointment_id)){
                        Prescription::whereNotIn('id',$ids)->where('appointment_id',$appointment_id)->delete();
                    }
                    Prescription::insert($data);
                }
            return $this->sendResponse([],"Prescription is created successfull");
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }
    public function medicines($keyword){
        try{
            $data = Medicine::where('medicine_name','LIKE',"'%".$keyword."%'")->get();
            return $this->sendResponse($data,'Medicine List');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function createOptics(Request $request){
        try{
            $eyedata = [];
            $inputs = $request->input();
            if(!empty($inputs['dsph_l'][0]) && !empty($inputs['dcyl_l'][0]) && !empty($inputs['axis_l'][0]) && !empty($inputs['va_l'][0])){
                $eyedata[] = [
                    'dsph' => $inputs['dsph_l'][0],
                    'dcyl' => $inputs['dcyl_l'][0],
                    'axis' => $inputs['axis_l'][0],
                    'va' => $inputs['va_l'][0],
                    'type' => $inputs['type'][0],
                    'eye_details' => 'left',
                    'remark' => $inputs['remark'],
                    'ipd' => $inputs['ipd'],
                    'appointment_id' => $inputs['appointment_id']
                ];
            }
            if(!empty($inputs['dsph_l'][1]) && !empty($inputs['dcyl_l'][1]) && !empty($inputs['axis_l'][1]) && !empty($inputs['va_l'][1])){
                $eyedata[] = [
                    'dsph' => $inputs['dsph_l'][1],
                    'dcyl' => $inputs['dcyl_l'][1],
                    'axis' => $inputs['axis_l'][1],
                    'va' => $inputs['va_l'][1],
                    'type' => $inputs['type'][1],
                    'remark' => $inputs['remark'],
                    'ipd' => $inputs['ipd'],
                    'eye_details' => 'left',
                    'appointment_id' => $inputs['appointment_id']
                ];
            }
            if(!empty($inputs['dsph_r'][0]) && !empty($inputs['dcyl_r'][0]) && !empty($inputs['axis_r'][0]) && !empty($inputs['va_r'][0])){
                $eyedata[] = [
                    'dsph' => $inputs['dsph_r'][0],
                    'dcyl' => $inputs['dcyl_r'][0],
                    'axis' => $inputs['axis_r'][0],
                    'va' => $inputs['va_r'][0],
                    'type' => $inputs['type'][0],
                    'remark' => $inputs['remark'],
                    'ipd' => $inputs['ipd'],
                    'eye_details' => 'right',
                    'appointment_id' => $inputs['appointment_id']
                ];
            }
            if(!empty($inputs['dsph_r'][1]) && !empty($inputs['dcyl_r'][1]) && !empty($inputs['axis_r'][1]) && !empty($inputs['va_r'][1])){
                $eyedata[] = [
                    'dsph' => $inputs['dsph_r'][1],
                    'dcyl' => $inputs['dcyl_r'][1],
                    'axis' => $inputs['axis_r'][1],
                    'va' => $inputs['va_r'][1],
                    'type' => $inputs['type'][1],
                    'remark' => $inputs['remark'],
                    'ipd' => $inputs['ipd'],
                    'eye_details' => 'right',
                    'appointment_id' => $inputs['appointment_id']
                ];
            }
            $check = EyeDetail::where('appointment_id',$inputs['appointment_id'])->get();
            // echo "<pre>";print_r($check);die;
            if(count($eyedata) > 0){
                if(count($check) > 0){
                    foreach($eyedata as $data){
                        $c = EyeDetail::where('appointment_id',$inputs['appointment_id'])->where('type',$data['type'])->where('eye_details',$data['eye_details'])->first();
                        if(!empty($c)){
                            $c = EyeDetail::where('appointment_id',$inputs['appointment_id'])->where('type',$data['type'])->where('eye_details',$data['eye_details'])->update($data);
                        }else{
                            EyeDetail::insert($data);
                        }
                    }
                }else{                        
                    EyeDetail::insert($eyedata);
                }
            }
            return $this->sendResponse([],"Optics is entered successfull");
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function opticsList($id){
        try{
            $data = EyeDetail::where('appointment_id',$id)->get();
            return $this->sendResponse($data,"Optics list");
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function addOptics(Request $request){
        try{
            $appointment_id = '';
            $eyedata = [];
            $inputs = $request->input();
            if(isset($inputs['optics'])){
                foreach($inputs['optics'] as $optics){
                    $appointment_id = $optics['appointment_id'];
                    $eyedata[] = [
                        'dsph' => $optics['dsph'],
                        'dcyl' => $optics['dcyl'],
                        'axis' => $optics['axis'],
                        'va' => $optics['va'],
                        'type' => $optics['type'],
                        'remark' => $optics['remark'],
                        'ipd' => $optics['ipd'],
                        'eye_details' => $optics['eye_details'],
                        'appointment_id' => $optics['appointment_id']
                    ];
                }
            }

            $check = EyeDetail::where('appointment_id',$appointment_id)->get();
            // echo "<pre>";print_r($check);die;
            if(count($eyedata) > 0){
                if(count($check) > 0){
                    foreach($eyedata as $data){
                        $c = EyeDetail::where('appointment_id',$appointment_id)->where('type',$data['type'])->where('eye_details',$data['eye_details'])->first();
                        if(!empty($c)){
                            $c = EyeDetail::where('appointment_id',$appointment_id)->where('type',$data['type'])->where('eye_details',$data['eye_details'])->update($data);
                        }else{
                            EyeDetail::insert($data);
                        }
                    }
                }else{                        
                    EyeDetail::insert($eyedata);
                }
            }
            return $this->sendResponse([],"Optics is entered successfull");
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }
}

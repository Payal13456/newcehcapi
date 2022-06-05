<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Exception;
use App\Models\Schedule;
use Validator;
use App\Models\Appointment;
use App\Models\UserInfomation;

class ScheduleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $specialization = Schedule::with('userinfo')->get();
            // echo "<pre>";print_r($specialization);die;
            if($specialization->count() > 0){
                return $this->sendResponse($specialization, 'List of all Schedule.');
            }
            return $this->sendError('Schedule is not found');
        }catch(Exception $ex){
            return $this->sendError($ex->getMessage(),'',500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function doctorsList()
    {
        try{
            $specialization = Schedule::with('userinfo')->groupBy('doctor_id')->get();
            // echo "<pre>";print_r($specialization);die;
            if($specialization->count() > 0){
                return $this->sendResponse($specialization, 'List of scheduled doctors.');
            }
            return $this->sendError('Scheduled doctors not found');
        }catch(Exception $ex){
            return $this->sendError($ex->getMessage(),'',500);
        }
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
            
            $validator = Validator::make($inputs, [
                'doctor_id' => 'required',
            ]);
            // return json_encode($request->all());
            if($validator->fails()){
                return $this->sendError("Validation error",$validator->errors());
            }
            
            $data = [];
            for($i=0;$i<=6;$i++){
                if(isset($inputs['day'.$i])){
                    foreach($inputs['start_time'.$i] as $k=>$days){
                        $check = Schedule::where('day',$request->input('day'.$i))->where('type',$request->input('type'.$i)[$k])->first();
                        if(!empty($check)){
                            $update = [
                                'doctor_id' =>$request->input('doctor_id'),
                                'day' => $request->input('day'.$i),
                                'start_time' => $request->input('start_time'.$i)[$k],
                                'end_time' => $request->input('end_time'.$i)[$k],
                                'break' => $request->input('break'.$i)[$k],
                                'type' => $request->input('type'.$i)[$k],
                                'appointment_mode' => implode(' ',$request->input('appointment_mode'.$i)[$k]),
                                'is_disable' => !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0
                            ];

                            $scheduleData = Schedule::where('id',$check->id)->update($update);
                        }else{
                            $data = [
                                'doctor_id' =>$request->input('doctor_id'),
                                'day' => $request->input('day'.$i),
                                'start_time' => $request->input('start_time'.$i)[$k],
                                'end_time' => $request->input('end_time'.$i)[$k],
                                'break' => $request->input('break'.$i)[$k],
                                'type' => $request->input('type'.$i)[$k],
                                'appointment_mode' => implode(' ',$request->input('appointment_mode'.$i)[$k]),
                                'is_disable' => !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0
                            ];

                            $schedule = new Schedule();
                            $scheduleData = $schedule->create($data);
                        }
                    }
                }
            }
            // echo "<pre>";print_r($data);die;
            // foreach($data as $d){
            //     $schedule = new Schedule();
            //     $scheduleData = $schedule->create($d);
            // }
            
            if($scheduleData){
                return $this->sendResponse([],"Schedule is created successfull");
            }
            return $this->sendError("Schedule is not created."); 
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
            $specialization = Schedule::select('day')->where('doctor_id',$id)->groupBy('day')->get();
            foreach ($specialization as $key => $value) {
                $getDetails = Schedule::with('userinfo')->where('status','!=',2)->where('doctor_id',$id)->where('day',$value->day)->get();
                $value->details = $getDetails;

                $disabled = Schedule::with('userinfo')->where('status',2)->where('doctor_id',$id)->where('day',$value->day)->get();

                $value->disabled = $disabled;
            }


            if(!empty($specialization)){
                return $this->sendResponse($specialization, 'Scheduled doctor details.');
            }
            return $this->sendError('Scheduled doctor details is not found');
        } catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function scheduleDetails($id)
    {
        try{
            $data = [];
            $userinfo = UserInfomation::where('user_id',$id)->first();
            $specialization = Schedule::select('day')->where('doctor_id',$id)->groupBy('day')->get();
            //
            $data['userinfo'] = $userinfo;
            foreach ($specialization as $key => $value) {
                $getDetails = Schedule::where('doctor_id',$id)->where('day',$value->day)->get();
                $value->details = $getDetails;
                // $disabled = Schedule::with('userinfo')->where('status',2)->where('doctor_id',$id)->where('day',$value->day)->get();
                // $value->disabled = $disabled;
                $data[$value->day] = $getDetails;
            }
             // echo "<pre>";print_r($data);die;
            if(!empty($specialization)){
                return $this->sendResponse($data, 'Scheduled doctor details.');
            }
            return $this->sendError('Scheduled doctor details is not found');
        } catch(Exception $e){
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
            
            $validator = Validator::make($inputs, [
                'doctor_id' => 'required',
            ]);
            // return json_encode($request->all());
            // return $this->sendResponse($request->all(),"Schedule is created successfull");
            if($validator->fails()){
                return $this->sendError("Validation error",$validator->errors());
            }

            $recentData = Schedule::where('doctor_id',$id)->get();
            
            // if(empty($recentData)){
                $data = []; $data1 = [];
                for($i=0;$i<=6;$i++){
                    if(isset($inputs['day'.$i])){
                        foreach($inputs['start_time'.$i] as $k=>$days){
                            // echo $request->input('ids'.$id)[$k];die;
                            $check = Schedule::where('day',$request->input('day'.$i))->where('type',$request->input('type'.$i)[$k])->first();
                            if(!empty($check)){
                                $scheduleids [] = $check->id;
                                $data1 = [
                                    'id' => $check->id,
                                    'doctor_id' =>$request->input('doctor_id'),
                                    'day' => $request->input('day'.$i),
                                    'start_time' => $request->input('start_time'.$i)[$k],
                                    'end_time' => $request->input('end_time'.$i)[$k],
                                    'break' => $request->input('break'.$i)[$k],
                                    'type' => $request->input('type'.$i)[$k],
                                    'appointment_mode' => implode(' ',$request->input('appointment_mode'.$i)[$k]),
                                    'is_disable' => !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0,
                                    'status' => !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0
                                ];

                                $scheduleData = Schedule::where('id',$check->id)->update($data1);
                            }else{
                                if($request->input('ids'.$i) != NULL && isset($request->input('ids'.$i)[$k])){
                                    $scheduleids [] = $request->input('ids'.$i)[$k];
                                    $data1 = [
                                        'id' => $request->input('ids'.$i)[$k],
                                        'doctor_id' =>$request->input('doctor_id'),
                                        'day' => $request->input('day'.$i),
                                        'start_time' => $request->input('start_time'.$i)[$k],
                                        'end_time' => $request->input('end_time'.$i)[$k],
                                          'break' => $request->input('break'.$i)[$k],
                                        'type' => $request->input('type'.$i)[$k],
                                        'appointment_mode' => implode(' ',$request->input('appointment_mode'.$i)[$k]),
                                        'is_disable' => !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0,
                                        'status' => !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0
                                    ];
                                    $scheduleData = Schedule::where('id',$request->input('ids'.$i)[$k])->update($data1);
                                }else{
                                    $data [] = [
                                        'doctor_id' =>$request->input('doctor_id'),
                                        'day' => $request->input('day'.$i),
                                        'start_time' => $request->input('start_time'.$i)[$k],
                                        'end_time' => $request->input('end_time'.$i)[$k],
                                        'break' => $request->input('break'.$i)[$k],
                                        'type' => $request->input('type'.$i)[$k],
                                        'appointment_mode' => implode(' ',$request->input('appointment_mode'.$i)[$k]),
                                        'is_disable' => !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0,
                                        'status' =>  !empty($request->input('is_disable'.$i)[$k]) ? $request->input('is_disable'.$i)[$k] : 0
                                    ];
                                }
                            }
                        }
                    }
                }
                
                foreach($data as $d){
                    $schedule = new Schedule();
                    $scheduleData = $schedule->create($d);
                }

                Schedule::whereNotIn('id',$scheduleids)->where('doctor_id',$id)->update(['status'=>2]);
            
            if($scheduleData){
                return $this->sendResponse([],"Schedule is created successfull");
            }
            return $this->sendError("Schedule is not created."); 
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function scheduleStatusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(Schedule::where('id',$id)->exists()){
                    $ScheduleInfo = Schedule::where('id',$id)->first();
                    $ScheduleInfoUpdate = $ScheduleInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Schedule is enabled.";
                                break;
                            case '1':
                                $msg = "Schedule is disabled.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($ScheduleInfoUpdate){
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

    public function doctorSchedule(Request $request){
        try{  
            if(!empty($request->id) && !empty($request->input('date'))){
                $day = strtolower(date("l",strtotime($request->input('date'))));
                $ScheduleInfo = Schedule::where('doctor_id',$request->input('id'))->where('appointment_mode','LIKE','%'.$request->input('type').'%')->where('day',$day)->where('is_disable',0)->where('break','!=',0)->get();
                // echo "<pre>";print_r($ScheduleInfo);die;
                $res = [];$slots = [];
                if(count($ScheduleInfo) > 0){
                    foreach($ScheduleInfo as $schedule){
                        $starttime = strtotime($schedule->start_time);
                        $endtime = strtotime($schedule->end_time);
                        $meetingSlotInMinute = ( $schedule->break  * 60);
                        $slot_time = $starttime;
                        while($slot_time <= $endtime){
                            if(!in_array(date("h:i A",$slot_time), $slots)){
                                $slots [] = date("h:i A",$slot_time);
                            }
                            $slot_time += $meetingSlotInMinute;
                        }
                        $res['slots'] = $slots;
                        $bookedSlots = Appointment::select('slot_timing')->where('doctor_id',$request->input('id'))->where("schedule_date",$request->input("date"))->where("is_cancel",0)->get();
                        $bookedS = [];
                        foreach($bookedSlots as $slotss){
                            $bookedS [] = date("h:i A",strtotime($slotss->slot_timing));
                        }
                        $res['bookedSlots'] = $bookedS;
                    }
                    return $this->sendResponse($res, 'Slot details.');
                }else{
                    return $this->sendError("Slots not available."); 
                }
            }else{
                return $this->sendError("Doctor-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function createSchedule(Request $request){
        try{
            $inputs = $request->input();
            $update = []; $data = []; $ids = [];
            $doctor_id = $inputs['doctor_id'];
            $type = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
            // $tt = ['morning','afternoon','evening','night'];

            foreach($type as $k=>$t){
                foreach($inputs['days'][0] as $ks=>$days){
                    if($t == $ks){
                        foreach ($days as $value) {
                            $check = Schedule::where('day',$ks)->where('type',$value['type'])->first();
                            if(!empty($check)){
                                $update [] = ["day"=> $ks, "doctor_id"=>$doctor_id, "type"=>$value['type'], "start_time"=>$value['start_time'], "end_time"=>$value['end_time'], "break"=>$value['break'], "id"=>$check->id, "is_disable"=>$value['is_disable'],"status"=>0,"appointment_mode"=>$value['appointment_mode']];
                            }else{
                                if(isset($value['id']) && $value['id'] != 0 ){
                                    $ids [] = $value['id'];
                                    $update [] = ["day"=> $ks, "doctor_id"=>$doctor_id, "type"=>$value['type'], "start_time"=>$value['start_time'], "end_time"=>$value['end_time'], "break"=>$value['break'], "id"=>$value['id'], "is_disable"=>$value['is_disable'],"status"=>0,"appointment_mode"=>$value['appointment_mode']];
                                }else{
                                    $data [] = ["day"=> $ks, "doctor_id"=>$doctor_id, "type"=>$value['type'], "start_time"=>$value['start_time'], "end_time"=>$value['end_time'], "break"=>$value['break'], "is_disable"=>$value['is_disable'],"status"=>0,"appointment_mode"=>$value['appointment_mode']];
                                }
                            }
                        }
                    }
                }
            }

            // $delete = Schedule::where('doctor_id',$doctor_id)->whereNotIn('id',$ids)->delete();
            // echo '<pre>';print_r($update);die;
            if(count($update) > 0){
                foreach ($update as $key => $value) {
                    $update = Schedule::where('id',$value['id'])->update($value);
                }
            }

            if(count($data) > 0){
                foreach ($data as $key => $value) {
                    $create = Schedule::insert($value);
                }
            }

            return $this->sendResponse([], 'Schedule Created successfully.');

        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function disableSchedule($doctor_id,$day){
        try{
            $data = Schedule::where('doctor_id',$doctor_id)->get();
            // echo "<pre>";print_r($data);die;
            foreach($data as $d){
                if($d->status == 2){
                    Schedule::where('doctor_id',$doctor_id)->where('day','LIKE',$day)->update(['status'=>0,'is_disable'=>0]);
                }else{
                    Schedule::where('doctor_id',$doctor_id)->where('day','LIKE',$day)->update(['status'=>2,'is_disable'=>1]);
                }
            }
            $schedules = Schedule::where('doctor_id',$doctor_id)->where('status','!=',2)->get();
            
            return $this->sendResponse($schedules, 'Schedule Created successfully.');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function getScheduleStatus($doctor_id){
        try{
            $data = [];
            $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
            foreach($days as $k=>$d){
                $schedules = Schedule::where('doctor_id',$doctor_id)->where('day','LIKE',$d)->where('is_disable',0)->where('status','!=',2)->get();
                if(count($schedules) > 0){
                    $status = Schedule::where('doctor_id',$doctor_id)->where('day','LIKE',$d)->get();
                    if(count($status) > 0){
                        $data[] = ['is_disable' => 0 , 'slot' => true];
                    }else{
                        $data[] = ['is_disable' => 0 , 'slot' => false];
                    }
                }else{
                    $status = Schedule::where('doctor_id',$doctor_id)->where('day','LIKE',$d)->get();
                    if(count($status) > 0){
                        $data[] = ['is_disable' => 1 , 'slot' => true];
                    }else{
                        $data[] = ['is_disable' => 1 , 'slot' => false];
                    }
                }
            }
            
            return $this->sendResponse($data, 'Schedule Status.');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    public function doctorsByDate($id , $date){
        try{
        $day = strtolower(date("l",strtotime($date)));
            $ids = [];
            $ids = Schedule::where('day',$day)->where('is_disable',0)->where('break','!=',0)->pluck('doctor_id')->all();

            $states = UserInfomation::where('specialization_id',$id)->whereIn('user_id',$ids)->where('role_id',1)->get();

            if($states->count() > 0){    
                  return $this->sendResponse($states, 'List of all doctors.');
            }
            return $this->sendError('doctors not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseSummary;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;

class CasesummaryController extends BaseController
{
   
    public function summary($appointment_id)
    {
        try{
            $summary = CaseSummary::where('appointment_id',$appointment_id)->first();
            if(!empty($summary)){
                return $this->sendResponse($summary,"Case Summary of appointment");
            }
             return $this->sendError('Case Summary is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
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

            $check = CaseSummary::where('appointment_id',$inputs['appointment_id'])->first();
            if(!empty($check)){
                $rulesParams = [
                        'case_summary'=> 'required',
                        'appointment_id'=>'required'    
                ];
                $validator = Validator::make($inputs,$rulesParams);
                if($validator->fails()){
                    return $this->sendError($validator->getMessageBag()->first(),[]);
                }

                $check->case_summary = $inputs['case_summary'];
                $check->save();

                return $this->sendResponse([],"Case Summary is updated successfull");

            }
            // print_r($inputs);die();
            $summary = new CaseSummary();
            $rulesParams = [
                    'case_summary'=> 'required',
                    'appointment_id'=>'required'    
            ];

            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
                return $this->sendError($validator->getMessageBag()->first(),[]);
            }

            $data = ['case_summary' => $inputs['case_summary'],'appointment_id'=>$inputs['appointment_id']];
            $summaryData = $summary->create($data);
            if($summaryData){
                return $this->sendResponse([],"Case Summary is created successfull");
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
        //
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
        //
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
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Razorpay\Api\Api;
use App\Models\Payment;
use App\Models\Appointment;
use Session;
use Redirect;
use App\Models\Plan;

class RazorPaymentController extends BaseController
{
    public function payment(Request $request)
    {
        //Input items of form
        $input = $request->all();
        if($input['type'] == 'online'){
            // echo "<pre>";print_r($input);die;
            //get API Configuration 
            //$api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            //$api = new Api('rzp_live_lRU6KX64RykLIM', 'KUgLXdRMXErrJi0TYXxvHIbR');
            $api = new Api('rzp_test_2Tk2nyPsn3CKKV', 'vIhoCQxXjPxmcw0RV1ZiiFcD');
            //Fetch payment information by razorpay_payment_id
            $payment = $api->payment->fetch($input['razorpay_payment_id']);
	     $plan = Plan::orderBy('id','DESC')->first();
            if(count($input)  && !empty($input['razorpay_payment_id'])) {
                try {
                    $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount'])); 
                    //$data = ['payment_id' => $input['razorpay_payment_id'],'total'=>$payment['amount'],'payment_mode'=>'online'];
		    if(!empty($plan)){
		            $data = ['payment_id' =>  $input['razorpay_payment_id'],'total'=>$payment['amount'],'payment_mode'=>'online','consultation_fees'=>$plan->consultation_fees,"booking_fees" => $plan->booking_fees , "total_amount" => $plan->total_amount_after_gst];
		    }else{
			    $data = ['payment_id' =>  $input['razorpay_payment_id'] ,'total'=>$payment['amount'],'payment_mode'=>'online','consultation_fees'=>600,"booking_fees" => 50,"total_amount"=>650];
		    }
                    $pay = Payment::create($data);

                    Appointment::where('id',$input['appointment_id'])->update(['payment_id'=>$pay->id]);

                    return $this->sendResponse($pay,"Payment successful.");
                } catch (\Exception $e) {
                   return $this->sendError($e->getMessage(),'',500);
                }
            }
        }else{
            $plan = Plan::orderBy('id','DESC')->first();
            if(!empty($plan)){
                    $data = ['payment_id' => "",'total'=>intval($plan->total_amount_after_gst)*100,'payment_mode'=>'offline','consultation_fees'=>$plan->consultation_fees,"booking_fees" => $plan->booking_fees,"total_amount" => $plan->total_amount_after_gst];
            }else{
	            $data = ['payment_id' => "",'total'=>65000,'payment_mode'=>'offline','consultation_fees'=>600,"booking_fees" => 50,"total_amount"=>650];
	    }
            
            $pay = Payment::create($data);

            Appointment::where('id',$input['appointment_id'])->update(['payment_id'=>$pay->id]);

            return $this->sendResponse($pay,"Payment successful.");
        }

        return $this->sendResponse([],"Retry payment");
    }
    
    public function paymentForApp($appointment_id,$type,$razorpay_payment_id=0)
    {
        //Input items of form
        if($type == 'online'){
            // echo "<pre>";print_r($input);die;
            //get API Configuration 
            //$api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            //$api = new Api('rzp_live_lRU6KX64RykLIM', 'KUgLXdRMXErrJi0TYXxvHIbR');
            $api = new Api('rzp_test_2Tk2nyPsn3CKKV', 'vIhoCQxXjPxmcw0RV1ZiiFcD');            
            //Fetch payment information by razorpay_payment_id
            
            $payment = $api->payment->fetch($razorpay_payment_id);

            if(!empty($razorpay_payment_id) && $razorpay_payment_id != 0) {
                    $response = $api->payment->fetch($razorpay_payment_id)->capture(array('amount'=>$payment['amount'])); 
                    $data = ['payment_id' => $razorpay_payment_id,'total'=>$payment['amount'],'payment_mode'=>'online'];
	 	    if(!empty($plan)){
		            $data = ['payment_id' => $razorpay_payment_id,'total'=>$payment['amount'],'payment_mode'=>'online','consultation_fees'=>$plan->consultation_fees,"booking_fees" => $plan->booking_fees];
		    }else{
			    $data = ['payment_id' => $razorpay_payment_id,'total'=>$payment['amount'],'payment_mode'=>'online','consultation_fees'=>600,"booking_fees" => 50];
		    }
                    $pay = Payment::create($data);

                    Appointment::where('id',$appointment_id)->update(['payment_id'=>$pay->id]);

                    return true;
            }
        }else{
            $plan = Plan::orderBy('id','DESC')->first();
            if(!empty($plan)){
                    $data = ['payment_id' => "",'total'=>intval($plan->total_amount_after_gst)*100,'payment_mode'=>'offline','consultation_fees'=>$plan->consultation_fees,"booking_fees" => $plan->booking_fees];
            }else{
	            $data = ['payment_id' => "",'total'=>65000,'payment_mode'=>'offline','consultation_fees'=>600,"booking_fees" => 50];
	    }

            $pay = Payment::create($data);

            Appointment::where('id',$appointment_id)->update(['payment_id'=>$pay->id]);
            
             return true;

            //return $this->sendResponse($pay,"Payment successful.");
        }
        
         return true;

      //  return $this->sendResponse([],"Retry payment");
    }

    public function refund($payment_id)
    {
        //Input items of form
        
        //get API Configuration 
        $paymentData = Payment::find($payment_id);
        // echo "<pre>";print_r($paymentData);die;
        if($paymentData->payment_mode == 'online'){
            // echo "Here";die;
            $paymentId = $paymentData->payment_id;
		
            //$api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            //$api = new Api('rzp_live_lRU6KX64RykLIM', 'KUgLXdRMXErrJi0TYXxvHIbR');
            $api = new Api('rzp_test_2Tk2nyPsn3CKKV', 'vIhoCQxXjPxmcw0RV1ZiiFcD');            
            //Fetch payment information by razorpay_payment_id
            $payment = $api->payment->fetch($paymentId);
		//echo "<pre>";print_r($payment);die;
            if(!empty($payment)) {
		     $response = $api->payment->fetch($paymentId)->refund(array("amount"=> $payment['amount'], "speed"=>"normal", "notes"=>array("notes_key_1"=>"Cancelled Appointment", "notes_key_2"=>"Cancelled"), "receipt"=>"Cancelled Appointment"));
                   // echo "<pre>";print_r($response);die;
                    $paymentData->status = 1;
                    $paymentData->refund_date = date('Y-m-d H:i:s');
                    $paymentData->is_refund = 1;
                    $paymentData->save();
                    
                    // $response = $api->payment->fetch($payment_id)->capture(array('amount'=>$payment['amount'])); 
                  return true;
            }else{
            	$paymentData->update(['status' => 1,'is_refund'=>1,'refund_date'=>date('Y-m-d H:i:s')]);
		return true;
            }
        }else{
		$paymentData->update(['status' => 1,'is_refund'=>1,'refund_date'=>date('Y-m-d H:i:s')]);
		return true;
        }
    }
    
    public function refundDetails($id){
    	$paymentData = Payment::find($id);
    	if($paymentData->payment_mode == 'online'){
    	     $paymentId = $paymentData->payment_id;
			//echo $paymentId;die;
            //$api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $api = new Api('rzp_live_lRU6KX64RykLIM', 'KUgLXdRMXErrJi0TYXxvHIbR');
            //Fetch payment information by razorpay_payment_id
            $payment = $api->payment->fetch($paymentId);
            return $this->sendResponse($payment,"Payment Details");
    	} else{
    	    return $this->sendError('Payment Details not available');
    	}
    }
    
    public function paymentDetails(){
    	$plan = Plan::orderBy('id','DESC')->first();
    	if(!empty($plan)){
	    	$data = ["consultation_fees" => $plan->consultation_fees , "booking_fees" => $plan->booking_fees , 'GST'=>$plan->gst , "total" => $plan->total_amount_after_gst];
	}else{
		$data = ["consultation_fees" => 600 , "booking_fees" =>50 , 'GST'=>0 , "total" => 650];
	}
    	
    	return $this->sendResponse($data,"Payment details.");
    }

    public function payWithRazorpay()
    {        
        return view('rozorpay');
    }
}

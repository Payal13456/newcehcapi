<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocode extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "promocodes";
    protected $fillable=[
        'name',
        'discount_by',
        'discount_amount',
        'send_to',
        'phone_number',
        'status'
    ];

    protected $dates = [ 'deleted_at' ];
    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                	'name'=> 'required',
			        'discount_by'=> 'required',
			        'discount_amount'=> 'required',
			        'send_to'=> 'required' 
                ];
                break;
            case 'update':
                $params = [
                        'name'=> 'required',
				        'discount_by'=> 'required',
				        'discount_amount'=> 'required',
				        'send_to'=> 'required' 
                    ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $faq){
        
        $preData['name'] = array_key_exists('name', $data) ? $data['name'] : $faq['name'];
        $preData['discount_by'] = array_key_exists('discount_by', $data) ? $data['discount_by'] : $faq['discount_by'];
        $preData['discount_amount'] = array_key_exists('discount_amount', $data) ? $data['discount_amount'] : $faq['discount_amount'];
        $preData['send_to'] = array_key_exists('send_to', $data) ? $data['send_to'] : $faq['send_to'];
        $preData['phone_number'] = array_key_exists('phone_number', $data) ? $data['phone_number'] : $faq['phone_number'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] :  $faq['status'];
       
        return $preData;
    }

     public function prepareCreateData(array $data)
     {
        $preData['name'] =  $data['name'] ;
        $preData['discount_by'] =  $data['discount_by'] ;
        $preData['discount_amount'] =  $data['discount_amount'] ;
        $preData['send_to'] =  $data['send_to'] ;
        $preData['phone_number'] =  $data['phone_number'] ;
        return $preData;
    }

}

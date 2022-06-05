<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Plan extends Model
{
    use HasFactory,SoftDeletes;


    protected $table="plans";

    protected $fillable=[
        'name',
        'consultation_fees',
        'gst',
        'total_amount_after_gst',
        'number_of_consultation',
        'booking_fees',
        'ip_address',
        'status'
    ];
    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params =
                [
                    'name'=> 'required',
                    'consultation_fees'=> 'required',
                    'gst'=> 'required',
                    'total_amount_after_gst'=> 'required',
                    'number_of_consultation'=> 'required'
                ];
                break;
            case 'update':
                $params = [
                    'name'=> 'required',
                    'consultation_fees'=> 'required',
                    'gst'=> 'required',
                    'total_amount_after_gst'=> 'required',
                    'number_of_consultation'=> 'required'
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $blog)
    {
        $preData = [];

        $preData['name'] = array_key_exists('name', $data) ? $data['name'] : $blog['name'];
        $preData['consultation_fees'] = array_key_exists('consultation_fees', $data) ? $data['consultation_fees'] : $blog['consultation_fees'];
        $preData['booking_fees'] = array_key_exists('booking_fees', $data) ? $data['booking_fees'] : $blog['booking_fees'];        
        $preData['gst'] = array_key_exists('gst', $data) ? $data['gst'] : $blog['gst'];
        $preData['total_amount_after_gst'] = array_key_exists('total_amount_after_gst', $data) ? $data['total_amount_after_gst'] : $blog['total_amount_after_gst'];
        $preData['number_of_consultation'] = array_key_exists('number_of_consultation', $data) ? $data['number_of_consultation'] : $blog['number_of_consultation'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] : $blog['status'];

        return $preData;
    }

    public function prepareCreateData(array $data)
    {
        $preData=[];
        $preData['name']=$data['name'];
        $preData['consultation_fees'] = $data['consultation_fees'];
        $preData['booking_fees'] = $data['booking_fees'];
        $preData['gst'] = $data['gst'];
        $preData['total_amount_after_gst'] = $data['total_amount_after_gst'];
        $preData['number_of_consultation'] = $data['number_of_consultation'];

        return $preData;
    }

}

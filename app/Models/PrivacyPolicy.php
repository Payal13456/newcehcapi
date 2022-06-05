<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivacyPolicy extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "privacy_policies";
    protected $fillable=[
        'title',
        'description',
        'ip_address',
        'status'
    ];

     protected $dates = [ 'deleted_at' ];
     public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                        'title'=> 'required',
                        'description'=> 'required',     
                ];
                break;
            case 'update':
                $params = [
                        'title'=> 'required',
                        'description'=> 'required',
                    ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $faq){
        
        $preData['title'] = array_key_exists('title', $data) ? $data['title'] : $faq['title'];
         $preData['description'] = array_key_exists('description', $data) ? $data['description'] : $faq['description'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] :  $faq['status'];
       
        return $preData;
    }

     public function prepareCreateData(array $data)
     {
        $preData['title'] =  $data['title'] ;
        $preData['description'] =  $data['description'] ;
        
        return $preData;
    }

}

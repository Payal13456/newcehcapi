<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = "notifications";
    protected $fillable=[
        'title',
        'description',
        'notification_type',
        'status'
    ];

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                        'title'=> 'required',
                        'description'=> 'required',    
                        'notification_type' => 'required' 
                ];
                break;
            case 'update':
                $params = [
                        'title'=> 'required',
                        'description'=> 'required',
                        'notification_type'=>'required'
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
        $preData['notification_type'] = array_key_exists('notification_type', $data) ? $data['notification_type'] : $faq['notification_type'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] :  $faq['status'];
       
        return $preData;
    }

     public function prepareCreateData(array $data)
     {
        $preData['title'] =  $data['title'] ;
        $preData['description'] =  $data['description'] ;
        $preData['notification_type'] =  $data['notification_type'];
        return $preData;
    }
}

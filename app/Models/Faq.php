<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BlogCategory;
class Faq extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "faq";
    protected $fillable=[
        'title',
        'description',
        'ip_address',
        'status',
        'cat_id'
    ];

     protected $dates = [ 'deleted_at' ];
     public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                        'title'=> 'required',
                        'description'=> 'required',
                        'cat_id'=>'required'    
                ];
                break;
            case 'update':
                $params = [
                        'title'=> 'required',
                        'description'=> 'required',
                        'cat_id'=>'required'
                    ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $faq){
        
         $preData['cat_id'] = array_key_exists('cat_id', $data) ? $data['cat_id'] : $faq['cat_id'];
        $preData['title'] = array_key_exists('title', $data) ? $data['title'] : $faq['title'];
         $preData['description'] = array_key_exists('description', $data) ? $data['description'] : $faq['description'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] :  $faq['status'];
       
        return $preData;
    }

     public function prepareCreateData(array $data)
     {
        $preData['cat_id'] =  $data['cat_id'] ;
        $preData['title'] =  $data['title'] ;
        $preData['description'] =  $data['description'] ;
        
        return $preData;
    }
public function category(){
     return $this->hasOne(BlogCategory::class,'id','cat_id');
    }

}

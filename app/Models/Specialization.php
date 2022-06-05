<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserInfomation;

class Specialization extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ="specialization";
    protected $fillable=['specialization','status','ip_address'];
    protected $dates = [ 'deleted_at' ];
     public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                        'specialization'=> 'required',     
                ];
                break;
            case 'update':
                $params = [
                        'specialization'=> 'required',
                    ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $specilization){
        
        $preData['specialization'] = array_key_exists('specialization', $data) ? $data['specialization'] : $specilization['specialization'];       
        return $preData;
    }

     public function prepareCreateData(array $data)
     {
        $preData['specialization'] =  $data['specialization'] ;
        $preData['ip_address'] =  $data['ip_address'] ;
        
        return $preData;
    }

    public function user(){
        return $this->hasMany(UserInfomation::class,'specialization_id','id');
    }
}

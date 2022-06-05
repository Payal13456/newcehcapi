<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserInfomation;
use App\Models\State;

class Hospital extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='hospitals';
    protected $fillable=['user_id','name','email','address','primary_number','secondary_number','location','ip_address','status','is_block','city','state_id'];

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params =
                [

                    'user_id'=> 'required',
                    'name'=> 'required',
                    'email'=>"required|email|unique:hospitals,email,null,id",
                    'address'=> 'required',
                    'primary_number'=> 'required',
                    'city'=>'required',
                    'state_id'=>'required'

                ];
                break;
            case 'update':
                $params = [
                    'name'=> 'required',
                    'address'=> 'required',
                    'email'=>"required|email|unique:hospitals,email,".$id."id",
                    'primary_number'=> 'required',
                    'city'=>'required',
                    'state_id'=>'required'
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
        $preData['user_id'] = array_key_exists('user_id', $data) ? $data['user_id'] : $blog['user_id'];
        $preData['address'] = array_key_exists('address', $data) ? $data['address'] : $blog['address'];
        $preData['primary_number'] = array_key_exists('primary_number', $data) ? $data['primary_number'] : $blog['primary_number'];
        $preData['email'] = array_key_exists('email', $data) ? $data['email'] : $blog['email'];
        $preData['secondary_number'] = array_key_exists('secondary_number', $data) ? $data['secondary_number'] : $blog['secondary_number'];
        $preData['location'] = array_key_exists('location', $data) ? $data['location'] : $blog['location'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] : $blog['status'];
        $preData['state_id'] = array_key_exists('state_id', $data) ? $data['state_id'] : $blog['state_id'];
        $preData['city'] = array_key_exists('city', $data) ? $data['city'] : $blog['city'];

        return $preData;
    }

    public function prepareCreateData(array $data)
    {
        $preData=[];
        $preData['user_id']=$data['user_id'];
        $preData['name'] = $data['name'];
        $preData['email']=$data['email'];
        $preData['address'] = $data['address'];
        $preData['primary_number'] = $data['primary_number'];
        $preData['secondary_number'] = $data['secondary_number'];
        $preData['location'] = $data['location'];
         $preData['state_id'] = $data['state_id'];
          $preData['city'] = $data['city'];

        return $preData;
    }

    public function user(){
        return $this->belongsTo(UserInfomation::class)->select('id','first_name','last_name');
    }

     public function state(){
        return $this->hasOne(State::class,'id','state_id');
    }
}

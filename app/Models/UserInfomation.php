<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\Specialization;
use App\Models\Schedule;
use App\Models\Hospital;
use App\Models\State;

class UserInfomation extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ="users_infomation";
    protected $appends = array('gender_specification');
    protected $fillable=[
        'user_id',
        'specialization_id',
        'first_name',
        'last_name',
        'month_dob',
        'phonenumber',
        'aadharnumber',
        'gender',
        'picture',
        'education_qulaification',
        'description',
        'city',
        'state_id',
        'address',
        'ip_address',
        'status',
        'role_id',
        'is_block'
    ];
    protected $dates = [ 'deleted_at' ];

    public function getGenderSpecificationAttribute()
    {
        $gender = "male':0,female:1,others:2";
        return $gender;  
    }

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params =
                [
                    'specialization_id'=> '',
                    'first_name'=> 'required',
                    'last_name'=> 'required',
                    'month_dob'=> 'required',
                    'phonenumber'=> 'required',
                    'aadharnumber'=> 'required',
                    'gender'=> 'required',
                    'education_qulaification'=> 'required',
                    'description'=> 'required',
                    'address'=> 'required',
                    "role_id"=>"required",
                    "state_id"=>""
                ];
                break;
            case 'register':
                $params =
                [
                    'specialization_id'=> '',
                    'first_name'=> 'required',
                    'last_name'=> 'required',
                    'month_dob'=> 'required',
                    'phonenumber'=> 'required',
                    'aadharnumber'=> 'required',
                    'gender'=> 'required',
                    'education_qulaification'=> '',
                    'description'=> '',
                    'address'=> 'required',
                    "role_id"=>"required",
                    "state_id"=>""
                ];
                break;
            case 'update':
                $params = [
                    'specialization_id'=> '',
                    'first_name'=> 'required',
                    'last_name'=> 'required',
                    'month_dob'=> 'required',
                    'phonenumber'=> 'required',
                    'aadharnumber'=> 'required',
                    'gender'=> 'required',
                    'education_qulaification'=> 'required',
                    'description'=> 'required',
                    'address'=> 'required',
                    "role_id"=>"required"
                ];
                break;
            case 'profile_update':
                 $params = [
                    'first_name'=> 'required',
                    'last_name'=> 'required',
                    'month_dob'=> 'required|before:today',
                    'phonenumber'=> 'required',
                    'gender'=> 'required',
                    'education_qulaification'=> 'required',
                    'description'=> 'required',
                    'address'=> 'required',
                ];
            break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $userInfo)
    {
        $preData['specialization_id'] = array_key_exists('specialization_id', $data) ? $data['specialization_id'] : $userInfo['specialization_id'];
        $preData['first_name'] = array_key_exists('first_name', $data) ? $data['first_name'] : $userInfo['first_name'];
        $preData['last_name'] = array_key_exists('last_name', $data) ? $data['last_name'] : $userInfo['last_name'];
        $preData['month_dob'] = array_key_exists('month_dob', $data) ? $data['month_dob'] : $userInfo['month_dob'];
        $preData['phonenumber'] = array_key_exists('phonenumber', $data) ? $data['phonenumber'] : $userInfo['phonenumber'];
        $preData['aadharnumber'] = array_key_exists('aadharnumber', $data) ? $data['aadharnumber'] : $userInfo['aadharnumber'];
        $preData['gender'] = array_key_exists('gender', $data) ? $data['gender'] : $userInfo['gender'];
        $preData['education_qulaification'] = array_key_exists('education_qulaification', $data) ? $data['education_qulaification'] : $userInfo['education_qulaification'];
        $preData['description'] = array_key_exists('description', $data) ? $data['description'] : $userInfo['description'];
        $preData['address'] = array_key_exists('address', $data) ? $data['address'] : $userInfo['address'];
        $preData['picture'] = array_key_exists('picture', $data) ? $data['picture'] : $userInfo['picture'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] : $userInfo['status'];
         $preData['state_id'] = array_key_exists('state_id', $data) ? $data['state_id'] : $userInfo['state_id'];
          $preData['city'] = array_key_exists('city', $data) ? $data['city'] : $userInfo['city'];


            return $preData;
    }

    public function prepareCreateData(array $data)
     {
        $preData['specialization_id'] = $data['specialization_id'];
        $preData['first_name'] =  $data['first_name'];
        $preData['last_name'] =  $data['last_name'];
        $preData['month_dob'] = $data['month_dob'];
        $preData['phonenumber'] =  $data['phonenumber'];
        $preData['aadharnumber'] =$data['aadharnumber'];
        $preData['gender'] =  $data['gender'] ;
        $preData['education_qulaification'] = $data['education_qulaification'] ;
        $preData['description'] = $data['description'];
        $preData['address'] = $data['address'];
        $preData['role_id'] = $data['role_id'];
        $preData['state_id'] = $data['state_id'];
        $preData['city'] = $data['city'];
        
        $preData['picture'] =  array_key_exists('picture', $data) ? $data['picture'] : null;

        return $preData;
    }

    public function prepareRegisterData(array $data)
     {
        $preData['specialization_id'] = $data['specialization_id'];
        $preData['first_name'] =  $data['first_name'];
        $preData['last_name'] =  $data['last_name'];
        $preData['month_dob'] = $data['month_dob'];
        $preData['phonenumber'] =  $data['phonenumber'];
        $preData['aadharnumber'] =$data['aadharnumber'];
        $preData['gender'] =  $data['gender'] ;
        $preData['education_qulaification'] = $data['education_qulaification'] ;
        $preData['description'] = $data['description'];
        $preData['address'] = $data['address'];
        $preData['role_id'] = $data['role_id'];
        $preData['state_id'] = array_key_exists('state_id', $data) ? $data['state_id'] : null;
        $preData['city'] = array_key_exists('city', $data) ? $data['city']:'';
        
        $preData['picture'] =  array_key_exists('picture', $data) ? $data['picture'] : null;

        return $preData;
    }

    public function prepareProfileUpdateData($data,$userInfo){
        $preData = [];
        $preData['first_name']=array_key_exists('first_name', $data)?$data['first_name'] : $userInfo['first_name'];
        $preData['last_name']=array_key_exists('last_name', $data)?$data['last_name'] : $userInfo['last_name'];
        $preData['month_dob']=array_key_exists('month_dob', $data)?$data['month_dob'] : $userInfo['month_dob'];
        $preData['phonenumber']=array_key_exists('phonenumber', $data)?$data['phonenumber'] : $userInfo['phonenumber'];
        $preData['aadharnumber']=array_key_exists('aadharnumber', $data)?$data['aadharnumber'] : $userInfo['aadharnumber'];
        $preData['gender']=array_key_exists('gender', $data)?$data['gender'] : $userInfo['gender'];
        $preData['education_qulaification']=array_key_exists('education_qulaification', $data)?$data['education_qulaification'] : $userInfo['education_qulaification'];
        $preData['description']=array_key_exists('description', $data)?$data['description'] : $userInfo['description'];
        $preData['address']=array_key_exists('address', $data)?$data['address'] : $userInfo['address'];
        $preData['specialization_id']=array_key_exists('specialization_id', $data)?$data['specialization_id'] : $userInfo['specialization_id'];
        $preData['picture']=array_key_exists('picture', $data)?$data['picture'] : $userInfo['picture'];
        $preData['state_id']=array_key_exists('state_id', $data)?$data['state_id'] : $userInfo['state_id'];
        $preData['city']=array_key_exists('city', $data)?$data['city'] : $userInfo['city'];
        return $preData;

      }

    // public function uploadProfile($image){
    //    // $image is your base64 encoded
    //     // echo $image;die;

    //     $imageType = substr($image,11,strpos($image,';')-11);
    //     $image = str_replace('data:image/'.$imageType.';base64,', '', $image);
    //     $image = str_replace(' ', '+', $image);
    //     $imageName = Str::random(10).'.'.$imageType;
    //     \File::put(public_path(). '/profile/' . $imageName, base64_decode($image));
    //     return "profile/".$imageName;
    // }

    public function createImage($img)
    {
	$folderPath = "profile/";
        if(str_contains($img,"data:image/")){
            $image_parts = explode(";base64,", $img);
            // echo "<pre>";print_r($image_parts);die;
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.'.$image_type;
            $file = $folderPath . $imageName;
            file_put_contents($file, $image_base64);
            return "profile/".$imageName;
        }else{
            $imgg = "data:image/png;base64,".$img;
            $image_parts = explode(";base64,", $imgg);
            // echo "<pre>";print_r($image_parts);die;
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.'.$image_type;
            $file = $folderPath . $imageName;
            file_put_contents($file, $image_base64);
            return "profile/".$imageName;
        }
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function roles(){
        return $this->belongsTo(Role::class,'role_id','id');
    }

    public function specilization(){
        return $this->belongsTo(Specialization::class,'specialization_id','id');
    }

    public function hospital(){
        return $this->hasOne(Hospital::class,'user_id','id');
    }
    public function state(){
        return $this->hasOne(State::class,'id','state_id');
    }

    public function schedule(){
        return $this->belongsTo(Schedule::class,'user_id','id');
    }
}

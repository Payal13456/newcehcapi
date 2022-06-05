<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use File;
use Str;
use App\Models\State;
use App\Models\User;
class Patient extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "patient";
    protected $fillable=[
        'parent_id',
        'first_name',
        'last_name',
        'email_address',
        'password',
        'phone_number_primary',
        'phone_number_secondary',
        'date_of_birth',
        'blood_group',
        'gender',
        'address',
        'city',
        'pincode',
        'adhar_card',
        'type_of_patient',
        'upload_of_picture',
        'ip_address',
        'status',
        'state_id',
        'user_id',
        'uhid'
    ];

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params =[
                    'parent_id'=>"",
                    'first_name'=>"required",
                    'last_name'=>"required",
                    'email_address'=>"required",
                    'phone_number_primary'=>"required",
                    'date_of_birth'=>"required",
                    'blood_group'=>"required",
                    'gender'=>"required",
                    'address'=>"required",
                    'city'=>"required",
                    'pincode'=>"required",
                    'adhar_card'=>"required",
                    'type_of_patient'=>"required",
                    'state_id'=>'required',
                ];
                break;
            case 'register':
                $params =
                [
                    'first_name'=> 'required',
                    'last_name'=> 'required',
                    'month_dob'=> 'required',
                    'aadharnumber'=> 'required',
                    'gender'=> 'required',
                    'address'=> 'required',
                    'email' => 'required'
                ];
                break;
            case 'profile':
                $params =
                [
                    'first_name'=> 'required',
                    'last_name'=> 'required',
                    'month_dob'=> 'required',
                    'aadharnumber'=> 'required',
                    'gender'=> 'required',
                    'address'=> 'required',
                    'email' => 'required',
                    'blood_group'=>"",
                ];
                break;
            case 'update':
                $params = [
                    'first_name'=>"required",
                    'last_name'=>"required",
                    'email_address'=>"required",
                    'phone_number_primary'=>"required",
                    'date_of_birth'=>"required",
                    'blood_group'=>"required",
                    'gender'=>"required",
                    'address'=>"required",
                    'city'=>"required",
                    'pincode'=>"required",
                    'adhar_card'=>"required",
                    'type_of_patient'=>"required",
                    'state_id'=>'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $paitent)
    {
        $preData['first_name'] = array_key_exists('first_name', $data) ? $data['first_name'] : $paitent['first_name'];
        $preData['last_name'] = array_key_exists('last_name', $data) ? $data['last_name'] : $paitent['last_name'];
        $preData['email_address'] = array_key_exists('email_address', $data) ? $data['email_address'] : $paitent['email_address'];
        $preData['phone_number_primary'] = array_key_exists('phone_number_primary', $data) ? $data['phone_number_primary'] : $paitent['phone_number_primary'];
        $preData['phone_number_secondary'] = array_key_exists('phone_number_secondary', $data) ? $data['phone_number_secondary'] : $paitent['phone_number_secondary'];
        $preData['date_of_birth'] = array_key_exists('date_of_birth', $data) ? $data['date_of_birth'] : $paitent['date_of_birth'];
        $preData['blood_group'] = array_key_exists('blood_group', $data) ? $data['blood_group'] : $paitent['blood_group'];
        $preData['gender'] = array_key_exists('gender', $data) ? $data['gender'] : $paitent['gender'];
        $preData['city'] = array_key_exists('city', $data) ? $data['city'] : $paitent['city'];
        $preData['pincode'] = array_key_exists('pincode', $data) ? $data['pincode'] : $paitent['pincode'];
        $preData['adhar_card'] = array_key_exists('adhar_card', $data) ? $data['adhar_card'] : $paitent['adhar_card'];
        $preData['type_of_patient'] = array_key_exists('type_of_patient', $data) ? $data['type_of_patient'] : $paitent['type_of_patient'];
        $preData['address'] = array_key_exists('address', $data) ? $data['address'] : $paitent['address'];
        $preData['upload_of_picture'] = array_key_exists('upload_of_picture', $data) ? $data['upload_of_picture'] : $paitent['upload_of_picture'];

         $preData['state_id'] = array_key_exists('state_id', $data) ? $data['state_id'] : $paitent['state_id'];
        
        return $preData;
    }

    public function prepareCreateData(array $data)
    {
        $preData['parent_id']=array_key_exists('parent_id', $data) ? $data['parent_id'] : 0;
        $preData['first_name'] = $data['first_name'];
        $preData['last_name'] = $data['last_name'];
        $preData['email_address'] = $data['email_address'];
        $preData['password'] = $data['password'];
        $preData['phone_number_primary'] = $data['phone_number_primary'];
        $preData['phone_number_secondary'] =array_key_exists('phone_number_secondary', $data) ? $data['phone_number_secondary'] : '';
        $preData['date_of_birth'] = $data['date_of_birth'];
        $preData['blood_group'] = $data['blood_group'];
        $preData['gender'] = $data['gender'];
        $preData['address'] = $data['address'];
        $preData['adhar_card'] = $data['adhar_card'];
        $preData['city'] = $data['city'];
        $preData['pincode'] = $data['pincode'];
        $preData['type_of_patient'] = $data['type_of_patient'];
        $preData['state_id'] = $data['state_id'];
        $preData['uhid'] = array_key_exists('uhid', $data) ? $data['uhid'] : null;

        return $preData;
    }


    public function prepareRegisterData(array $data)
    {
        $preData['parent_id']=array_key_exists('parent_id', $data) ? $data['parent_id'] : '';
        $preData['first_name'] = $data['first_name'];
        $preData['last_name'] = $data['last_name'];
        $preData['email_address'] = $data['email'];
        $preData['password'] = 'test1234';
        $preData['uhid'] = array_key_exists('uhid', $data) ? $data['uhid'] : null;
        $preData['phone_number_primary'] = $data['phonenumber'];
        $preData['phone_number_secondary'] = $data['phonenumber'];
        $preData['date_of_birth'] = $data['month_dob'];
        $preData['blood_group'] = array_key_exists('blood_group',$data) ? $data['blood_group'] : '';
        $preData['gender'] = $data['gender'];
        $preData['address'] = $data['address'];
        $preData['adhar_card'] = $data['aadharnumber'];
        $preData['city'] = array_key_exists('city', $data) ? $data['city'] : "";
        $preData['pincode'] = array_key_exists('pincode', $data) ? $data['pincode'] : "";
        $preData['type_of_patient'] = array_key_exists('type_of_patient', $data) ? $data['type_of_patient'] : 1;
        $preData['upload_of_picture'] = array_key_exists('picture', $data) ? $data['picture'] : "";
        $preData['state_id'] = array_key_exists('state_id', $data) ? $data['state_id'] : "";

        return $preData;
    }

    public function prepareProfileData(array $data, array $paitent)
    {
        $preData['parent_id']=array_key_exists('parent_id', $data) ? $data['parent_id'] : $paitent['parent_id'];
        $preData['first_name'] = $data['first_name'];
        $preData['last_name'] = $data['last_name'];
        $preData['email_address'] = $data['email'];
        $preData['password'] = 'test1234';
        $preData['uhid'] = array_key_exists('uhid', $data) ? $data['uhid'] : $paitent['uhid'];
        $preData['phone_number_primary'] = $data['phonenumber'];
        $preData['phone_number_secondary'] = $data['phonenumber'];
        $preData['date_of_birth'] = $data['month_dob'];
        $preData['blood_group'] = array_key_exists('blood_group',$data) ? $data['blood_group'] : $paitent['blood_group'];
        $preData['gender'] = $data['gender'];
        $preData['address'] = $data['address'];
        $preData['adhar_card'] = $data['aadharnumber'];
        $preData['city'] = array_key_exists('city',$data) ? $data['city'] : $paitent['city'];
        $preData['pincode'] = array_key_exists('pincode',$data) ? $data['pincode'] : $paitent['pincode'];
        $preData['type_of_patient'] = $paitent['type_of_patient'];
        $preData['upload_of_picture'] = array_key_exists('picture', $data) ? $data['picture'] : $paitent['upload_of_picture'];
        $preData['state_id'] = array_key_exists('state_id', $data) ? $data['state_id'] : $paitent['state_id'];

        return $preData;
    }

    public function uploadImageFile($image){
        $imageType = substr($image,11,strpos($image,';')-11);
        $image = str_replace('data:image/'.$imageType.';base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = Str::random(10).'.'.$imageType;
        \File::put(public_path(). '/profile/' . $imageName, base64_decode($image));
        
        return "profile/".$imageName;
    }

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
    
    public function state(){
        return $this->hasOne(State::class,'id','state_id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}

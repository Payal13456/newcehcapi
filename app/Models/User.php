<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\UserInfomation;
use App\Models\Patient;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'phonenumber',
        'role_id',
        'password',
        'fcm_token',
        'device_type',
        'is_approved'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = 
                [
                    'email'=> 'required|unique:users',
                    'role_id'=>'required',
                    'phonenumber'=>'required|unique:users',
                    'password'=> 'required'
                ];
                break;
            case 'register':
                 $params = 
                [
                    'email'=> 'required|unique:users',
                    'role_id'=>'required',
                    'phonenumber'=>'required|unique:users',
                    'password'=> '',
                    'fcm_token' => '',
                    'device_type'=> ''
                ];
                break;
            case 'update':
                $params = [
                    'email'=> 'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareCreateData(array $data)
     {  
        $preData['name'] = $data['first_name'];
        $preData['role_id'] = $data['role_id'];
        $preData['phonenumber'] = $data['phonenumber'];
        $preData['email'] =  $data['email'];
        $preData['password'] =  Hash::make( $data['password']);
        $preData['is_approved'] = 1;
        return $preData;
    }

    public function prepareRegisterData(array $data)
     {  
        $preData['name'] = $data['first_name'];
        $preData['role_id'] = $data['role_id'];
        $preData['phonenumber'] = $data['phonenumber'];
        $preData['email'] =  $data['email'];
        $preData['password'] =  Hash::make($data['password']);
        $preData['is_approved'] = $data['is_approved'];
        return $preData;
    }

    public function prepareUpdateData($inputs,$user){
        $preData = [];
        // print_r($user);die();
        $preData['name'] = array_key_exists('name',$inputs) ?  $inputs['name']: $user['name'];
        $preData['email'] = array_key_exists('email',$inputs) ?  $inputs['email'] : $user['email'];
        return $preData;
    }

    public function userInfo(){
        return $this->hasOne(UserInfomation::class,'user_id');
    }

    public function patientinfo(){
        return $this->hasOne(Patient::class,'user_id');
    }
    
    public function role(){
    	 return $this->hasOne(Role::class,'id','role_id');
    }
}
    

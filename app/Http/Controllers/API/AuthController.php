<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use Validator;
use Exception;
// use GuzzleHttp\Client;
use Laravel\Passport\Client as OClient; 
use App\Http\Controllers\API\BaseController as BaseController;
use DB;
use Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Password; 
use Illuminate\Support\Str;
use App\Models\UserInfomation;
use App\Models\Patient;
use Session;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Log;


class AuthController extends BaseController
{
    public function login(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) { 
                return $this->sendError($validator->getMessageBag()->first(),[]);  
            }
            $credentials = request(['email', 'password']);
            $check = User::where('email','LIKE',$request->get('email'))->where('is_approved',1)->first();
            if(empty($check)){
                return $this->sendError('Waiting for admin approval.',[]);
            }


            if(\Schema::hasTable('users')){
                if(!Auth::attempt($credentials))
                    return $this->sendError('User credentials doesn\'t match.',[]);
                    Log::info(json_encode($request->all()));
                    if(!empty($request->get('fcm_token'))){
                        User::where("id",$check->id)->update(['fcm_token'=> $request->get('fcm_token')]);
                    }
                    $request->session()->put('key', 'value');
                    $user = User::find($check->id);
                    $user->roles;
                    $user->getAllPermissions();
                    $tokenResult = $user->createToken('Personal Access Token');
                    if(empty($tokenResult)){
                        return $this->sendError('Yor are Unauthorised!.', ['error'=>'Unauthorised'],401);
                    }
                    $token = $tokenResult->token;
                    if ($request->remember_me)
                        $token->expires_at = Carbon::now()->addWeeks(1);
                    $token->save();
                    $success['access_token'] = $tokenResult->accessToken;
                    $success['token_id'] = $tokenResult->token['id'];
                    $success['token_type'] = 'Bearer';
                    $success['expires_at'] = Carbon::parse(
                                                $tokenResult->token->expires_at
                                             )->toDateTimeString();
                    $success['user'] =  $user;
                    $success['restaurant_name'] = "";   
                    Log::info(json_encode($request->header(),JSON_PRETTY_PRINT));
                    return $this->sendResponse($success, 'User login successfully.');
            }else{
                return $this->sendError('Users doesn\'t exist.',[]);
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(),'',500);
        }       
    }
    public function loginWithNumber(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'mobile_no' => 'required|regex:/[0-9]{10}/|digits:10',
            ]);
		
            if ($validator->fails()) { 
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $role_id = 1;
            
            if(!empty($request->get('role_id'))){
            	$role_id = $request->get('role_id');
            }

            $user = User::where('phonenumber', $request->get('mobile_no'))->where('role_id',$role_id)->first();

           
            
            // if(empty($user)) {
            //     $user = User::create(['mobile_no'=>$request->get('mobile_no')]);
            // } 
            
            if(!empty($user)){
            Log::info(json_encode($request->all()));
                if($user->is_approved == 0){
                    return $this->sendError('Waiting for admin approval.',[]);
                }
                if(\Schema::hasTable('users')){
                    if(!Auth::loginUsingId($user->id))
                        return $this->sendError('User credentials doesn\'t match.',[]);
                        
                        if(!empty($request->get('fcm_token'))){
                            User::where("id",$user->id)->update(['fcm_token'=> $request->get('fcm_token')]);
                        }
                        $userinfo = "";
                        if($user->role_id == 1){
                            $userinfo = UserInfomation::select('users_infomation.*','specialization.specialization as specialization_name')->leftJoin('specialization','specialization.id','=','users_infomation.specialization_id')->where('user_id',$user->id)->first();
                        }elseif($user->role_id == 3){
                            $userinfo = Patient::where('user_id',$user->id)->first();
                        }

                        $request->session()->put('key', 'value');
                        $user = $request->user();
                        $user->userinfo = $userinfo;
                        // $user->getAllPermissions();
                        $tokenResult = $user->createToken('Personal Access Token');
                        if(empty($tokenResult)){
                            return $this->sendError('Yor are Unauthorised!.', ['error'=>'Unauthorised'],401);
                        }
                        $token = $tokenResult->token;
                        if ($request->remember_me)
                            $token->expires_at = Carbon::now()->addWeeks(1);
                        $token->save();
                        $success['access_token'] = $tokenResult->accessToken;
                        $success['token_id'] = $tokenResult->token['id'];
                        $success['token_type'] = 'Bearer';
                        $success['expires_at'] = Carbon::parse(
                                                    $tokenResult->token->expires_at
                                                 )->toDateTimeString();
                        $success['user'] =  $user;
                        Log::info(json_encode($request->header(),JSON_PRETTY_PRINT));
                        return $this->sendResponse($success, 'User login successfully.');
                }else{
		    
	                    return $this->sendError('Users doesn\'t exist.',[]);
                   
                }
            }else{
                    $checkuser = User::where('phonenumber', $request->get('mobile_no'))->where('role_id','!=',$role_id)->first();
		    if(!empty($checkuser)){
		    	return $this->sendError('This number is already registered for other role.',[]);
		    }else{
		            return $this->sendError('Users doesn\'t exist.',[]);
		    }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(),'',500);
        }       
    }
    public function logout(Request $request)
    { 
        try{
            if (Auth::user()) {
                $user = $request->user();
                $moduleName = 'Logout';
                $moduleActivity = 'User logged out - '.$user->email;
                $description ='User logged out'.$user->email;

                /*Add action in audit log*/
                // captureAuditLog($moduleName,$moduleActivity,$description,$request->all(),
                                // $user->id);
                $accessToken = Auth::user()->token();

                User::where("id",Auth::user()->id)->update(['fcm_token'=> NULL]);
                
                DB::table('oauth_access_tokens')
                    ->where('id', $accessToken->id)
                    ->update([
                        'revoked' => true
                    ]);
                $accessToken->revoke();
                return $this->sendResponse('','Successfully logged out!');
            }else {
                return $this->sendError(trans('User does not exist'),[],404); 
            }
        } catch (Exception $e) {
           return $this->sendError($e->getMessage(),'',500);
        } 
    }
    public function getTokenAndRefreshToken(OClient $oClient, $email, $password) { 
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        $response = $http->request('POST', env('APP_URL').'oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $email,
                'password' => $password,
                'scope' => '*',
            ],
        ]);
       $result = json_decode((string) $response->getBody(), true);
       return $result;
    }

    public function getAllRoles(){
        try{
            $roles = Role::all();
            if($roles->count() > 0){
                return $this->sendResponse($roles, 'List of all Roles.');
            }
            return $this->sendError('Roles is not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function getAllPermissions(){
        try{
            $Permission = Permission::all();
            if($Permission->count() > 0){
                return $this->sendResponse($Permission, 'List of all Permission.');
            }
            return $this->sendError('Permission is not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function getRolePermission($id){
        try{
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->get();
            if(count($rolePermissions) > 0){
                return $this->sendResponse($rolePermissions, 'List of all Role Permission.');
            }
            return $this->sendError('Role Permission is not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

public function forgot_password(Request $request){

     try{
            $inputs = $request->only('email');
            if(!empty($inputs['email'])){
                if($user = User::where('email',$inputs['email'])->exists()){
                    $user = User::with(
                        ['userInfo'=>
                            function($query){
                                $query->where('status',1);
                            }
                        ]
                    )
                    ->where('email',$inputs['email'])
                    ->first();
                   if($user){
                        $link = env('WEB_URL');
                        $otp = mt_rand(100000, 999999);
                        $fromEmail = env('MAIL_FROM_ADDRESS', 'pydeve6@gmail.com');
                        $fromName = env('MAIL_USERNAME', 'Reset Passsword');
                        $subject = "Reset Password";
                        $activationLink = $link . '/resetPassword' . '?resetLink=' . encrypt(
                        $inputs['email'] . '$$' . $user->id . '$$' .$otp );
                        Mail::send(
                            'mail',
                            [
                                "resetLink"=>$activationLink
                            ],
                            function ($message) use($fromEmail,$fromName) {
                                $message->to($user->email)->subject("This is Test Subject");
                                $message->from($fromEmail, $fromName);  
                            }
                        );
                         return $this->sendResponse([], 'Email send successfully.');
                    }
                    return $this->sendError('Email-id is Not valide');
                }else{
                     return $this->sendError('Email-id is Not valide');
                }
            }else{
                return $this->sendError('Email-id is empty');
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function reset_password(Request $request){
        try{
            $inputs = $request->only('resetLink','password');
            if(!empty($inputs['resetLink']) && !empty($inputs['password'])){
                 $activationLink = decrypt($inputs['resetLink']);
                 $data = explode("$$",$activationLink);
                 $email = $data[0];
                 $id = $data[1];
                if(User::where(['email'=>$email,'id'=>$id])->exists()){
                    $password =bcrypt($inputs['password']);
                    $check = User::where(['email'=>$email,'id'=>$id])->update(['password'=>$password]);
                    if($check){
                        return $this->sendResponse([], 'Passsword is change successfully.');
                    }else{
                         return $this->sendError('Passsword is not change');
                    }
                }else{
                    return $this->sendError('User is dose not exist');
                }
            }else{
                   return $this->sendError('Passsword is Not valide');
            }



        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }

    }
    public function change_password(Request $request){
        try{
            if(auth()->user()){
                $inputs = $request->only('oldPassword','newPassword');
                 $validator = Validator::make($inputs, [ 
                    'oldPassword' => 'required|string',
                    'newPassword' => 'required|string'
                ]);

                if ($validator->fails()) { 
                   return $this->sendError($validator->getMessageBag()->first(),[]);
                }

                $pass = bcrypt($inputs['oldPassword']);
                $newPass = bcrypt($inputs['newPassword']);
                $id = auth()->user()->id;
                $check = User::where(['id'=>$id])->first();
                $updatePass = User::where('id',$id)->update(['password'=>$newPass]);
                if($updatePass){
                    return $this->sendResponse([], 'Passsword change successfully.');
                }else{
                    return $this->sendError('Password dose not change');  
                }
            }else{
                return $this->sendError('Please login first');  
            }  
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function verifyAccessToken(Request $request)
    {
        $success['user_found']= 0;
        $token = $request->token;
        $getUserCount = DB::table('oauth_access_tokens')->select('*')->where('id',$token)->count();
        if($getUserCount > 0){
            $success['user_found']= 1;    
        }
        return $this->sendResponse($success, '');
    }

    public function register(Request $request){
        try{      
            $inputs= $request->input();    
            if($inputs['role_id'] == 1){     
                $userInfo = new UserInfomation();
                $inputs['is_approved'] = 0;
                $inputs['password'] = random_int(100000, 999999);
            }else{
                $inputs['password'] = random_int(100000, 999999);
                $inputs['is_approved'] = 1;
                $inputs['uhid'] = substr($inputs['first_name'],0,3).'_'.mt_rand(100000,999999);
                $userInfo = new Patient();
            }

            $requiredParams = $userInfo->requiredRequestParams('register');
            $validator = Validator::make($inputs, $requiredParams);
            if ($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $user = new user();
            $userParams = $user->requiredRequestParams('register');
            $validator = Validator::make($inputs, $userParams);
            if ($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }

            $images='';
            if(array_key_exists('profilePic',$inputs) && !empty($inputs['profilePic'])){
                $images = $userInfo->createImage($inputs['profilePic']);
            }else{
                if($inputs['gender'] == 1){
                    if($inputs['role_id'] == 1){
                        $images = 'profile/unsplash_pTrhfmj2jDA-2.png';
                    }else{
                        $images = 'profile/unsplash_8ig-SzHpqDw-2.png';
                    }
                }else{
                    if($inputs['role_id'] == 1){
                        $images = 'profile/unsplash_rm7rZYdl3rY-2.png';
                    }else{
                        $images = 'profile/unsplash_va0YmklFtPA-2.png';
                    }
                }
            }

            $createUserData = $user->prepareRegisterData($inputs);
            $createData = $userInfo->prepareRegisterData($inputs);

            $user = $user->create($createUserData); 
            $createData['picture']=$images;
            $createData['upload_of_picture']=$images;
            $createData['user_id'] = $user->id;
            $createData['ip_address']=request()->ip(); 
            $createData['type_of_patient'] = 2;
            // echo "<pre>";print_r($createData);die;
            $create = $userInfo->create($createData);
            // $role = Role::FindOrFail($request->role_id);
            // $user->assignRole([$role->id]);
            $userInfo = User::where("id",$user->id)->whereIn('role_id',[1,3,0])->first();
            // if(empty($user)) {
            //     $user = User::create(['mobile_no'=>$request->get('mobile_no')]);
            // } 
            
                $subject = "Welcome to CEHC Portal";
                $to = $inputs['email'];
                $userd = [];
                $data = ['user'=>$inputs];
                Mail::send('emails.welcome', $data, function($message) use ($to, $subject){
                    $message->from('cehc@gmail.com', "CEHC Portal");
                    $message->subject($subject);
                    $message->to($to);                
                });
	    if($inputs['role_id'] == 1){
                if($userInfo->is_approved == 0){
                    return $this->sendError('Registered Successfully , Waiting for admin approval.',[]);
                }
            }
            
            if(!empty($userInfo)){
               
                if(\Schema::hasTable('users')){
                    if(!Auth::loginUsingId($userInfo->id))
                        return $this->sendError('User credentials doesn\'t match.',[]);
                        $userinfo = "";
                        if($user->role_id == 1){
                            $userinfo = UserInfomation::where('user_id',$userInfo->id)->first();
                        }elseif($user->role_id == 3){
                            $userinfo = Patient::where('user_id',$userInfo->id)->first();
                        }

                        $request->session()->put('key', 'value');
                        $userInfo = $request->user();
                        $userInfo->userinfo = $userinfo;

                        // $user->getAllPermissions();
                        $tokenResult = $userInfo->createToken('Personal Access Token');
                        if(empty($tokenResult)){
                            return $this->sendError('Yor are Unauthorised!.', ['error'=>'Unauthorised'],401);
                        }
                        $token = $tokenResult->token;
                        if ($request->remember_me)
                            $token->expires_at = Carbon::now()->addWeeks(1);
                        $token->save();
                        $success['access_token'] = $tokenResult->accessToken;
                        $success['token_id'] = $tokenResult->token['id'];
                        $success['token_type'] = 'Bearer';
                        $success['expires_at'] = Carbon::parse(
                                                    $tokenResult->token->expires_at
                                                 )->toDateTimeString();
                        $success['user'] =  $userInfo;
                        Log::info(json_encode($request->header(),JSON_PRETTY_PRINT));
                        return $this->sendResponse($success, 'User login successfully.');
                }else{
                    return $this->sendError('Users doesn\'t exist.',[]);
                }
            }else{
                
                return $this->sendError('Users doesn\'t exist.',[]);
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

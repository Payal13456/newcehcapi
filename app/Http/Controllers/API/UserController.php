<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Exception;
use Validator;
use App\Models\Specialization;
use App\Models\UserInfomation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\States;
use App\Models\Complain;
use App\Models\Patient;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRolewiseAllUser(Request $request){
        try{
            $roles = [];
            $role_name = !empty($request->role_name) ? $request->role_name : ""; 
            $userInfo = UserInfomation::with(['user','roles','specilization','state']);
            if(!empty($role_name)){
                 $roles = Role::where('name',$role_name)->first();
                if($roles->count()>0){
                    $userInfo = $userInfo->where('role_id',$roles->id);
                }
            }                   

            $userInfo=$userInfo->get();
            $data = [];
            if($userInfo->count() > 0){
              foreach($userInfo as $useri){
                if($useri->user->is_approved == 1){
                  $data [] = $useri;
                }
              }
              if(count($data) > 0){
              // echo "<pre>";print_r($userInfo);die;
                return $this->sendResponse($data, 'List of all Doctors.');
              }else{
                return $this->sendError('Doctors is not found');    
              }
            }
            return $this->sendError('Doctors is not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{      
            $inputs= $request->input();         
            $userInfo = new UserInfomation();
            $requiredParams = $userInfo->requiredRequestParams('create');
            $validator = Validator::make($inputs, $requiredParams);
            if ($validator->fails()) {
                return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $user = new user();
            $userParams = $user->requiredRequestParams('create');
             $validator = Validator::make($inputs, $userParams);
            if ($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $images='';
            if(array_key_exists('profilePic',$inputs) && !empty($inputs['profilePic'])){
                $images = $userInfo->createImage($inputs['profilePic']);
            }

            $createUserData = $user->prepareCreateData($inputs);
            $user = $user->create($createUserData); 
            $createData = $userInfo->prepareCreateData($inputs);
            $createData['picture']=$images;
            $createData['user_id'] = $user->id;
            $createData['ip_address']=request()->ip(); 
            // echo "<pre>";print_r($createData);die;
            $create = $userInfo->create($createData);
            // $role = Role::FindOrFail($request->role_id);
            // $user->assignRole([$role->id]);
            return $this->sendResponse([], 'User is created successfull.');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{  
            if(!empty($id)){
                if(UserInfomation::where('id',$id)->exists()){
                    $userInfo = UserInfomation::with('user','state','user.role')->where('id', $id)->first();
                    if(!empty($userInfo)){
                     return $this->sendResponse($userInfo, 'User is found.');    
                    }
                }else{
                    return $this->sendError("User is not found.");
                }
            }else{
                return $this->sendError("User-id is empty.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }     
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(UserInfomation::where('id',$id)->exists()){
                    $userInfo = UserInfomation::where('id',$id)->first();
                    $userInfoPrepare = $userInfo->requiredRequestParams('update',$id);
                    $validator = Validator::make($inputs, $userInfoPrepare);
                    if ($validator->fails()) {
                       return $this->sendError($validator->getMessageBag()->first(),[]);
                    }
                    $prepareUpdateForUserInfo = $userInfo->prepareUpdateData($inputs,$userInfo->toArray());

                    if(array_key_exists('profilePic',$inputs) && !empty($inputs['profilePic'])){
                        $images = $userInfo->createImage($inputs['profilePic']);
                        $prepareUpdateForUserInfo['picture']=$images;
                    }
                    //  if(array_key_exists('profile_pic',$inputs) && !empty($inputs['profile_pic'])){
                    //     $images = $userInfo->uploadProfile($inputs['profile_pic']);
                    // }
                    $userInfoUpdate = $userInfo->update($prepareUpdateForUserInfo);
                    if($userInfoUpdate){
                        return $this->sendResponse([], 'User is updated successfull.'); 
                    }else{
                         return $this->sendError("User is not updated.");
                    }
                }else{
                  return $this->sendError("User is not found.");   
                }
            }else{
                return $this->sendError("User-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         try{
            if(!empty($id)){
                if(UserInfomation::where('id', $id)->exists()){
                    $userInfo = UserInfomation::where('id', $id)->forceDelete();
                    return $this->sendResponse([],'User is deleted successfull.');
                }else{
                     return $this->sendError("User is not found.");
                }
            }else{
                return $this->sendError("User-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
 
    public function profileUpdate(Request $request,$id){
        // try{
            if(!empty($id)){
                $inputs = $request->input();
                $user_id = $id;
                // echo $user_id;die;
                if(UserInfomation::where('user_id',$user_id)->exists()){
                    $userInfo = UserInfomation::where('user_id',$user_id)->first();  
                    if(array_key_exists('image',$inputs)){
                        $img = $userInfo->createImage($inputs['image']);
                        $inputs['picture'] = $img;
                    } 
                    $rulesParams = $userInfo->requiredRequestParams('profile_update');
                    // $validator = Validator::make($inputs, $rulesParams);
                    // if ($validator->fails()) {
                    //     return $this->sendError('Validation Error.', $validator->errors());
                    // }
                    $profileData = $userInfo->prepareProfileUpdateData($inputs,$userInfo->toArray()); 

                    $update = $userInfo->update($profileData);
                    if($update){
                        $userInfo->refresh();
                        $data = [];
                        $userdata = User::where('id',$id)->first();
                        $userinfo = UserInfomation::where('user_id',$userdata->id)->first();
                        $userdata->userinfo = $userinfo;
                        $data['user'] = $userdata;
                        return $this->sendResponse($data,"Profile is updated successfull");
                    }else{
                         return $this->sendError("Profile is not updated");
                    }
                }else{
                    return $this->sendError("User is not found");
                }
            }else{
                 return $this->sendError("User-id is empty.."); 
            }
        // }catch(Exception $e){
        //      return $this->sendError($e->getMessage(),'',500);
        // }

    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function statusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(UserInfomation::where('id',$id)->exists()){
                    $userInfo = UserInfomation::where('id',$id)->first();
                    $userInfoUpdate = $userInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "User is active.";
                                break;
                            case '1':
                                $msg = "User is inactive.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($userInfoUpdate){
                        return $this->sendResponse($userInfo, $msg); 
                    }else{
                         return $this->sendError("User is not updated.");
                    }
                }else{
                  return $this->sendError("User is not found.");   
                }
            }else{
                return $this->sendError("User-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }

    /**
     * User block and unblock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userBlockUnblock(Request $request, $id){
        try{
            if(!empty($id)){
                $inputs = $request->input();
                if(UserInfomation::where('id',$id)->exists()){
                    $userInfo = UserInfomation::where('id',$id)->first();
                    $userInfoUpdate = $userInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['is_block']){
                            case '1':
                                $msg = "User is unblock.";
                                break;
                            case '2':
                                $msg = "User is block.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($userInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("User is not updated.");
                    }
                }else{
                  return $this->sendError("User is not found.");   
                }
            }else{
                return $this->sendError("User-id is empty.."); 
            }

        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
    public function getProfileData($id){
        try{  
            if(!empty($id)){
                if(User::where('id',$id)->exists()){
                    $user = User::where('id',$id)->first();
                    if($user->role_id == 3){
                        $userInfo = User::with('patientinfo')->where('id', $id)->first();
                    }else{
                        $userInfo = User::with('userInfo','userInfo.specilization')->where('id', $id)->first();
                    }  
                    if(!empty($userInfo)){
                     return $this->sendResponse($userInfo, 'User is found.');    
                    }
                }else{
                    return $this->sendError("User is not found.");
                }
            }else{
                return $this->sendError("User-id is empty.");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }     
    }

    public function updateToken(Request $request){
      try{
          $request->user()->update(['fcm_token'=>$request->token]);
          return $this->sendResponse($userInfo, 'User is found.');    
      }catch(\Exception $e){
          return $this->sendError($e->getMessage(),'',500);
      }
    }

    public function states(){
      try{
          $states = States::all();
          if($states->count() > 0){    
                  return $this->sendResponse($states, 'List of all States.');
          }
          return $this->sendError('States is not found');
        } catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function doctorsBySpecification($id){
        try{
          $userids = User::where('role_id',1)->where('is_approved',1)->pluck('id')->all();
          $states = UserInfomation::where('specialization_id',$id)->whereIn('user_id',$userids)->where('role_id',1)->get();
          if($states->count() > 0){    
                  return $this->sendResponse($states, 'List of all doctors.');
          }
          return $this->sendError('doctors not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function pendingList(){
       try{
          $userInfo = User::with('userInfo')->where('is_approved',0)->get();
          if(count($userInfo) > 0){
          // echo "<pre>";print_r($userInfo);die;
            return $this->sendResponse($userInfo, 'List of pending Doctors.');
          }else{
            return $this->sendError('Doctors is not found');    
          }
          return $this->sendError('Doctors is not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function changeStatus($id , $status){
        try{
          $userInfo = User::where('id',$id)->update(['is_approved'=>$status]);
          if($status == 1){
          // echo "<pre>";print_r($userInfo);die;
            return $this->sendResponse([], 'Doctor Approved!');
          }else{
            $userInfo = User::where('id',$id)->forceDelete();
            $userInfo = UserInfomation::where('user_id',$id)->forceDelete();
            return $this->sendResponse([],'Doctors Rejected!');    
          }
          return $this->sendError('Doctors is not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function sendHelp(Request $request){
        try{
            $inputs = $request->input();
            $user = User::find($inputs['user_id']);
            if(!empty($user)){
                $data = [
                    "user_id" => $inputs['user_id'],
                    "description" => $inputs['description'],
                    "subject" => $inputs['subject']
                ];
                Complain::insert($data);

                return $this->sendResponse([],'Sent Successfully!');    
            }
            return $this->sendError('User not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }
    
    public function address(){
    	try{
    		$data = [];
    		$data['address'] = "3rd Floor, Ramaniyam Isha,11, OMR";
    		$data['city'] = "Thoraipakkam";
    		$data['state'] = "Chennai";
    		$data['pincode'] = "600097";
    		$data['mobile_no']  = '044 24961414 , 9445261414 , 9176939047';
    		$data['email'] = 'cehcchennai@gmail.com';
    		$data['website'] = 'https://cehcchennai.com';
    		
    		return $this->sendResponse($data,'CEHC Address'); 
    	}catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }
    
    public function checkUserStatus(){
    	try{
	     $user = User::find(auth()->user()->id);
	     if(!empty($user)){
	     	if($user->role_id == 1){
	     		$check = UserInfomation::select('status')->where('user_id',$user->id)->first();
	     		if(!empty($check)){
	     			if($check->status == 1){
	     				return $this->sendResponse($check,'User Status');   
	     			}else{
	     				return $this->sendResponse($check,'User Status');   
	     			}
	     		}else{
	     			return $this->sendError("User not found");
	     		}
	     	}elseif($user->role_id == 3){
	     		$check = Patient::select('status')->where('user_id',$user->id)->first();
	     		if(!empty($check)){
	     			if($check->status == 1){
	     				return $this->sendResponse($check,'User Status');   
	     			}else{
	     				return $this->sendResponse($check,'User Status');   
	     			}
	     		}else{
	     			return $this->sendError("User not found");
	     		}
	     	}
	     }else{
	     	return $this->sendError("User not found");
	     }
    	}catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }
}

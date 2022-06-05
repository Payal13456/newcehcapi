<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use DB;
use Validator;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // function __construct()
    // {
    //      $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
    //      $this->middleware('permission:role-create', ['only' => ['create','store']]);
    //      $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
    //      $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    // }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('admin.roles.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $permission = Permission::get();
            if($permission->count() > 0){
                return $this->sendResponse($permission,"List of all permissions.");
            }
            return $this->sendError('permissions is not found');
        } catch(Exception $e){
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
            $inputs = $request->input();
            $validator = Validator::make($inputs, [
                'name' => 'required|unique:roles,name',
                // "permission"    => "required|array",
                "permissions.*"  => "required|string|distinct",
            ]);
            // return json_encode($request->all());
            if($validator->fails()){
              return $this->sendError($validator->getMessageBag()->first(),[]);
            }

            $role = Role::create(['name' => $request->input('name'),'guard_name'=> $request->input('guard_name')])->id;

            foreach($request->input('permissions') as $permission){
                DB::table('role_has_permissions')->insert(['permission_id'=>$permission,'role_id'=>$role]);
            }



            // $role->syncPermissions($request->input('permissions'));
            // echo "<pre>";print_r($request->permission);die; 
            if($role){
                return $this->sendResponse([],"Role is created successfully");
            }
            return $this->sendError("Role is not created"); 
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
        // $role = Role::find($id);
        // $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
        //     ->where("role_has_permissions.role_id",$id)
        //     ->get();
        // return view('admin.roles.create',compact('role','rolePermissions'));
        try{
            $role = Role::find($id);
            if(!empty($role)){
                return $this->sendResponse($role, 'Roles details.');
            }
            return $this->sendError('Role details is not found');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $role = Role::find($id);
            if(!empty($role)){
                return $this->sendResponse($role, 'Roles details.');
            }
            return $this->sendError('Role details is not found');
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
                if(Role::where('id',$id)->exists()){
                    $role = Role::where('id',$id)->first();
                    if(!empty($role)){
                        $inputs = $request->input();
                        $validator = Validator::make($inputs, [
                            'name' => 'required',
                            // "permission"    => "required|array",
                            "permissions.*"  => "required|string|distinct",
                        ]);
                        if($validator->fails()){
                            return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        
                        $role = Role::find($id);
                        $role->name = $request->input('name');
                        $role->guard_name = $request->input('guard_name');
                        $role->save();
                        
                        // $role->syncPermissions($request->input('permissions'));
                        $p_id = [];
                        foreach($request->input('permissions') as $permission){
                            $p_id [] = $permission;
                            $check = DB::table('role_has_permissions')->where('permission_id',$permission)->where('role_id',$id)->first();
                            if(empty($check)){
                                DB::table('role_has_permissions')->insert(['permission_id'=>$permission,'role_id'=>$id]);
                            }
                        }
                        if(count($p_id) > 0){
                            DB::table('role_has_permissions')->whereNotIn('permission_id',$p_id)->delete();
                        }
                        if($role){
                            return $this->sendResponse([],"Role details is updated successfull");
                        }
                         return $this->sendError('Role details is not updated'); 
                    }else{
                        return $this->sendError('Role details is not found');               
                    }
                }else{
                    return $this->sendError('Role-id is not found');               
                }
            }else{
                return $this->sendError('Role-id is empty');                
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),500);
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
                if(Role::where('id',$id)->exists()){
                    $Promocode = Role::where('id',$id)->first();
                    if(!empty($Promocode)){
                        $Promocode->delete();
                        return $this->sendResponse([],'Role remove successfully.');
                    }
                    return $this->sendError("Role is not found.");   
                }else{
                   return $this->sendError( 'Role  is not found.');  
               }
            }else{
              return $this->sendError("Role-id is empty.");   
            }
           
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        }  
    }
}

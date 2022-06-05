<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\BlogCategory;
use Validator;
use App\Models\Blog;
use App\Models\BlogFiles;

class BlogController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $blogs = Blog::with('category','files')->get();
            if($blogs->count() > 0){
                return $this->sendResponse($blogs,"List of all blogs.");
            }
             return $this->sendError('Blogs is not found');
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
            $inputs = $request->input();
            //echo "<pre>";print_r($inputs);die;
            $blog = new Blog();
            $rulesParams = $blog->requiredRequestParams('create');

            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
                return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $blog->prepareCreateData($inputs);
            $blogData = $blog->create($createData);
            
            if(array_key_exists('images',$inputs) && !empty($inputs['images'])){
                 foreach($inputs['images'] as $image){
                   $images = $blog->createImage($image);
                    BlogFiles::create(
                        [
                            "blog_id" => $blogData->id,
                            "type" => 1,
                            "file_path" => $images
                        ]
                    );
                }
            }else{
                $image = "blog/default_blog.jpeg";
                 BlogFiles::create(
                    [
                        "blog_id" => $blogData->id,
                        "type" => 1,
                        "file_path" => $image
                    ]
                );
            }
            if($blogData){
                return $this->sendResponse([],"Blog is created successfull");
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),500);
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
                if(Blog::where('id',$id)->exists()){
                    $blog = Blog::with('category')->where('id',$id)->first();
                    if(!empty($blog)){
                         return $this->sendResponse($blog, 'Blog is found.'); 
                    }else{
                        return $this->sendError('Blog is not found');               
                    }
                }else{
                    return $this->sendError('Blog-id is not found');               
                }
            }else{
                return $this->sendError('Blog-id is empty');                
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
                if(Blog::where('id',$id)->exists()){
                    $blog = Blog::with('category')->where('id',$id)->first();
                    if(!empty($blog)){

                         $rulesParams = $blog->requiredRequestParams('update',$id);
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                            return $this->sendError($validator->getMessageBag()->first(),[]);
                        }
                        $updateData = $blog->prepareUpdateData($inputs,$blog->toArray());
                        $isUpdated = $blog->update($updateData);
                        if($isUpdated){
                            if(array_key_exists('pdf',$inputs) && !empty($inputs['pdf'])){
                                $pdfs = $blog->uploadPdfFiles($inputs['pdf']);
                                foreach($pdfs as $pdf){
                                    BlogFiles::create(
                                        [
                                            "blog_id" => $blog->id,
                                            "type" => 2,
                                            "file_path" => $pdf
                                        ]
                                    );
                                }
                            }
                            if(array_key_exists('images',$inputs) && !empty($inputs['images'])){
                                $images = $blog->uploadImageFiles($inputs['images']);
                                foreach($images as $image){
                                    BlogFiles::create(
                                        [
                                            "blog_id" => $blog->id,
                                            "type" => 1,
                                            "file_path" => $image
                                        ]
                                    );
                                }
                            }
                            return $this->sendResponse([], 'Blog is updated successfull.'); 
                        } 
                        return $this->sendError('Blog is not updated');               
                    }else{
                        return $this->sendError('Blog is not found');               
                    }
                }else{
                    return $this->sendError('Blog-id is not found');               
                }
            }else{
                return $this->sendError('Blog-id is empty');                
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
                if(Blog::where('id',$id)->exists()){
                    $blog = Blog::where('id',$id)->first();
                    if(!empty($blog)){
                       $blog->delete();
                         return $this->sendResponse([], 'Blog is deleted successfull.'); 
                    }else{
                        return $this->sendError('Blog is not found');               
                    }
                }else{
                    return $this->sendError('Blog-id is not found');               
                }
            }else{
                return $this->sendError('Blog-id is empty');                
            }
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function listCategorys(){
        try{
            
            $category = BlogCategory::all();
            if($category->count() > 0){
                return $this->sendResponse($category,"List of all Category.");
            }
             return $this->sendError('Categorys is not found');
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function getCategorys($id){
        try{
            if(!empty($id)){
                if(BlogCategory::where('id',$id)->exists()){
                    $category = BlogCategory::where('id',$id)->first();
                    if(!empty($category)){
                         return $this->sendResponse($category, 'Category is found.'); 
                    }else{
                        return $this->sendError('Category is not found');               
                    }
                }else{
                    return $this->sendError('Categorys-id is not found');               
                }
            }else{
                return $this->sendError('Categorys-id is empty');                
            }
        }catch(Exception $e){
             return $this->sendError($e->getMessage(),'',500);
        }
    }

    public function createCategory(Request $request){
        try{
            $inputs = $request->input();
            $category = new BlogCategory();
            $rulesParams = $category->requiredRequestParams('create');
            $validator = Validator::make($inputs,$rulesParams);
            if($validator->fails()){
                 return $this->sendError($validator->getMessageBag()->first(),[]);
            }
            $createData = $category->prepareCreateData($inputs);
            $category = $category->create($createData);
            if($category){
                return $this->sendResponse([],"Category is created successfull");
            }
            return $this->sendError('Category is not created');  
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),500);
        }
    }

    public function updateCategory(Request $request,$id){
        try{
            if(!empty($id)){
                if(BlogCategory::where('id',$id)->exists()){
                    $category = BlogCategory::where('id',$id)->first();
                    if(!empty($category)){
                        $inputs = $request->input();
                        $rulesParams = $category->requiredRequestParams('update',$id);
                        $validator = Validator::make($inputs,$rulesParams);
                        if($validator->fails()){
                            return $this->sendError($validator->getMessageBag()->first(),[]);
                        }

                        $updateData= $category->prepareUpdateData($inputs,$category->toArray());
                        $isUpdated = $category->update($updateData);
                        if($isUpdated){
                            return $this->sendResponse([],"Category is updated successfull");
                        }
                         return $this->sendError('Category is not updated'); 
                    }else{
                        return $this->sendError('Category is not found');               
                    }
                }else{
                    return $this->sendError('Categorys-id is not found');               
                }
            }else{
                return $this->sendError('Categorys-id is empty');                
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),500);
        }
    }

    public function removeCategorys($id){
        try{
            if(!empty($id)){
                if(BlogCategory::where('id',$id)->exists()){
                    $category = BlogCategory::where('id',$id)->first();
                    if(!empty($category)){
                        $category->delete();
                        Blog::where('cat_id',$id)->delete();
                        return $this->sendResponse([], 'Category is deleted successfull.'); 
                    }else{
                        return $this->sendError('Category is not found');               
                    }
                }else{
                    return $this->sendError('Categorys-id is not found');               
                }
            }else{
                return $this->sendError('Categorys-id is empty');                
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
    public function blogStatusUpdate(Request $request, $id)
    {
        try{  
            if(!empty($id)){
                $inputs = $request->input();
                if(Blog::where('id',$id)->exists()){
                    $blogInfo = Blog::where('id',$id)->first();
                    $blogInfoUpdate = $blogInfo->update($inputs);
                       $msg = ""; 
                        switch($inputs['status']){
                            case '0':
                                $msg = "Blog is active.";
                                break;
                            case '1':
                                $msg = "Blog is inactive.";
                                break;    
                            default:
                                $msg = "";
                                break;
                        }
                    if($blogInfoUpdate){
                        return $this->sendResponse([], $msg); 
                    }else{
                         return $this->sendError("Blog is not updated.");
                    }
                }else{
                  return $this->sendError("Blog is not found.");   
                }
            }else{
                return $this->sendError("Blog-id is empty.."); 
            }
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),'',500);
        } 
    }
}

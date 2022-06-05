<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BlogCategory;
use App\Models\BlogFiles;
use File;
use Str;

class Blog extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="blogs";
    protected $fillable=['title','description','status','cat_id'];
    protected $dates = [ 'deleted_at' ];

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params =
                [
                    'title'=> 'required',
                    'description'=>'required',
                    'cat_id'=>"required"
                ];
                break;
            case 'update':
                $params = [
                    'title'=> "required|unique:blogs,title,".$id."id",
                    'description'=>'required'
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
        $preData['title'] = array_key_exists('title', $data) ? $data['title'] : $blog['title'];
        $preData['description'] = array_key_exists('description', $data) ? $data['description'] : $blog['description'];
        $preData['cat_id'] = array_key_exists('cat_id', $data) ? $data['cat_id'] : $blog['cat_id'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] : $blog['status'];

        return $preData;
    }

    public function prepareCreateData(array $data)
    {
        $preData['title'] = $data['title'];
        $preData['description'] = $data['description'];
        $preData['cat_id']=$data['cat_id'];
        return $preData;
    }

    public function uploadPdfFiles(array $pdfs){
       $pdfData = [];
       foreach($pdfs as $pdf){
            $pdf = str_replace('data:application/pdf;base64,', '', $pdf);
            $pdf = str_replace(' ', '+', $pdf);
            $pdfName = Str::random(10).'.pdf';
            \File::put(public_path(). '/profile/' . $pdfName, base64_decode($pdf));
            
            array_push($pdfData,"profile/".$pdfName);
       }
       return $pdfData;
    }

    public function createImage($img)
    {

        $folderPath = "blog/";
        if(str_contains($img,"data:image/")){
            $image_parts = explode(";base64,", $img);
            // echo "<pre>";print_r($image_parts);die;
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.'.$image_type;
            $file = $folderPath . $imageName;
            file_put_contents($file, $image_base64);
            return "blog/".$imageName;
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
            return "blog/".$imageName;
        }
    }


    public function uploadImageFiles(array $images){
        $imageData = [];
       foreach($images as $image){
            $imageType = substr($image,11,strpos($image,';')-11);
            $image = str_replace('data:image/'.$imageType.';base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10).'.'.$imageType;
            \File::put(public_path(). '/profile/' . $imageName, base64_decode($image));
            array_push($imageData,"profile/".$imageName);
       }
       return $imageData;
    }

    public function category(){
     return $this->hasOne(BlogCategory::class,'id','cat_id');
    }

    public function files(){
        return $this->hasMany(BlogFiles::class,'blog_id','id');
    }
}

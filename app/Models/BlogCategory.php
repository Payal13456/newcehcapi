<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Blog;
use App\Models\Faq;

class BlogCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ="blog_categories";
    protected $fillable=['category_name','status'];
    protected $dates = [ 'deleted_at' ];

    public function requiredRequestParams(string $action, $id = null){
        
        switch ($action) {
            case 'create':
                $params =
                [
                    'category_name'=> 'required|unique:blog_categories,category_name,null,id',
                ];
                break;
            case 'update':
                $params = [
                    'category_name'=> 'required|unique:blog_categories,category_name,'.$id.',id',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function prepareUpdateData(array $data, array $category)
    {
        $preData['category_name'] = array_key_exists('category_name', $data) ? $data['category_name'] : $category['category_name'];
        $preData['status'] = array_key_exists('status', $data) ? $data['status'] : $category['status'];

        return $preData;
    }

    public function prepareCreateData(array $data)
    {
        $preData['category_name'] = $data['category_name'];

        return $preData;
    }

    public function blogs(){
        return $this->hasMany(Blog::class,'cat_id','id');
    }
    public function faqs(){
        return $this->hasMany(Faq::class,'cat_id','id');
    }
}

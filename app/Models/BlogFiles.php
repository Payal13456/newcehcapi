<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Blog;

class BlogFiles extends Model
{
    use HasFactory;

    protected $table='blog_files';
    protected $fillable=['blog_id','type','file_path'];

    public function blog(){
        return $this->belongsTo(Blog::class);
    }
}

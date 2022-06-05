<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class States extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ="state";
    protected $fillable=['state_name','status','ip_address'];
    protected $dates = [ 'deleted_at' ];
}

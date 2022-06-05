<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class BaseController extends Controller
{
    use ApiResponser;
}

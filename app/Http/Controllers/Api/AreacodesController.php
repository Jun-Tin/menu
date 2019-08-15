<?php

namespace App\Http\Controllers\Api;

use App\Models\Areacode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreacodeResource;

class AreacodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new AreacodeResource(Areacode::where('show',1)->orderBy('order_number','desc')->get());
    }
}

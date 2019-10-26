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
    public function index(Areacode $areacode)
    {
    	$message = '{"type":"say_to_all","content":"hello"}';
        $req_data = json_decode($message, true);
    	dd(Gateway::sendToAll($req_data));
        return (new AreacodeResource($areacode::where('show',1)->orderBy('order_number','desc')->get()))->additional(['status' => 200]);
    }
}

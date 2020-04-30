<?php

namespace App\Http\Controllers\Api;

use App\Models\{Qrcode, Place};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\QrcodeResource;

class QrcodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Qrcode $qrcode)
    {
        $qrcode = Qrcode::where('code', $request->code)->first();
        if (!$qrcode) {
            return response()->json(['error' => ['message' => __('illegal')], 'status' => 404]);
        }

        return (new QrcodeResource($qrcode))->additional(['status' => 200]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Qrcode $qrcode)
    {
        $place = Place::find($request->id);
        if (!$place) {
            return response()->json(['error' => ['message' => __('illegal')], 'status' => 404]);
        }
        Qrcode::where('code', $request->code)->update(['link' => $place->image->link]);
        $qrcode = Qrcode::where('code', $request->code)->first();
        
        return (new QrcodeResource($qrcode))->additional(['status' => 200, 'message' => __('messages.update')]);
    }
}

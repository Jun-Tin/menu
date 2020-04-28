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
    public function index(Qrcode $qrcode)
    {
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
            return response()->json(['error' => ['message' => '座位不存在'], 'status' => 404]);
        }
        $qrcode->update(['link' => $place->image->link]);

        return (new QrcodeResource($qrcode))->additional(['status' => 200, 'message' => __('messages.update')]);
    }
}

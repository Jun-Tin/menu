<?php

namespace App\Http\Controllers\Api;

use App\Models\StorePayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StorePaymentResource;

class StorePaymentsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(StorePayment $storepayment)
    {
        return (new StorePaymentResource($storepayment))->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, StorePayment $storepayment)
    {
        $storepayment->fill($request->all());
        $storepayment->save();
        return (new StorePaymentResource($storepayment))->additional(['status' => 200, 'message' => __('messages.store')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StorePayment $storepayment)
    {
        $storepayment->update($request->all());
        return (new StorePaymentResource($storepayment))->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(StorePayment $storepayment)
    {
        $storepayment->delete();
        return response()->json(['status' => 200, 'message' => __('messages.destroy')]);
    }
}

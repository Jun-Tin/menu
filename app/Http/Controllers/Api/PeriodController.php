<?php

namespace App\Http\Controllers\Api;

use App\Models\Period;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PeriodResource;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Period $period)
    {
        return (PeriodResource::collection($period::where('show',1)->orderBy('order_number','desc')->get()))->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Period $period)
    {
        $period->fill($request->all());
        $period->save();

        return (new PeriodResource($period))->additional(['status' => 200, 'message' => __('messages.store')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Period $period)
    {
        $period->update($request->all());

        return (new PeriodResource($period))->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Period $period)
    {
        $period->delete();

        return response()->json(['message' => __('messages.destroy'), 'status' => 200]);
    }
}

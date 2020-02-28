<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;

class CurrenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $collection = Currency::where('show', 1)->get();
        return (CurrencyResource::collection($collection))->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Currency $currency)
    {
        $currency->fill($request->all());
        $currency->save();
        return (new CurrencyResource($currency))->additional(['status' => 200, 'message' => __('messages.store')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency)
    {
        $currency->update($request->all());
        return (new CurrencyResource($currency))->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();
        return response()->json(['status' => 200, 'message' => __('messages.destroy')]);
    }
}

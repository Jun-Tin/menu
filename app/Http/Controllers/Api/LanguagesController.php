<?php

namespace App\Http\Controllers\Api;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;

class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $collection = Language::where('show', 1)->get();
        return (LanguageResource::collection($collection))->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Language $language)
    {
        $language->fill($request->all());
        $language->save();
        return (new LanguageResource($language))->additional(['status' => 200, 'message' => __('messages.store')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        $language->update($request->all());
        return (new LanguageResource($language))->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Language $language)
    {
        $language->delete();
        return response()->json(['status' => 200, 'message' => __('messages.destroy')]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\{Area, Store};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Area $area)
    {
        return (new AreaResource($area))->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Area $area)
    {
        $area->fill($request->all());
        $area->section_number = $request->section_right - $request->section_left +1;
        $area->show = $request->section_left. '-'. $request->section_right;
        $area->save();

        return (new AreaResource($area))->additional(['status' => 200, 'message' => __('messages.store')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        $data = json_decode($request->data, true);

        foreach ($data as $key => $value) {
            Area::where('id', (int)$value['id'])->update([
                'section_left' => (int)$value['section_left'],
                'section_right' => (int)$value['section_right']?:NULL,
                'section_number' => (int)$value['section_right']?(int)$value['section_right'] - (int)$value['section_left'] + 1:NULL,
                'show' => (int)$value['section_right']?(int)$value['section_left']. '-'. (int)$value['section_right']:(int)$value['section_left']. '-',
            ]);
        }

        $areas = Store::find($request->id)->areas;

        return AreaResource::collection($areas)->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        $area->delete();
        return response()->json(['message' => __('messages.destory'), 'status' => 200]);
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Store;
use App\Http\Resources\{ImageResource, PlaceCollection};
use Illuminate\Http\Resources\Json\Resource;

class PlaceResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->date = strtotime($request->date);
        $this->meal_time = $request->meal_time;
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'name' => $this->name,
            'image' => new ImageResource($this->image),
            'number' => $this->number,
            'floor' => $this->floor,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
            'place' => new PlaceCollection($this->where('floor',$this->id)->get()->map(function ($item, $key){
                $store = Store::find($this->store_id);
                $type = $store->checkTimeArea($this->meal_time);
                $item->bookings = $store->manyBook()->where('place_id',$item->id)->where('date',$this->date)->where('type',$type)->first();
                return $item;
            })),
        ];
    }
}

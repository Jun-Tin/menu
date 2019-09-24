<?php

namespace App\Http\Resources;

use App\Http\Resources\PlaceResource;
use Illuminate\Http\Resources\Json\Resource;

class BookResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            // 'place_id' => $this->place_id,
            'place' => new PlaceResource($this->place),
            'name' => $this->name,
            'area_code' => $this->area_code,
            'phone' => $this->phone,
            'gender' => $this->gender?'男生':'女生',
            'date' => $this->date?date('Y-m-d',$this->date):'',
            'meal_time' => $this->meal_time,
            'meal_number' => $this->meal_number,
            'lock_in' => $this->lock_in?date('Y-m-d H:i:s', $this->lock_in):'',
            'lock_out' => $this->lock_out?date('Y-m-d H:i:s', $this->lock_out):'',
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):''
        ];
    }
}

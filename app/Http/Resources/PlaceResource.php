<?php

namespace App\Http\Resources;

use App\Http\Resources\ImageResource;
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
        switch ($request->method()) {
            case 'GET':
                return [
                    'id' => $this->id,
                    'store_id' => $this->store_id,
                    'name' => $this->name,
                    'image' => new ImageResource($this->image),
                    'number' => $this->number,
                    'floor' => $this->floor,
                    'status' => $this->status,
                    'created_at' => $this->created_at?$this->created_at->format('Y/m/d H:i:s'):'',
                    'updated_at' => $this->updated_at?$this->updated_at->format('Y/m/d H:i:s'):'',
                    'order' => $this->order,
                ];
                break;
            default :
                return [
                    'id' => $this->id,
                    'store_id' => $this->store_id,
                    'name' => $this->name,
                    'image' => new ImageResource($this->image),
                    'number' => $this->number,
                    'floor' => $this->floor,
                    'status' => $this->status,
                    'created_at' => $this->created_at?$this->created_at->format('Y/m/d H:i:s'):'',
                    'updated_at' => $this->updated_at?$this->updated_at->format('Y/m/d H:i:s'):'',
                ];
                break;
        }
    }
}

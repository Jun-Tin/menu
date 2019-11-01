<?php

namespace App\Http\Resources;

use App\Http\Resources\{PlaceResource};
use Illuminate\Http\Resources\Json\Resource;

class OrderResource extends Resource
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
            'order' => $this->order,
            'set_time' => $this->set_time,
            'place' => new PlaceResource($this->place),
            'price' => $this->price,
            'final_price' => $this->final_price,
            'number' => $this->number,
            'final_number' => $this->final_number,
            'finish_number' => $this->finish_number,
            'sitter' => $this->sitter,
            'status' => $this->status,
            'finish' => $this->finish,
            'details' => $this->details,
            'clean' => $this->clean,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
        ];
    }
}

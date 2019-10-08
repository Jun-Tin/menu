<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ShopcartResource extends Resource
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
            'place_id' => $this->place_id,
            'menu_id' => $this->menu_id,
            'menus_id' => $this->menus_id,
            'tags_id' => $this->tags_id,
            'number' => $this->number,
            'price' => $this->price,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
        ];
    }
}

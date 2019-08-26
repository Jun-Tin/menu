<?php

namespace App\Http\Resources;

use App\Http\Resources\ImageResource;
use App\Http\Resources\MenuCollection;
use Illuminate\Http\Resources\Json\Resource;

class MenuResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->tags);
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'name' => $this->name,
            'image' => new ImageResource($this->image),
            'original_price' => $this->original_price,
            'special_price' => $this->special_price,
            'level' => $this->level,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
            'tags' => new MenuCollection($this->tags)
        ];
    }
}

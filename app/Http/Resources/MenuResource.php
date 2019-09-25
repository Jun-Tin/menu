<?php

namespace App\Http\Resources;

use App\Http\Resources\{ImageResource, MenuResource, MenuCollection};
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
        if ($this->category == 'm') {
            return [
                'id' => $this->id,
                'store_id' => $this->store_id,
                'name' => $this->name,
                'image' => new ImageResource($this->image),
                'original_price' => $this->original_price,
                'special_price' => $this->special_price,
                'level' => $this->level,
                'type' => $this->type,
                'category' => $this->category,
                'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
                'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
                'tags' => new MenuCollection($this->tags),
                'class' => $this->tags->where('category','class')->pluck('id'),
                'perfer' => $this->tags->where('category','perfer')->pluck('id'),
            ];
        } else {
            return [
                'id' => $this->id,
                'store_id' => $this->store_id,
                'name' => $this->name,
                'image' => new ImageResource($this->image),
                'original_price' => $this->original_price,
                'special_price' => $this->special_price,
                'level' => $this->level,
                'type' => $this->type,
                'category' => $this->category,
                'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
                'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
                'tags' => TagResource::collection($this->tags()->wherePivot('pid',0)->get()),
            ];
        }
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Tag;
use App\Http\Resources\{ImageResource, MenuResource, MenuCollection, TagCollection};
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
        switch ($this->category) {
            case 'm':
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
                    'status' => $this->status,
                    'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
                    'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
                    'class' => new MenuCollection($this->tags()->where('category','class')->get()),
                    'perfer' => new MenuCollection($this->tags()->where('category','perfer')->get()->map(function ($item, $key){
                        $item->category = new TagCollection(Tag::where('pid',$item->pivot->target_id)->get());
                        return $item;
                    })),
                    'class_id' => $this->tags->where('category','class')->pluck('id'),
                    'perfer_id' => $this->tags->where('category','perfer')->pluck('id'),
                ];
                break;
            
            default:
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
                    'status' => $this->status,
                    'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
                    'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
                    'tags' => TagResource::collection($this->tags()->wherePivot('pid',0)->get()),
                ];
                break;
        }
    }
}

<?php

namespace App\Http\Resources;

use App\Models\{Menu, Tag, Image};
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\{MenuCollection, TagCollection};

class TagResource extends Resource
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
            'pid' => $this->pid,
            'store_id' => $this->store_id,
            'name' => $this->name,
            'category' => $this->category,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
            'pivot' => $this->whenPivotLoaded('menu_tag', function(){
                return $this->pivot;
            }),
            'menus' => $this->whenPivotLoaded('menu_tag', function(){
                return Menu::find($this->pivot->menu_id)->menus($this->pivot->id)->get()->map(function ($item, $key){
                    $item->image = Image::find($item->image_id);
                    $item->perfer = $item->tags()->where('category', 'perfer')->get()->map(function ($item, $key){
                        $item->category = new TagCollection(Tag::where('pid', $item->pivot->target_id)->get());
                        return $item;
                    });
                    return $item;
                });
            }), 
        ];
    }
}

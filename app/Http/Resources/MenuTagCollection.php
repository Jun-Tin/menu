<?php

namespace App\Http\Resources;

use App\Models\{Menu, MenuTag, Tag, Image};
use App\Http\Resources\TagCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuTagCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($item) {
                $item->menus = MenuTag::where('pid', $item->id)->orderBy('order_number')->orderByDesc('id')->get()->map(function ($item){
                    $menu = Menu::find($item->target_id);
                    $item->name = $menu->name;
                    $item->name_en = $menu->name_en;
                    $item->image = $menu->image;
                    $item->perfer = $menu->tags()->where('category', 'perfer')->get()->map(function ($item, $key){
                        $item->category = new TagCollection(Tag::where('pid', $item->pivot->target_id)->get());
                        return $item;
                    });
                    return $item;
                });
                return $item;
            }), 
        ];
    }
}

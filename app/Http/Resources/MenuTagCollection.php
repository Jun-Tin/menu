<?php

namespace App\Http\Resources;

use App\Models\{Menu, MenuTag, Image};
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
                MenuTag::where('pid', $item->id)->get()->map(function ($value) use ($item){
                    dump($value);
                    $menus[] = Menu::where('id', $value->target_id)->get();
                    $item->menus = $menus;
                    return $item;
                });
                return $item;
            }), 

            // 'menus' => $this->collection->map(function ($item) {
            //     return Menu::find($this->pivot->menu_id)->menus($this->pivot->id)->get()->map(function ($item, $key){
            //         $item->image = Image::find($item->image_id);
            //         $item->perfer = $item->tags()->where('category', 'perfer')->get()->map(function ($item, $key){
            //             $item->category = new TagCollection(Tag::where('pid', $item->pivot->target_id)->get());
            //             return $item;
            //         });
            //         return $item;
            //     });
            // }), 
        ];
    }
}

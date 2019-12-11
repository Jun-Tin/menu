<?php

namespace App\Http\Resources;

use App\Models\{Menu, Tag};
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopcartCollection extends ResourceCollection
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
            'data' => $this->collection->map(function ($item){
                $item->menu_name = (Menu::find($item->menu_id, ['name']))->name;
                if ($item->menus_id) {
                    foreach (json_decode($item->menus_id) as $key => $value) {
                        // $names[] = '';
                        $names[] = Menu::where('id', $value)->value('name')?:'';
                        // if (!empty($value)) {
                        // }
                    }
                    $item->menus_name = $names;
                }
                if ($item->tags_id) {
                    foreach (json_decode($item->tags_id) as $k => $value) {
                        $name[] = Tag::find($value)->pluck('name')?:'';
                        // $name[] = '';
                        // if (!empty($value)) {
                        // }
                    }
                    $item->tags_name = $name;
                }
                $item->fill_price = json_decode($item->fill_price);
                $item->remark = json_decode($item->remark);

                return $item;
            }),
        ];
    }
}

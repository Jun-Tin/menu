<?php

namespace App\Http\Resources;

use App\Models\{Menu, Tag};
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
            'state' => $this->state,
            'paid_at' => $this->paid_at?$this->paid_at->format('Y/m/d H:i:s'):'',
            'package' => $this->package->map(function ($item){
                $item->menu_name = Menu::where('id', $item->menu_id)->value('name');
                $item->category = Menu::where('id', $item->menu_id)->value('category');
                if ($item->category == 'p') {
                    $item->details = $item->where('pid', $item->id)->get()->map(function ($item, $key){
                        if ($item->menus_id) {
                            $item->menus_name = Menu::where('id', $item->menus_id)->value('name');
                        }

                        if (!empty(json_decode($item->tags_id,true))) {
                            foreach (json_decode($item->tags_id,true) as $k => $value) {
                                $name[] = Tag::where('id', $value)->value('name');
                            }
                            $item->tags_name = $name;
                        }
                        return $item;
                    });
                }

                if (!empty(json_decode($item->tags_id,true))) {
                    foreach (json_decode($item->tags_id,true) as $k => $value) {
                        $name[] = Tag::where('id', $value)->value('name');
                    }
                    $item->tags_name = $name;
                }
                return $item;
            }),
            'clean' => $this->clean,
            'place_name' => $this->place_name,
            'created_at' => $this->created_at?$this->created_at->format('Y/m/d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y/m/d H:i:s'):'',
        ];
    }
}

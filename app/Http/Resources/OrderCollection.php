<?php

namespace App\Http\Resources;

use App\Models\{Menu, Tag, Place, Behavior, Image};
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    private $param;

    public function __construct($resource, $param = false) {
        // Ensure we call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->param = $param; // $param param passed
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        switch ($this->param) {
            case 'orders':
                return [
                    'data' => [
                        'unfinished' => $this->collection['unfinished']->flatten()->filter(function ($item){
                            $item->place_name = Place::where('id',$item->place_id)->value('name');
                            if ($item->pid) {
                                $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
                            } else{
                                $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
                            }

                            if (!empty(json_decode($item->tags_id,true))) {
                                foreach (json_decode($item->tags_id,true) as $k => $value) {
                                    $name[] = Tag::where('id',$value)->value('name');
                                }
                                $item->tags_name = $name;
                            }
                            $item->remark = $item->remark;
                            $item->path = Image::where('id',$item->menus_id)->value('path');
                            return $item;
                        })->values(),
                        'finished' => $this->collection['finished']->flatten()->filter(function ($item){
                            $item->place_name = Place::where('id',$item->place_id)->value('name');
                            if ($item->pid) {
                                $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
                            } else{
                                $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
                            }

                            if (!empty(json_decode($item->tags_id,true))) {
                                foreach (json_decode($item->tags_id,true) as $k => $value) {
                                    $name[] = Tag::where('id',$value)->value('name');
                                }
                                $item->tags_name = $name;
                            }
                            $item->remark = $item->remark;
                            $item->path = Image::where('id',$item->menus_id)->value('path');
                            return $item;
                        })->values(),
                        'myself' => $this->collection['behavior']->flatten()->filter(function ($item){
                            $behavior = Behavior::where('target_id',$item->id)->where('category','cooking')->first();
                            if ($behavior->user_id == auth()->id()) {
                                $item->place_name = Place::where('id',$item->place_id)->value('name');
                                if ($item->pid) {
                                    $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
                                } else{
                                    $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
                                }

                                if (!empty(json_decode($item->tags_id,true))) {
                                    foreach (json_decode($item->tags_id,true) as $k => $value) {
                                        $name[] = Tag::where('id',$value)->value('name');
                                    }
                                    $item->tags_name = $name;
                                }
                                $item->remark = $item->remark;
                                $item->behavior = $behavior;
                                $item->path = Image::where('id',$item->menus_id)->value('path');
                                return $item;
                            }
                        })->values(),
                    ],
                    'unfinished_count' => $this->collection['unfinished']->flatten()->values()->count(),
                    'finished_count' => $this->collection['finished']->flatten()->values()->count(),
                ];
                break;
            case 'serving':
                return [
                    'finished' => $this->collection['finished']->flatten()->filter(function ($item) use ($request){
                        $item->place_name = Place::where('id',$item->place_id)->value('name');
                        if ($item->pid) {
                            $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
                        } else{
                            $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
                        }

                        if (!empty(json_decode($item->tags_id,true))) {
                            foreach (json_decode($item->tags_id,true) as $k => $value) {
                                $name[] = Tag::where('id',$value)->value('name');
                            }
                            $item->tags_name = $name;
                        }
                        $item->remark = $item->remark;

                        if ($request->keyword) {
                            if (strpos($item->place_name, $request->keyword) !== FALSE) {
                                return $item;
                            }
                        } else {
                            return $item;
                        }
                    })->values(),
                    'myself' => $this->collection['behavior']->flatten()->filter(function ($item){
                        $behavior = Behavior::where('target_id',$item->id)->where('category','serving')->first();
                        if ($behavior->user_id == auth()->id()) {
                            $item->place_name = Place::where('id',$item->place_id)->value('name');
                            if ($item->pid) {
                                $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
                            } else{
                                $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
                            }

                            if (!empty(json_decode($item->tags_id,true))) {
                                foreach (json_decode($item->tags_id,true) as $k => $value) {
                                    $name[] = Tag::where('id',$value)->value('name');
                                }
                                $item->tags_name = $name;
                            }
                            $item->remark = $item->remark;
                            $item->behavior = $behavior;
                            return $item;
                        }
                    })->values(),
                ];
                break;
        }
    }
}

<?php

namespace App\Http\Resources;

use App\Models\{Tag, Image};
use Illuminate\Http\Resources\Json\ResourceCollection;

class TagCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'data' => $this->collection->map(function ($item) {
                $item->menus = Tag::find($item->id)->menus()->where('category','m')->where('status',1)->get()->map(function ($item) {
                    $item->image = Image::find($item->image_id);
                    return $item;
                });
                return $item;
            }),
        ];
    }
}

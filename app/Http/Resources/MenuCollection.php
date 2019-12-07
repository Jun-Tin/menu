<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        switch ($request->type) {
            case 'in':
                return [
                    'data' => $this->collection->filter(function ($item){
                        if ($item->menuTag()->where('pid', '<>', 0)->get()->isNotEmpty()) {
                            $item->image = Image::find($item->image_id);
                            return $item;
                        }
                    })
                ];
                break;
            default:
                return [
                    'data' => $this->collection->filter(function ($item){
                        $item->image = Image::find($item->image_id);
                        return $item;
                    })
                ];
                break;
        }
    }
}

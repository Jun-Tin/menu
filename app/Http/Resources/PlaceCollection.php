<?php

namespace App\Http\Resources;

use App\Models\{Image, Place, Order};
use App\Http\Resources\PlaceResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlaceCollection extends ResourceCollection
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
        return [
            'data' => $this->collection->map(function ($item) use($request){
                $where[] = ['floor', $item->id];
                if ($request->number) {
                    $where[] = ['number', '>=', $request->number];
                }
                $item->image = Image::find($item->image_id);
                $item->place = PlaceResource::collection($item->where($where)->get())->map(function ($item){
                    switch ($this->param) {
                        case 'places':
                            $item->order = '';
                            if ($item->status == 1) {
                                $item->order = $item->order()->orderByDesc('created_at')->first();
                            }
                            break;
                        
                        case 'retreat':
                            $item->order = Order::where('place_id', $item->id)->where('status', 0)->orderByDesc('created_at')->first();
                            break;
                        /*case 'PUT':
                            $item->place = PlaceResource::collection($item->place);
                            break;*/
                    }
                    return $item;
                });
                return $item;
            }),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Menu;
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
        return parent::toArray($request);
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Store;
use Illuminate\Http\Resources\Json\Resource;

class AreaResource extends Resource
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
            'store_id' => $this->store_id,
            'name' => $this->name,
            'section_left' => $this->section_left,
            'section_right' => $this->section_right,
            'section_number' => $this->section_number,
            'sign' => $this->sign,
            'show' => $this->show,
        ];
    }
}

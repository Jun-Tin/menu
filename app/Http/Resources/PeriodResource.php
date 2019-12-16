<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PeriodResource extends Resource
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
            'title' => $this->title,
            'number' => $this->number,
            'show' => $this->show,
            'order_number' => $this->order_number,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
        ];
    }
}

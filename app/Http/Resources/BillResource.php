<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class BillResource extends Resource
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
            'order' => $this->order,
            'operate' => $this->operate,
            'accept' => $this->accept,
            'execute' => $this->execute,
            'type' => $this->type,
            'number' => $this->number,
            'method' => $this->method,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PaymentMethodResource extends Resource
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
            'name_cn' => $this->name_cn,
            'name_en' => $this->name_en,
            'show' => $this->show,
            'order_number' => $this->order_number,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TypeResource extends Resource
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
            'name_hk' => $this->name_hk,
            'name_en' => $this->name_en,
            'show' => $this->show,
            'created_at' => $this->created_at?$this->created_at->format('Y/m/d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y/m/d H:i:s'):'',
        ];
    }
}

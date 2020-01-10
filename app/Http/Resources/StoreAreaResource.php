<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class StoreAreaResource extends Resource
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
            'screen_link' => $this->screen_link,
            'screen_qrcode' => $this->screen_qrcode,
            'line_qrcode' => $this->line_qrcode,
            'book_qrcode' => $this->book_qrcode,
            'created_at' => $this->created_at?$this->created_at->format('Y/m/d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y/m/d H:i:s'):'',
        ];
    }
}

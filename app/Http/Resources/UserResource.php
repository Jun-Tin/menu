<?php

namespace App\Http\Resources;

use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'store_id' => $this->store_id,
            'area_code' => $this->area_code,
            'phone' => $this->phone,
            'coins' => $this->coins,
            'image' => new ImageResource($this->image),
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'post' => $this->post,
            'entry_time' => $this->entry_time?date('Y-m-d',$this->entry_time):'',
            'password' => $this->pro_password,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):''
        ];
    }
}

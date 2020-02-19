<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Http\Resources\UserResource;
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
            'target' => $this->target,
            'execute' => $this->execute,
            'type' => $this->type? new UserResource(User::where('id', $this->accept)->first()):'系统',
            'number' => $this->number,
            'method' => $this->method,
            'category' => $this->category,
            'created_at' => $this->created_at?$this->created_at->format('Y/m/d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y/m/d H:i:s'):''
        ];
    }
}

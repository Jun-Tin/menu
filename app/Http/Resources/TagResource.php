<?php

namespace App\Http\Resources;

use App\Models\Package;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\MenuCollection;

class TagResource extends Resource
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
            'pid' => $this->pid,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'category' => $this->category,
            'created_at' => $this->created_at?$this->created_at->format('Y-m-d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y-m-d H:i:s'):'',
            'pivot' => $this->whenPivotLoaded('package_group', function(){
                return $this->pivot;
            }),
            'menus' => $this->whenPivotLoaded('package_group', function(){
                return new MenuCollection(Package::find($this->pivot->package_id)->menus($this->pivot->id)->get());
            }), 
        ];
    }
}

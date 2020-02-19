<?php

namespace App\Http\Resources;

use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\Resource;

class StoreResource extends Resource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'address' => $this->address,
            'image' => new ImageResource($this->image),
            'phone' => $this->phone,
            'start_time' => $this->start_time,
            'after_start' => $this->after_start,
            'after_end' => $this->after_end,
            'end_time' => $this->end_time,
            'intro' => $this->intro,
            'set_time' => $this->set_time,
            'clean' => $this->clean,
            'settle' => $this->settle,
            'active' => $this->active,
            'days' => $this->days,
            'type' => $this->type,
            'interval' => $this->interval,
            'condition' => $this->condition,
            'language' => $this->language,
            'line' => $this->line,
            'chef' => $this->chef,
            'actived_at' => $this->actived_at,
            'created_at' => $this->created_at?$this->created_at->format('Y/m/d H:i:s'):'',
            'updated_at' => $this->updated_at?$this->updated_at->format('Y/m/d H:i:s'):'',
        ];
    }
}

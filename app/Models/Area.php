<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
    	'store_id', 'name', 'section_left', 'section_right', 'section_number', 'sign', 'show'
    ];

    /** 【 一对多排队关联关系 】 */
    public function lines()
    {
    	return $this->hasMany(Line::class, 'area_id', 'id');
    }
}

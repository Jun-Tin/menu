<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTag extends Model
{
    protected $table = 'menu_tag';

    protected $fillable = [
        'menu_id', 'target_id', 'name', 'pid', 'fill_price', 'order_number'
    ];

    /** 【 多对多标签嵌入菜品关联关系 】 */
    public function menuTags()
    {
        return $this->hasMany(self::class, 'pid');
    }
}

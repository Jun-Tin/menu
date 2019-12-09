<?php 
namespace App\Observers;

use App\Models\Place;

class PlaceObserver
{
	public function created(Place $place)
	{
		$place->update([
			'name' => '新座位'.$place->id,
			'number' => 1,
			'image_id' => 0,
		]);
	}
} 
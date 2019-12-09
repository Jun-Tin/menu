<?php 
namespace App\Observers;

use App\Models\Place;

class PlaceObserver
{
	public function saving(Place $place)
	{
		$last = Place::where('store_id', $place->store_id)->where('floor', '<>', 0)->orderBy('id', 'desc')->first();
		dd($last);
		$name = Place::where('store_id', $place->store_id)->where('floor', '<>', 0)->value('name')?Place::where('store_id', $place->store_id)->where('floor', '<>', 0)->value('name')+1:'001';
		dd($name);
		$place->update([
			'name' => '001',
			'number' => 1,
			'image_id' => 0,
		]);
	}
} 
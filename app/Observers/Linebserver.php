<?php 
namespace App\Observers;

use App\Models\Line;

class LineObserver
{
	public function created(Line $line)
	{
		dd($line);
	}
} 
<?php 
namespace App\Observers;

use App\Models\{Book, Behavior};

class BookObserver
{
	public function saved(Book $book)
	{
		// 记录员工表现
		Behavior::create([
			'user_id' => auth()->id(),
			'target_id' => $book->id,
			'category' => 'book',
			'status' => 1,
		]);
	}
} 
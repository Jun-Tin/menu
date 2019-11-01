<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class insertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('stores')->insert([
            'user_id' => '1',
            'name' => 'ceshi',
            'address' => 'ceshi',
            'image_id' => '1',
            'phone' => '1213124',
            'start_time' => '09:00',
            'end_time' => '23:00',
            'intro' => 'test',
            'set_time' => '40',
        ]);
    }
}

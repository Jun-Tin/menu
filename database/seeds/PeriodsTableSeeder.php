<?php

use App\Models\Period;
use Illuminate\Database\Seeder;

class PeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            [
                'title' => '30天',
                'number' => 30,
                'days' => 30,
                'discount' => '100%',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'title' => '90天',
                'number' => 80,
                'days' => 90,
                'discount' => '88%',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'title' => '180天',
                'number' => 150,
                'days' => 180,
                'discount' => '83%',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'title' => '365天',
                'number' => 288,
                'days' => 365,
                'discount' => '78%',
                'show' => 1,
                'order_number' => 1,
            ]
        ];

        foreach ($data as $key => $value) {
            Period::insert([
                'title' => $value['title'],
                'number' => $value['number'],
                'days' => $value['days'],
                'discount' => $value['discount'],
                'show' => $value['show'],
                'order_number' => $value['order_number'],
            ]);
        }
    }
}

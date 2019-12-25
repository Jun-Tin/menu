<?php

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsTableSeeder extends Seeder
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
                'name_cn' => '现金',
                'name_en' => 'Cash',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'name_cn' => '微信',
                'name_en' => 'WeChat Pay',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'name_cn' => '支付宝',
                'name_en' => 'Alipay',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'name_cn' => 'Apple Pay',
                'name_en' => 'Apple Pay',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'name_cn' => '信用卡',
                'name_en' => 'Credit Card',
                'show' => 1,
                'order_number' => 1,
            ],
            [
                'name_cn' => '其他',
                'name_en' => 'Others',
                'show' => 1,
                'order_number' => 1,
            ],
        ];

        foreach ($data as $key => $value) {
            PaymentMethod::insert([
                'name_cn' => $value['name_cn'],
                'name_en' => $value['name_en'],
                'show' => $value['show'],
                'order_number' => $value['order_number'],
            ]);
        }
    }
}

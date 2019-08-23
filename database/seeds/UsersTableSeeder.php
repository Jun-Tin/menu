<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = factory(User::class)->times(10)->make();
        User::insert($users->makeVisible(['password','remember_token'])->toArray());

        $user = User::find(1);
        $user->name = 'Admin';
        $user->email = 'admin@163.com';
        $user->area_code = '86';
        $user->phone = '15019424243';
        $user->password = bcrypt('password');
        $user->pro_password = 'password';
        $user->coins = '1000';
        $user->image_id = '1';
        $user->save();
    }
}

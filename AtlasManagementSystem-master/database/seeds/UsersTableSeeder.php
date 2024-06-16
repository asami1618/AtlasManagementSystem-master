<?php

use Illuminate\Database\Seeder;
use App\Models\Users\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
            'over_name' => '野間',
            'under_name' => '亜沙美',
            'over_name_kana' => 'ノマ',
            'under_name_kana' => 'アサミ',
            'mail_address' => 'noma@gmail.com',
            'sex' => '2',
            'birth_day' => '1998-07-16',
            'role' => '4',
            'password' => Hash::make('password')],
            // 'id' => 1,
            // 'remember_token' => str_random(10),
            // 'created_at' => DB::raw('NOW()'),
            // 'updated_at' => DB::raw('NOW()'),
            // 'deleted_at' => DB::raw('NOW()'),
        ]);    
    }
}
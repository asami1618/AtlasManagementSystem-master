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
            'id' => 1,
            'over_name' => '野間',
            'under_name' => '亜沙美',
            'over_name_kana' => 'ノマ',
            'under_name_kana' => 'アサミ',
            'mail_address' => 'noma@gmail.com',
            'sex' => '',
            'birth_day' => '',
            'role' => '',
            'password' => Hash::make('password'),
            'remember_token' => '',
            'created_at' => '',
            'updated_at' => '',
            'deleted_at' => '',
        ]);    
    }
}
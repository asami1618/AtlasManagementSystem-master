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
        ]);

        DB::table('users')->insert([
            [
            'over_name' => 'noma',
            'under_name' => 'asami',
            'over_name_kana' => 'ノマ',
            'under_name_kana' => 'アサミ',
            'mail_address' => '1616@gmail.com',
            'sex' => '2',
            'birth_day' => '2000-07-16',
            'role' => '4',
            'password' => Hash::make('password')],
        ]);
    }
}
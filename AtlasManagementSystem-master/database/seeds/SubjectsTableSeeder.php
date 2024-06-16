<?php

use Illuminate\Database\Seeder;
use App\Models\Users\Subjects;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 国語、数学、英語を追加

        DB::table('subjects')->insert([

            [
            'subject' => '国語',
            'created_at' => DB::raw('NOW()'),
            ],

            [
            'subject' => '数学',
            'created_at' => DB::raw('NOW()'),
            ],

            [
            'subject' => '英語',
            'created_at' => DB::raw('NOW()'),
            ],
        ]);
    }
}
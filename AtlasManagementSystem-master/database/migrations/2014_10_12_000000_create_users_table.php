<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->comment('id');
            $table->string('over_name', 60)->index('over_name')->comment('姓');
            $table->string('under_name', 60)->index('under_name')->comment('名');
            $table->string('over_name_kana', 60)->index('over_name_kana')->comment('セイ');
            $table->string('under_name_kana', 60)->index('under_name_kana')->comment('メイ');
            $table->string('mail_address', 60)->unique()->comment('メールアドレス');
            $table->integer('sex')->index('sex')->comment('性別:1=男性, 2=女性, 3=その他');
            $table->date('birth_day')->index('birth_day')->comment('生年月日');
            $table->integer('role')->index('role')->comment('権限:1=教師(国語), 2=教師(数学), 3=教師(英語), 4=生徒');
            $table->string('password', 191)->comment('パスワード');
            $table->rememberToken();
            $table->timestamp('created_at')->nullable()->comment('登録日時');
            $table->timestamp('updated_at')->default(DB::raw('current_timestamp on update current_timestamp'))->comment('更新日時');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
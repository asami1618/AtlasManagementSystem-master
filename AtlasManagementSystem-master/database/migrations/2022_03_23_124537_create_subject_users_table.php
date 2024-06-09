<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_users', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->comment('id');
            $table->integer('user_id')->comment('ユーザーid');
            $table->integer('subject_id')->comment('選択科目id');
            $table->timestamp('created_at')->nullable()->comment('登録日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_users');
    }
}
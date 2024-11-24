<?php
namespace App\Searchs;

use App\Models\Users\User;

class AllUsers implements DisplayUsers{

  public function resultUsers($keyword, $category, $updown, $gender, $role, $subjects){
    // 11/24 ユーザーテーブルを取得しているので全員表示される
    $users = User::all();
    return $users;
  }


}
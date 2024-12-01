<?php
namespace App\Searchs;

use App\Models\Users\User;

class SearchResultFactories{

  // 改修課題：選択科目の検索機能
  public function initializeUsers($keyword, $category, $updown, $gender, $role, $subjects){
    if($category == 'name'){ //ブラウザ上で「カテゴリ・名前」を選択した場合下記が動く
      if(is_null($subjects)){
        $searchResults = new SelectNames();
      }else{
        $searchResults = new SelectNameDetails(); //->を編集
      }
      return $searchResults->resultUsers($keyword, $category, $updown, $gender, $role, $subjects);
    }else if($category == 'id'){ //ブラウザ上で「カテゴリ・社員ID」を選択した場合下記が動く
      if(is_null($subjects)){
        $searchResults = new SelectIds();
      }else{
        $searchResults = new SelectIdDetails(); //->を編集
      }
      return $searchResults->resultUsers($keyword, $category, $updown, $gender, $role, $subjects);
    }else{
      // 11/24 $categoryがnameでもidでもない、検索する前の状態
      // AllUsersはuse宣言されていないが、この場合は同じディレクトリ内(Searchs)にいるため。
      $allUsers = new AllUsers();
    return $allUsers->resultUsers($keyword, $category, $updown, $gender, $role, $subjects);
    }
  }
}
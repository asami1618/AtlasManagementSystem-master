<?php
namespace App\Searchs;

use App\Models\Users\User;

class SelectIdDetails implements DisplayUsers{

  // 改修課題：選択科目の検索機能
  public function resultUsers($keyword, $category, $updown, $gender, $role, $subjects){
    if(is_null($keyword)){
      $keyword = User::get('id')->toArray();
    }else{
      $keyword = array($keyword);
    }
    if(is_null($gender)){
      $gender = ['1', '2', '3'];
    }else{
      $gender = array($gender);
    }
    if(is_null($role)){
      $role = ['1', '2', '3', '4'];
    }else{
      $role = array($role);
    }
    $users = User::with('subjects')
    ->whereIn('id', $keyword)
    ->where(function($q) use ($role, $gender){
      $q->whereIn('sex', $gender)
      ->whereIn('role', $role);
    })
    ->whereHas('subjects', function($q) use ($subjects){
      $q->where('subjects.id', $subjects);
    })
    ->orderBy('id', $updown)->get();
    return $users;
  }

}

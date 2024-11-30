<?php
namespace App\Searchs;

use App\Models\Users\User;

class SelectIdDetails implements DisplayUsers{

  // 改修課題：選択科目の検索機能
  public function resultUsers($keyword, $category, $updown, $gender, $role, $subjects){
    // 11/24　キーワードがnullの場合、全ユーザーを対象
    if(is_null($keyword)){
      $keyword = User::get('id')->toArray();
    }else{
      $keyword = array($keyword);
    }

    // 11/24　性別がnullの場合、全ての性別を対象
    if(is_null($gender)){
      $gender = ['1', '2', '3'];
    }else{
      $gender = array($gender);
    }

    // 11/24　権限がnullの場合、全ての権限を対象
    if(is_null($role)){
      $role = ['1', '2', '3', '4'];
    }else{
      $role = array($role);
    }

    // 11/24　ユーザーの絞り込み処理
    $users = User::with('subjects')
    ->whereIn('id', $keyword) //キーワードに一致
    ->where(function($q) use ($role, $gender){
      $q->whereIn('sex', $gender) //性別に一致
      ->whereIn('role', $role); //権限に一致
    })
    // 11/30　修正
    ->whereHas('subjects', function($q) use ($subjects) {
      foreach ($subjects as $index => $subjectId) {
        if ($index === 0) {
          $q->where('subjects.id', $subjectId);
        } else {
          $q->orWhere('subjects.id', $subjectId);
        }
      }
    })->orderBy('subjects.id', $updown)->get();

    // // 11/24　追記
    // $subjectIds = [1, 2, 3];
    // $users = User::whereHas('subjects', function ($q) use ($subjectIds) {
    //   $q->whereIn('subjects.id', $subjectIds); // 複数のIDに一致する条件
    // })->get();    
  
  return $users;
  }

}

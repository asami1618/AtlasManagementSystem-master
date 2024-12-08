<?php
namespace App\Calendars\General;

use App\Models\Calendars\ReserveSettings;
use Carbon\Carbon;
use Auth;

class CalendarWeekDay{
  protected $carbon;

  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  function getClassName(){
    return "day-" . strtolower($this->carbon->format("D"));
  }

  function pastClassName(){
    return;
  }

  /**
   * @return
   */

  function render(){
    return '<p class="day">' . $this->carbon->format("j"). '日</p>';
  }
  
  function selectPart($ymd){
    // 現在の日付
    $today = date('Y-m-d');
    // 特定の日付（$ymd）について、各時間帯（1部、2部、3部）の予約可能枠（limit_users）を取得。
    // 時間帯ごとの予約設定が存在しない場合は、枠数を 0 として返す。
    $one_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '1')->first();
    $two_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '2')->first();
    $three_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '3')->first();
    if($one_part_frame){
      $one_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '1')->first()->limit_users;
    }else{
      $one_part_frame = '0';
    }
    if($two_part_frame){
      $two_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '2')->first()->limit_users;
    }else{
      $two_part_frame = '0';
    }
    if($three_part_frame){
      $three_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '3')->first()->limit_users;
    }else{
      $three_part_frame = '0';
    }

    // // 過去日判定
    // if (strtotime($ymd) < strtotime($today) && $one_part_frame == '0' && $two_part_frame == '0' && $three_part_frame == '0') {
    //   // 過去日かつ予約枠が全て 0 の場合はセレクトボックスを削除
    //   return ''; // 空文字列を返してセレクトボックスを表示しない
    // }    
    
    
    // 過去日判定
    if (strtotime($ymd) < strtotime($today)) {
      if ($one_part_frame == 0 && $two_part_frame == 0 && $three_part_frame == 0) {
        // 予約がない場合
        $html[] = '<p>受付終了</p>';
      } else {
        // 予約がある場合
        $html[] = '<p>部参加</p>';
      }
      return implode('', $html); // 過去日の場合はここで終了
    }

    //   // 過去日は「受付終了」として選択肢を無効化
    //   $html[] = '</select>'; // select を閉じる
    //   $html[] = '<option value="" disabled>受付終了</option>';
    //   return implode('', $html); // ここで結果を返す 
    // }    
    
    // HTML配列の初期化
    $html = [];
    $html[] = '<select name="getPart[]" class="border-primary" style="width:70px; border-radius:5px;" form="reserveParts">';
    $html[] = '<option value="" selected></option>';   
    
    // 現在または未来日のオプションを追加
      if($one_part_frame == "0"){
        $html[] = '<option value="1" disabled>リモ1部(残り0枠)</option>';
      }else{
        $html[] = '<option value="1">リモ1部(残り'.$one_part_frame.'枠)</option>';
      }
      if($two_part_frame == "0"){
        $html[] = '<option value="2" disabled>リモ2部(残り0枠)</option>';
      }else{
        $html[] = '<option value="2">リモ2部(残り'.$two_part_frame.'枠)</option>';
      }
      if($three_part_frame == "0"){
        $html[] = '<option value="3" disabled>リモ3部(残り0枠)</option>';
      }else{
        $html[] = '<option value="3">リモ3部(残り'.$three_part_frame.'枠)</option>';
      }
      $html[] = '</select>';
      return implode('', $html);
  }

  function getDate(){
    return '<input type="hidden" value="'. $this->carbon->format('Y-m-d') .'" name="getData[]" form="reserveParts">';
  }

  function everyDay(){
    return $this->carbon->format('Y-m-d');
  }

  function authReserveDay(){
    return Auth::user()->reserveSettings->pluck('setting_reserve')->toArray();
  }

  function authReserveDate($reserveDate){
    return Auth::user()->reserveSettings->where('setting_reserve', $reserveDate);
  }

}
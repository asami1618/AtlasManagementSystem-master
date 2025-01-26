<?php
namespace App\Calendars\General;

use Carbon\Carbon;
use Auth;

class CalendarView{

  private $carbon;
  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  public function getTitle(){
    return $this->carbon->format('Y年n月');
  }

  function render(){
    $html = [];
    $html[] = '<div class="calendar text-center">';
    $html[] = '<table class="table">';
    $html[] = '<thead>';
    $html[] = '<tr>';
    $html[] = '<th>月</th>';
    $html[] = '<th>火</th>';
    $html[] = '<th>水</th>';
    $html[] = '<th>木</th>';
    $html[] = '<th>金</th>';
    $html[] = '<th>土</th>';
    $html[] = '<th>日</th>';
    $html[] = '</tr>';
    $html[] = '</thead>';
    $html[] = '<tbody>';
    $weeks = $this->getWeeks();
    foreach($weeks as $week){
      $html[] = '<tr class="'.$week->getClassName().'">';
      
      $days = $week->getDays();
      foreach($days as $day){
        $startDay = $this->carbon->copy()->format("Y-m-01");
        $toDay = $this->carbon->copy()->format("Y-m-d");
        
        if($startDay <= $day->everyDay() && $toDay >= $day->everyDay()){
          $html[] = '<td class="past-day border">';
        }else{
          $html[] = '<td class="border '.$day->getClassName().'">';
        }
        $html[] = $day->render();
        
        // 予約状態のチェック
        // $day->everyDay() は現在のカレンダーの日付
        // $day->authReserveDay() は、現在ログインしているユーザーが予約している日付のリスト。
        // 予約がある日
        if(in_array($day->everyDay(), $day->authReserveDay())){
          $reservePart = $day->authReserveDate($day->everyDay())->first()->setting_part;
          if($reservePart == 1){
            $reservePart = "リモ1部";
          }else if($reservePart == 2){
            $reservePart = "リモ2部";
          }else if($reservePart == 3){
            $reservePart = "リモ3部";
          }
          // 12/15　
          // 7３行目の処理の目的:この条件により$day->everyDay()が
          // $startDayから$toDayの範囲内の日付であるかどうかを判定している
          
          // $startDay: 日付範囲の開始日を表している変数
          // $toDay: 日付範囲の終了日を表している変数
          // $day->everyDay(): 現在の日付（または特定の繰り返し日）を取得するメソッド
          
          // $startDay <= $day->everyDay():
          // ->現在の日付（$day->everyDay()）が日付範囲の開始日よりも後か、または等しい
          // $toDay >= $day->everyDay():
          // ->現在の日付（$day->everyDay()）が日付範囲の終了日よりも前か、または等しい
          
          // 12/15　条件が成立した場合の処理
          if($startDay <= $day->everyDay() && $toDay >= $day->everyDay()){
            $reserve = $day->authReserveDate($day->everyDay())->first();
            $html[] = '<p class="m-auto p-0 w-75" style="font-size:16px">リモ' . $reserve->setting_part  . '部</p>';
            $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
          }else{
            // 予約データを取得する例 (配列を仮定)
            $date = $day->everyDay(); // 予約日
            $reserve = $day->authReserveDate($date)->first();

            if($reserve) {
              $part = $reserve->setting_part;
              // キャンセルボタン
              $html[] = '<button type="button" class="btn btn-danger p-0 w-75" data-bs-toggle="modal" data-bs-target= #cancelModal data-date="'  . $day->authReserveDate($day->everyDay())->first()->setting_reserve .'" data-part="' . htmlspecialchars($reserve->setting_part, ENT_QUOTES, 'UTF-8') . '" style="font-size:12px">'. $reservePart .'</button>';
              $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
            }

            // モーダルを追加
            $html[] = '<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">';
            $html[] = '  <div class="modal-dialog">';
            $html[] = '    <div class="modal-content">';
            $html[] = '      <div class="modal-body">';
            $html[] = '        <p>予約日:</p>';
            $html[] = '        <p>時間:リモ 部</p>';
            $html[] = '        <p>上記の予約をキャンセルしてもよろしいですか？</p>';
            $html[] = '      </div>';
            $html[] = '      <div class="modal-footer justify-content-between">';
            $html[] = '        <button type="button" class="btn btn-secondary bg-primary" data-bs-dismiss="modal">閉じる</button>';
            $html[] = '        <button type="button" class="btn btn-danger" id="confirmCancel" data-reservation-id="' . $day->authReserveDate($day->everyDay())->first()->id . '">キャンセルする</button>';
            $html[] = '      </div>';
            $html[] = '    </div>';
            $html[] = '  </div>';
            $html[] = '</div>';
            $html[] = '<script>
          </script>';            
          }
        }else{ //予約がない日->CalendarWeekDayクラスのselectPart()メソッド
          $html[] = $day->selectPart($day->everyDay());
        }
        $html[] = $day->getDate();
        $html[] = '</td>';
      }
      $html[] = '</tr>';
    }
    $html[] = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';
    $html[] = '<script>
      document.addEventListener("DOMContentLoaded", function () {
      // ①DOMContentLoaded イベント:ページの読み込みが完了したときに実行されるイベントリスナーを設定、ページが完全に読み込まれるまでスクリプトの処理を遅らせる
        const cancelModal = document.getElementById("cancelModal");
        // ②モーダルを取得:Dが cancelModal の要素（モーダル）を取得、このモーダルはキャンセル確認のUI要素を指す
        cancelModal.addEventListener("show.bs.modal", function (event) {
        // ③show.bs.modal イベントリスナーを追加:Bootstrapモーダルが表示される直前（show.bs.modal イベント）に実行される処理を設定
        // event オブジェクトは、モーダルを表示するトリガーとなった要素（ボタンなど）に関する情報

        // ④トリガーボタンの情報を取得:
        const button = event.relatedTarget; 
        const date = button.getAttribute("data-date");
        const part = button.getAttribute("data-part");
        const reservationId = button.getAttribute("data-reservation-id");  // 予約IDを取得

        // event.relatedTarget はモーダルを表示するためにクリックされたボタン要素を指す
        // ボタンに設定されたカスタム属性（data-date, data-part, data-reservation-id）から値を取得
        // data-date: 予約日
        // data-part: 時間帯や区分（例: 午前・午後など）
        // data-reservation-id: キャンセル対象の予約ID

        // ⑤モーダルの内容を更新:
        cancelModal.querySelector(".modal-body p:nth-child(1)").textContent = "予約日: " + date;
        cancelModal.querySelector(".modal-body p:nth-child(2)").textContent = "時間: " + part + "部";
        // モーダル内の予約日や時間帯の表示を、ボタンから取得した値で動的に更新
        // querySelector:
        // .modal-body p:nth-child(1): モーダル内の最初の段落（予約日表示）
        // .modal-body p:nth-child(2): モーダル内の2番目の段落（時間帯表示）。

      // ⑥キャンセルボタンに予約IDを設定
      const cancelButton = cancelModal.querySelector("#confirmCancel");
      cancelButton.setAttribute("data-reservation-id", reservationId);
      });
      // モーダル内にあるキャンセルボタン（IDが confirmCancel）を取得
      // キャンセルボタンに data-reservation-id 属性を動的に設定し、対象の予約IDを埋め込む
      // これにより、キャンセルボタンがクリックされた際に予約IDを利用できるようになる

      // キャンセルボタンがクリックされたときの処理
      document.getElementById("confirmCancel").addEventListener("click", function () {
        const reservationId = this.getAttribute("data-reservation-id");
        const deleteForm = document.getElementById("deleteParts");

        // フォームに予約IDをセット
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "reservation_id"; // サーバーで受け取る予約IDの名前
        input.value = reservationId;
        deleteForm.appendChild(input);
        
        // フォームを送信
        deleteForm.submit();
        });
      });
      </script>';
    
    $html[] = '</tbody>';
    $html[] = '</table>';
    $html[] = '</div>';
    $html[] = '    
    ';
    $html[] = '<form action="/reserve/calendar" method="post" id="reserveParts">'.csrf_field().'</form>';
    $html[] = '<form action="/delete/calendar" method="post" id="deleteParts">'.csrf_field().'</form>';

    return implode('', $html);
  }

  protected function getWeeks(){
    $weeks = [];
    $firstDay = $this->carbon->copy()->firstOfMonth();
    $lastDay = $this->carbon->copy()->lastOfMonth();
    $week = new CalendarWeek($firstDay->copy());
    $weeks[] = $week;
    $tmpDay = $firstDay->copy()->addDay(7)->startOfWeek();
    while($tmpDay->lte($lastDay)){
      $week = new CalendarWeek($tmpDay, count($weeks));
      $weeks[] = $week;
      $tmpDay->addDay(7);
    }
    return $weeks;
  }
}
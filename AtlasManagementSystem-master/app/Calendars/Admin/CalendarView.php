<?php
namespace App\Calendars\Admin;
use Carbon\Carbon;
use App\Models\Users\User;

class CalendarView{
  private $carbon;

  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  public function getTitle(){
    return $this->carbon->format('Y年n月');
  }

  public function render(){
    $html = [];
    $html[] = '<div class="calendar text-center">';
    $html[] = '<table class="table m-auto border">';
    $html[] = '<thead>';
    $html[] = '<tr>';
    $html[] = '<th class="border">月</th>';
    $html[] = '<th class="border">火</th>';
    $html[] = '<th class="border">水</th>';
    $html[] = '<th class="border">木</th>';
    $html[] = '<th class="border">金</th>';
    $html[] = '<th class="border day-sat">土</th>';
    $html[] = '<th class="border day-sun">日</th>';
    $html[] = '</tr>';
    $html[] = '</thead>';
    $html[] = '<tbody>';

    $weeks = $this->getWeeks();

    // 日付に応じてクラス（スタイル）を付与しながら、
    // 適切なコンテンツを埋め込むカレンダー形式のHTMLを生成。
    foreach($weeks as $week){
      $html[] = '<tr class="'.$week->getClassName().'">';
      $days = $week->getDays();
      foreach($days as $day){
        // -日付の比較とクラスの付与-
        // ①現在の月の「月初日」を文字列として取得
        $startDay = $this->carbon->format("Y-m-01");
        // ②現在の日付を取得
        $toDay = $this->carbon->format("Y-m-d");
        //　12/6　↓過去の日付かどうかを判定
        // ③日付を条件に応じて判定。
        if($startDay <= $day->everyDay() && $toDay >= $day->everyDay()){
          $html[] = '<td class="past-day border '.$day->getClassName().'">';
        }else{
          $html[] = '<td class="border '.$day->getClassName().'">';
        }
        $html[] = $day->render();
        $html[] = $day->dayPartCounts($day->everyDay());
        $html[] = '</td>';
      }
      $html[] = '</tr>';
    }
    $html[] = '</tbody>';
    $html[] = '</table>';
    $html[] = '</div>';

    return implode("", $html);
  }

  protected function getWeeks(){
    $weeks = [];
    $firstDay = $this->carbon->copy()->firstOfMonth(); //現在の月の「1日目」の日付を取得
    $lastDay = $this->carbon->copy()->lastOfMonth(); //現在の月の「月末日」の日付を取得

    // 最初の週を表す "CalendarWeek"
    $week = new CalendarWeek($firstDay->copy());
    $weeks[] = $week;
    $tmpDay = $firstDay->copy()->addDay(7)->startOfWeek();

    // ↓while 条件 
    // $tmpDay（現在計算中の週の開始日）が月末日（$lastDay）以前である限りループを続行
    while($tmpDay->lte($lastDay)){
      // ↓新しい週を表す "CalendarWeek"
      // count($weeks)->現在までに作成された週の数（インデックス）を渡している
      $week = new CalendarWeek($tmpDay, count($weeks));
      // ↓配列 $weeks に現在の週を追加
      $weeks[] = $week;
      // ↓次の週の日付に進める
      $tmpDay->addDay(7);
    }
    return $weeks;
  }
}
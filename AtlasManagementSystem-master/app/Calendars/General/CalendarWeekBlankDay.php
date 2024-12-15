<?php
namespace App\Calendars\General;

use Carbon\Carbon;

class CalendarWeekBlankDay extends CalendarWeekDay{
  function getClassName(){
    return "day-blank";
  }

  /**
   * @return
   */

  function render(){
    return '';
  }

  function selectPart($ymd){
    return '';
  }

  function getDate(){
    return '';
  }

  function cancelBtn(){
    return '';
  }

  public function __construct($date) {
    $this->date = $date; // 日付をセット
  }

  public function everyDay() {
    return '';
  }
  
  public function authReserveDay() {
    // 空の配列を返す
    return [];
  }

}
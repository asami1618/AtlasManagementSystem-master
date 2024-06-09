<?php
namespace App\Calendars\General;

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

   function everyDay(){
     return '';
   }

}
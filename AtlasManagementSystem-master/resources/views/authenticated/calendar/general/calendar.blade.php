@extends('layouts.sidebar')

@section('content')
<!-- スクール予約(calendar.general.showが動く) -->
<head><meta name="csrf-token" content="{{ csrf_token() }}"></head>
<div class="vh-100 pt-5" style="background:#ECF1F6;">
  <div class="border w-75 m-auto pt-5 pb-5" style="border-radius:5px; background:#FFF;">
    <div class="w-75 m-auto border" style="border-radius:5px;">

    <!--12/8 カレンダーのタイトルを取得 -->
      <p class="calendar_title">{{ $calendar->getTitle() }}</p> 
      <div class="">
        <!--12/8 カレンダーのHTMLを生成 -->
        {!! $calendar->render() !!}
      </div>
    </div>
    <div class="text-right w-75 m-auto">
      <input type="submit" class="btn btn-primary reserve" value="予約する" form="reserveParts">
    </div>
  </div>
</div>
@endsection
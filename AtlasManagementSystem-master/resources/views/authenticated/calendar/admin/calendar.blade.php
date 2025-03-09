@extends('layouts.sidebar')

@section('content')
<!-- スクール予約確認(calendar.admin.showが動く) -->
<div class="vh-100 pt-5">
  <div class="w-100">
    <div class="calendar_area border w-75 m-auto pt-5 pb-5" style="border-radius:5px; background:#FFF;">
    <div class="w-75 m-auto">
      <p class="calendar_title">{{ $calendar->getTitle() }}</p>
      <p>{!! $calendar->render() !!}</p>
    </div>
  </div>
</div>
@endsection
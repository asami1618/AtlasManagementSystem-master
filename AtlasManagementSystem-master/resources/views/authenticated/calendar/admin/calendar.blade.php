@extends('layouts.sidebar')

@section('content')
<!-- スクール予約確認(calendar.admin.showが動く) -->
<div class="w-75 m-auto">
  <div class="w-100">
    <p>{{ $calendar->getTitle() }}</p>
    <p>{!! $calendar->render() !!}</p>
  </div>
</div>
@endsection
@extends('layouts.sidebar')

@section('content')
<div class="board_area w-100 border m-auto d-flex">
  <div class="post_view w-75 mt-5">
    <p class="w-75 m-auto">投稿一覧</p>
    @foreach($posts as $post)
    <div class="post_area border w-75 m-auto p-3">
      <p><span>{{ $post->user->over_name }}</span><span class="ml-3">{{ $post->user->under_name }}</span>さん</p>
      <p><a href="{{ route('post.detail', ['id' => $post->id]) }}">{{ $post->post_title }}</a></p>
      <div class="post_bottom_area d-flex">
        <div class="d-flex post_status">
          <div class="mr-5">
            <!-- 9/21　追記　コメント数 -->
            <i class="fa fa-comment"></i><span class="commentCounts{{ $post->id }}">{{ $post_comment->commentCounts($post->id) }}</span>
          </div>
          <div>
            <!-- 9/21　追記　いいね数 -->
            @if(Auth::user()->is_Like($post->id))
            <p class="m-0"><i class="fas fa-heart un_like_btn" post_id="{{ $post->id }}"></i><span class="like_counts{{ $post->id }}">{{ $like->likeCounts($post->id) }}</span></p>
            @else
            <p class="m-0"><i class="fas fa-heart like_btn" post_id="{{ $post->id }}"></i><span class="like_counts{{ $post->id }}">{{ $like->likeCounts($post->id) }}</span></p>
            @endif
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  <div class="other_area border w-25">
    <div class="border m-4">
      <div class=""><a href="{{ route('post.input') }}"><button type="button" class="btn btn-info">投稿</button></a></div>
      <div class="">
        <input type="text" placeholder="キーワードを検索" name="keyword" form="postSearchRequest">
        <input type="submit" value="検索" class="btn btn-info" name="category_word" form="postSearchRequest">
      </div>
      <input type="submit" name="like_posts" class="btn btn-secondary btnx-indigo category_btn" value="いいねした投稿" form="postSearchRequest">
      <input type="submit" name="my_posts" class="btn btn-secondary btnx-indigo category_btn" value="自分の投稿" form="postSearchRequest">
      <ul>
        @foreach($categories as $category)
        <li class="main_categories" category_id="{{ $category->id }}"><span>{{ $category->main_category }}<span></li>
        @endforeach
      </ul>
    </div>

    <!-- カテゴリー検索 -->
    <div>
      <p>カテゴリー検索</p>
      <nav class="category-menu">
        <div class="category-menu-item">
          @foreach($categories as $category) 
          <div class="category-menu-item-btn">{{ $category->main_category }}</div>
          <ul>
            @foreach($category->subcategories as $sub_category)
            <li><a href="{{ route('post.show', $sub_category->sub_category) }}">{{ $sub_category->sub_category }}</a></li>
            @endforeach
          </ul>
          @endforeach
        </div>
      </nav>
    </div>    
  </div>
  <form action="{{ route('post.show') }}" method="get" id="postSearchRequest"></form>
</div>
@endsection
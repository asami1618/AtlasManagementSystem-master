@extends('layouts.sidebar')

@section('content')
<div class="board_area w-100 border m-auto d-flex">
  <div class="post_view w-75 mt-5">
    @foreach($posts as $post)
    <div class="post_area border w-75 m-auto p-3">
      @foreach($post->subCategories as $subcategory)
        <button type="button" class="btn btn-info sub_category_btn">
          {{ $subcategory->sub_category}}
        </button>
      @endforeach
      <p><span>{{ $post->user->over_name }}</span><span class="ml-3">{{ $post->user->under_name }}</span>さん</p>
      <p><a href="{{ route('post.detail', ['id' => $post->id]) }}">{{ $post->post_title }}</a></p>
      <div class="post_bottom_area d-flex">
        <div class="d-flex post_status w-100 justify-content-end">
          <div class="d-flex align-items-center mr-3">
            <!-- 9/21　追記　コメント数 -->
            <i class="fa fa-comment"></i>
            <span class="commentCounts{{ $post->id }} ml-1">{{ $post_comment->commentCounts($post->id) }}</span>
          </div>
          <div class="d-flex align-items-center">
            <!-- 9/21　追記　いいね数 -->
            @if(Auth::user()->is_Like($post->id))
            <p class="m-0  d-flex align-items-center">
              <i class="fas fa-heart un_like_btn" post_id="{{ $post->id }}"></i>
              <span class="like_counts{{ $post->id }}  ml-1">{{ $like->likeCounts($post->id) }}</span></p>
            @else
            <p class="m-0 d-flex align-items-center">
              <i class="fas fa-heart like_btn" post_id="{{ $post->id }}"></i>
              <span class="like_counts{{ $post->id }} ml-1">{{ $like->likeCounts($post->id) }}</span></p>
            @endif
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  <div class="other_area border w-25">
    <div class="border m-4">
      <div class="">
        <a href="{{ route('post.input') }}"><button type="button" class="btn btn-info post_button w-100">投稿</button></a>
      </div>
      <div class="input-group search">
        <input type="text" class="form-control" placeholder="キーワードを検索" name="keyword" form="postSearchRequest">
        <input type="submit" value="検索" class="btn btn-info post_search" name="category_word" form="postSearchRequest">
      </div>
      
      <!-- 横並び -->
      <div class="row">
        <div class="col">
          <input type="submit" name="like_posts" class="btn pink-bg category_btn w-100" value="いいねした投稿" form="postSearchRequest">
        </div>
        <div class="col">
          <input type="submit" name="my_posts" class="btn yellow-bg category_btn w-100" value="自分の投稿" form="postSearchRequest">
        </div>
      </div>
    </div>

    <!-- カテゴリー検索 -->
    <div>
      <p class="category_search_box">カテゴリー検索</p>
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
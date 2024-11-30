<?php

namespace App\Http\Controllers\Authenticated\BulletinBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\MainCategory;
use App\Models\Categories\SubCategory;
use App\Models\Posts\Post;
use App\Models\Posts\PostComment;
use App\Models\Posts\Like;
use App\Models\Users\User;
use App\Http\Requests\BulletinBoard\PostFormRequest;
use App\Http\Requests\PostEditFormRequest;
use App\Http\Requests\PostCommentFormRequest;
use App\Http\Requests\MainCategoryFormRequest;
use App\Http\Requests\SubCategoryFormRequest;

use Auth;

class PostsController extends Controller
{
    public function show(Request $request){
        $posts = Post::with('user', 'postComments')->get();
        $categories = MainCategory::get();
        $like = new Like;
        // $like_counts = $like->likeCounts();
        $post_comment = new Post;
        if(!empty($request->keyword)){
            // 11/24　追記　キーワード検索されたとき
            //　posts.blade.php 34行目<input type="text" placeholder="キーワードを検索" name="keyword" form="postSearchRequest">

            // 　サブカテゴリーの完全一致
            $sub_category_name = $request->keyword; // フォームで送信されたサブカテゴリー
            // $sub_category = SubCategory::whereHas->get();
            // dd($request->input('keyword'));
            // →11/25　修正　whereHasメソッドは「リレーション先のテーブルの条件で検索したいとき」に使用する。
            // whereHasの第一引数にはリレーションメソッド名が入る。
            // where('post_title', '=', $title)を使用することで、post_titleと完全一致した場合のみ結果が返される。
            

            //　11/30　追記
            $posts = Post::with('user', 'postComments')
            // Postモデルのリレーション名 'user'メソッドと'postComments'メソッドを取得
            // ※Post モデルにリレーションが定義されていることが前提
            ->where('post_title', 'like', '%'.$request->keyword.'%')
            // 最初の条件　基本構文　Model::where('カラム名', '演算子', '値')
            // 投稿タイトル(post_title)に、フォームから送信されたキーワード($request->keyword)が部分一致するデータを取得
            ->orWhere('post', 'like', '%'.$request->keyword.'%')
            // 基本構文　Model::where('カラム名', '演算子', '値')->orWhere('カラム名', '演算子', '値');
            // orWhere は「または」(OR) を用いて、既存の条件に新しい条件を追加
            ->orWhereHas('subCategories', function ($q) use ($sub_category_name) {
                $q->where('sub_category', '=', $sub_category_name); //'='は省略が可能、記載がなければ完全一致と認識できる
            // 1. orWhereHas('subCategories', ...)'subCategories'はPost モデルに定義されたリレーション名
            // 2. function ($q)　サブクエリを定義するクロージャ(無名関数)
            // 3. use ($sub_category_name)　外部の変数（$sub_category_name）をクロージャの中で利用するための宣言
            // 4. $q->where(...)　サブカテゴリの中で、特定の条件（ここでは sub_category = $sub_category_name）を満たすものを検索
            })->get();

        // }else if($request->category_word){
        //     $sub_category = $request->category_word;
        //     // 11/24　修正　サブカテゴリー名が一致する投稿名を取得
        //     $posts = Post::with('user', 'postComments')
        //     // '$sub_category'が文字列として扱われているため$sub_categoryに変更
        //     // post_titleカラムを指定し、曖昧検索を実行
        //     ->where('post_title', 'like', '%' . $sub_category . '%')->get(); 
        }else if($request->like_posts){
            // 11/24　追記　「いいねした投稿」というボタンが押された時
            // posts.blade.php 37行目<input type="submit" name="like_posts" class="btn btn-secondary btnx-indigo category_btn" value="いいねした投稿" form="postSearchRequest">
            $likes = Auth::user()->likePostId()->get('like_post_id');
            $posts = Post::with('user', 'postComments')
            ->whereIn('id', $likes)->get();
        }else if($request->my_posts){
            // 11/24　追記　「自分の投稿」というボタンが押された時
            // posts.blade.php 38行目<input type="submit" name="my_posts" class="btn btn-secondary btnx-indigo category_btn" value="自分の投稿" form="postSearchRequest">
            $posts = Post::with('user', 'postComments')
            ->where('user_id', Auth::id())->get();
        }
        return view('authenticated.bulletinboard.posts', compact('posts', 'categories', 'like', 'post_comment'));
    }

    public function postDetail($post_id){
        $post = Post::with('user', 'postComments')->findOrFail($post_id);
        return view('authenticated.bulletinboard.post_detail', compact('post'));
    }

    public function postInput(){
        $main_categories = MainCategory::get();
        return view('authenticated.bulletinboard.post_create', compact('main_categories'));
    }

    public function postCreate(PostFormRequest $request){
        // dd($request);
        $post = Post::create([
            'user_id' => Auth::id(),
            'post_title' => $request->post_title,
            'post' => $request->post_body
        ]);
        $sub_category = $request->post_category_id;
        $post->subCategories()->attach($sub_category);
        return redirect()->route('post.show');
    }

    public function postEdit(PostEditFormRequest $request){
        Post::where('id', $request->post_id)->update([
            'post_title' => $request->post_title,
            'post' => $request->post_body,
        ]);
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function postDelete($id){
        Post::findOrFail($id)->delete();
        return redirect()->route('post.show');
    }

    // カテゴリー
    public function mainCategoryCreate(MainCategoryFormRequest $request){
        MainCategory::create(['main_category' => $request->main_category_name]);
        return redirect()->route('post.input');
    }

    public function subCategoryCreate(SubCategoryFormRequest $request){
        // dd($request);
        SubCategory::create([
            'sub_category' => $request->sub_category_name,
            'main_category_id' => $request->main_category_id,
        ]);
        return redirect()->route('post.input');
    }

    // コメント
    public function commentCreate(PostCommentFormRequest $request){
        PostComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function myBulletinBoard(){
        $posts = Auth::user()->posts()->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_myself', compact('posts', 'like'));
    }

    public function likeBulletinBoard(){
        $like_post_id = Like::with('users')->where('like_user_id', Auth::id())->get('like_post_id')->toArray();
        $posts = Post::with('user')->whereIn('id', $like_post_id)->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_like', compact('posts', 'like'));
    }

    public function postLike(Request $request){
        $user_id = Auth::id();
        $post_id = $request->post_id;

        $like = new Like;

        $like->like_user_id = $user_id;
        $like->like_post_id = $post_id;
        $like->save();

        return response()->json();
    }

    public function postLike_count(Request $request){

        $data = ['like_counts' => $like_counts,];

        return view('post_like' , $data);
    }


    public function postUnLike(Request $request){
        $user_id = Auth::id();
        $post_id = $request->post_id;

        $like = new Like;

        $like->where('like_user_id', $user_id)
            ->where('like_post_id', $post_id)
            ->delete();

        return response()->json();
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['guest']], function(){
    Route::namespace('Auth')->group(function(){
        Route::get('/register', 'RegisterController@registerView')->name('registerView');
        Route::post('/register/post', 'RegisterController@registerPost')->name('registerPost');
        Route::get('/login', 'LoginController@loginView')->name('loginView');
        Route::post('/login/post', 'LoginController@loginPost')->name('loginPost');
    });
});

Route::group(['middleware' => 'auth'], function(){
    Route::namespace('Authenticated')->group(function(){
        Route::namespace('Top')->group(function(){
            Route::get('/logout', 'TopsController@logout');
            Route::get('/top', 'TopsController@show')->name('top.show');
        });
        Route::namespace('Calendar')->group(function(){
            Route::namespace('General')->group(function(){
                Route::get('/calendar/{user_id}', 'CalendarsController@show')->name('calendar.general.show');
                Route::post('/reserve/calendar', 'CalendarsController@reserve')->name('reserveParts');
                Route::post('/delete/calendar', 'CalendarsController@delete')->name('deleteParts');
            });
            Route::namespace('Admin')->group(function(){
                Route::get('/calendar/{user_id}/admin', 'CalendarsController@show')->name('calendar.admin.show');
                Route::get('/calendar/{date}/{part}', 'CalendarsController@reserveDetail')->name('calendar.admin.detail');
                Route::get('/setting/{user_id}/admin', 'CalendarsController@reserveSettings')->name('calendar.admin.setting');
                Route::post('/setting/update/admin', 'CalendarsController@updateSettings')->name('calendar.admin.update');
            });
        });
        Route::namespace('BulletinBoard')->group(function(){
            Route::get('/bulletin_board/posts/{keyword?}', 'PostsController@show')->name('post.show');
            Route::get('/bulletin_board/input', 'PostsController@postInput')->name('post.input');
            Route::get('/bulletin_board/like', 'PostsController@likeBulletinBoard')->name('like.bulletin.board');
            Route::get('/bulletin_board/my_post', 'PostsController@myBulletinBoard')->name('my.bulletin.board');
            Route::post('/bulletin_board/create', 'PostsController@postCreate')->name('post.create');
            Route::post('/create/main_category', 'PostsController@mainCategoryCreate')->name('main.category.create');
            Route::post('/create/sub_category', 'PostsController@subCategoryCreate')->name('sub.category.create');
            Route::get('/bulletin_board/post/{id}', 'PostsController@postDetail')->name('post.detail');
            Route::post('/bulletin_board/edit', 'PostsController@postEdit')->name('post.edit');
            Route::get('/bulletin_board/delete/{id}', 'PostsController@postDelete')->name('post.delete');
            Route::post('/comment/create', 'PostsController@commentCreate')->name('comment.create');
            Route::post('/like/post/{id}', 'PostsController@postLike')->name('post.like');
            Route::post('/unlike/post/{id}', 'PostsController@postUnLike')->name('post.unlike');
        });
        Route::namespace('Users')->group(function(){
            Route::get('/show/users', 'UsersController@showUsers')->name('user.show');
            Route::get('/user/profile/{id}', 'UsersController@userProfile')->name('user.profile');
            Route::post('/user/profile/edit', 'UsersController@userEdit')->name('user.edit');
        });
    });
});

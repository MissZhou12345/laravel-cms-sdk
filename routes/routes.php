<?php

Route::post('comment', ['namespace' => 'QuickCms', 'middleware' => ['api', 'rate'], 'uses' => '\QuickCms\SDK\Https\Controllers\CommentController@store']);
Route::post('comment/{reply_id}/reply', ['namespace' => 'QuickCms', 'middleware' => ['api'], 'uses' => '\QuickCms\SDK\Https\Controllers\CommentController@reply'])->where(['reply_id' => '[0-9]+']);
Route::get("/comment/list", function () {
    echo \QuickCms\SDK\Blade\Comment\Comment::show(request('link_id'), request('comment_key'), request('tpl'), request('avgs'));
});
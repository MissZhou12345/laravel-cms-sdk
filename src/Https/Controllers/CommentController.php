<?php

namespace QuickCms\SDK\Https\Controllers;

use QuickCms\SDK\CommentService;
use QuickCms\SDK\Requests\Comment\ReplyRequest;
use QuickCms\SDK\Requests\Comment\StoreRequest;

class CommentController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    /**
     * @param StoreRequest $request
     * @param CommentService $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, CommentService $comment)
    {
        $data = $request->all();

        if (!auth()->check()) {
            return $this->toFailure('请登陆之后再评论!!');
        }

        if ($this->filter($data['content'])) {
            return $this->toFailure('文明用语!和气生财哦!!');
        }


        $user = auth()->user();

        $data['operator_user_id'] = auth()->id();
        $data['operator_nickname'] = $user ? (isset($user->nickname) ? (string)$user->nickname : $user->name) : '';

        $ret = $comment->store($data);
        if (!$ret) {
            return $this->toFailure('操作失败!请稍后再试');
        }

        $ret->pic = isset($user->avatar) ? (string)$user->avatar : null;

        return $this->toSucess($ret);
    }

    /**
     * @param ReplyRequest $request
     * @param CommentService $comment
     * @param int $reply_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reply(ReplyRequest $request, CommentService $comment, int $reply_id)
    {
        $data = $request->all();

        if (!auth()->check()) {
            return $this->toFailure('请登陆之后再评论!!');
        }

        if ($this->filter($data['content'])) {
            return $this->toFailure('文明用语!和气生财哦!!');
        }

        $user = auth()->user();

        $data['operator_user_id'] = auth()->id();
        $data['operator_nickname'] = isset($user->nickname) ? (string)$user->nickname : $user->name;

        $ret = $comment->replay($reply_id, $data);
        if (!$ret) {
            return $this->toFailure('操作失败!请稍后再试');
        }

        $ret->pic = isset($user->avatar) ? (string)$user->avatar : null;

        return $this->toSucess($ret);
    }

    /**
     * @param $content
     * @return array
     */
    private function filter($content)
    {
        return array_filter((array)config('sys.filter_string'), function ($item) use ($content) {
            return strpos($content, $item) === false ? false : true;
        });
    }


}
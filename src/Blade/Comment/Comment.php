<?php

namespace QuickCms\SDK\Blade\Comment;

/**
 * 使用是传入的参数，"page为禁用字段"
 *
 * Class Meta
 * @package App\Services\Tpl\Cms
 */
class Comment
{
    public static function render($dataSourceView, $avgs = [])
    {
        $dataSourceView = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . $dataSourceView;

        view()->addNamespace("Comment", dirname($dataSourceView));

        return view('Comment' . '::' . basename($dataSourceView), $avgs);
    }

    public static function data($contentId, $commentKey)
    {
        return <<< EOT
        <?php
        
        ?>
EOT;
    }

    /**
     * @param $contentId
     * @param $commentKey
     * @param $tpl
     * @param array $avgs
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function show($contentId, $commentKey, $tpl, $avgs = [])
    {
        $contentId = isset($contentId) ? intval($contentId) : 0;
        $commentKey = isset($commentKey) ? trim($commentKey) : '';

        $pageSize = isset($avgs['pageSize']) ? $avgs['pageSize'] : 5;
        $sourceTitle = isset($avgs['title']) ? $avgs['title'] : '';
        // 获取子回复数量
        $childNum = isset($avgs['childNum']) ? $avgs['childNum'] : 5;
        // 可输入字符数量
        $limit = isset($avgs['limit']) ? $avgs['limit'] : 200;
        // 是否需要登陆
        $needLogin = isset($avgs['needLogin']) ? $avgs['needLogin'] : true;
        // 当前分页数量
        $page = isset($avgs['page']) ? $avgs['page'] : 1;

        $list = app(\QuickCms\SDK\CommentService::class)->rpcSearch(self::getSearch($commentKey, $contentId, $childNum), self::getOrderBy($commentKey), $pageSize, ['*'], $page);

        if (is_array($contentId)) {
            $linkIdStr = implode('_', $contentId);
        } else {
            $linkIdStr = $contentId;
        }
        $urlAvgs = [];
        if ($avgs) {
            foreach ($avgs as $k => $v) {
                $urlAvgs[$k] = urlencode($v);
            }
        }

        $_user = auth()->user();
        $_addUrl = '/comment';

        return self::render($tpl . '.show', compact('_user', 'list', 'commentKey', 'contentId', 'linkIdStr', 'tpl', 'sourceTitle', 'avgs', 'urlAvgs', 'limit', '_addUrl', 'needLogin'));
    }


    /**
     * @param $commentKey
     * @param $contentId
     * @param $childNum
     * @return array
     */
    private static function getSearch($commentKey, $contentId, $childNum)
    {
//        dd([
//            'comment_key' => $commentKey,
//            'link_id' => $contentId,
//            'child_num' => $childNum,
//            'reply_id' => 0,
//            'status' => 0,
//            'is_admin' => true,
//        ]);
        return [
            'comment_key' => $commentKey,
            'link_id' => $contentId,
            'child_num' => $childNum,
            'reply_id' => 0,
            'status' => 0,
            'is_admin' => true,
        ];
    }

    /**
     * @param $commentKey
     * @return array
     */
    private static function getOrderBy($commentKey)
    {

        $orderby = ['created_at' => 'desc'];
        switch ($commentKey) {
            case "cms_comment":
                break;
            case "shop_commodity":
                $orderby = ['score' => 'desc', 'created_at' => 'desc'];
                break;
        }

        return $orderby;
    }


}
<?php

namespace QuickCms\SDK\Https\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use QuickCms\SDK\ArticleService;
use SimpleShop\Commons\Utils\ReturnJson;

class ArticleController extends BaseController
{
    public function getLists(Request $request, ArticleService $articleService)
    {
        $params = ['system' => 'www_aojia','cate_id' => 122];
        $sort   = ['id' => 'DESC'];
        $ret = $articleService->search($params, $sort);
        return ReturnJson::success($ret);
    }
}
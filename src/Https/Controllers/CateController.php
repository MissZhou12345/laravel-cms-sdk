<?php

namespace QuickCms\SDK\Https\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use QuickCms\SDK\CateService;
use SimpleShop\Commons\Utils\ReturnJson;

class CateController extends BaseController
{
    public function getLists(Request $request, CateService $cateService)
    {
        $params = ['system' => 'www_aojia'];
        $sort   = ['sort' => 'DESC'];
        $ret = $cateService->search($params, $sort);
        return ReturnJson::success($ret);
    }
}
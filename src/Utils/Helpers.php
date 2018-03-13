<?php
namespace QuickCms\SDK\Utils;

use Illuminate\Support\Facades\Request;

class Helpers
{
    
    public static function buildUrl($baseUrl, $params)
    {
        return $baseUrl.'?'.http_build_query($params);
    }

    public static function getCommonParams()
    {
        $params = [];
        foreach (['cli_v'] as $key) {
            if (Request::has($key)) {
                $params[$key] = Request::input($key);
            }
        }
        return $params;
    }

}

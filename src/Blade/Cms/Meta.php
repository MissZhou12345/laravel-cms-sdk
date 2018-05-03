<?php

namespace QuickCms\SDK\Blade\Cms;

use Illuminate\Support\Facades\Cache;


/**
 *
 * Class Meta
 * @package App\Services\Tpl\Cms
 */
class Meta
{
    public static function data($templetId, $params)
    {
        $cacheKey = 'seo_meta' . md5(json_encode([
                'system' => config('sys.website_key'),
                'templetId' => $templetId,
                'params' => $params
            ]));

        $metaInfo = Cache::get($cacheKey);
        if (!$metaInfo) {
            // 获取模板信息
            $metaInfo = app(\QuickCms\SDK\MetaService::class)->getMetaByTempletId($templetId);
            Cache::put($cacheKey, $metaInfo, 120);
        }

        // 初始化
        $_pageSEO = [
            'title' => isset($metaInfo->title) ? $metaInfo->title : '',
            'keywords' => isset($metaInfo->keywords) ? $metaInfo->keywords : '',
            'description' => isset($metaInfo->description) ? $metaInfo->description : '',
        ];

        try {
            $seo_title = $_pageSEO['title'];
            $seo_keywords = $_pageSEO['keywords'];
            $seo_description = $_pageSEO['description'];

            //拆分变量
            extract($params);
            // 拆分二层
            extract($title);
            extract($keywords);
            extract($description);

            eval("\$str_title = \"$seo_title\";");
            eval("\$str_keywords = \"$seo_keywords\";");
            eval("\$str_description = \"$seo_description\";");

            $_pageSEO['title'] = $str_title;
            $_pageSEO['keywords'] = $str_keywords;
            $_pageSEO['description'] = $str_description;

        } catch (\Exception $e) {

        }

        return $_pageSEO;
    }


    /**
     * @param $templetId
     * @param array $params
     * @return string
     */
    public static function html(string $templetId, array $params = [])
    {
        $templetId = self::_getTempletId($templetId);
        $_pageSEO = static::data($templetId, $params);

        $_pageSEO['title'] = isset($_pageSEO['title']) && $_pageSEO['title'] ? $_pageSEO['title'] : '安达人官网';
        $_pageSEO['keywords'] = isset($_pageSEO['keywords']) && $_pageSEO['keywords'] ? $_pageSEO['keywords'] : '安达人官网';
        $_pageSEO['description'] = isset($_pageSEO['description']) && $_pageSEO['description'] ? $_pageSEO['description'] : '安达人官网';

        $html = <<<EOT
            <meta http-equiv="content-type" charset="text/html;charset=utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=Edge" >
            <meta content="always" name="referrer">
            <title>{$_pageSEO['title']}</title>
            <meta name="keywords" content="{$_pageSEO['keywords']}">
            <meta name="description" content="{$_pageSEO['description']}">
EOT;

        echo $html;
    }

    /**
     * @param $params
     * @return array
     */
    private static function _getTempletId($templetId)
    {
        $url = request()->getRequestUri();
        return trim($templetId) ? $templetId : $url;
    }

}
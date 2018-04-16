<?php

namespace QuickCms\SDK\Blade\Cms;

use SimpleShop\Commons\Exceptions\Exception;

/**
 * 使用是传入的参数，"page为禁用字段"
 *
 * Class Meta
 * @package App\Services\Tpl\Cms
 */
class Meta
{

    public static function data($templetId, $params)
    {
        $url = isset($templetId[0]) ? $templetId[0] : '';
        $path = isset($templetId[1]) ? $templetId[1] : '';
        $params = json_encode($params);

        return <<< EOT
        <?php
            \$_params = json_decode('$params',true);
            //带动态参数
//            \$_pageSEO = \Cache::get('web_seo_meta' . md5('$url'));
//            //不带动态参数
//            if(!\$_pageSEO){
//                \$_pageSEO = \Cache::get('web_seo_meta' . md5('$path'));
//            }

            //无缓存查数据库
//            if(!\$_pageSEO) {
                \$_pageSEO = app(QuickCms\SDK\MetaService::class)->getMeta('$url','$path');
//            }

            \$_pageSEO = \$_pageSEO ? (array)\$_pageSEO : ['title' => '', 'keywords' => '', 'description' => ''];
            //拆分变量
            extract(\$_params);

            try {
                \$seo_title = \$_pageSEO['title'];
                \$seo_keywords = \$_pageSEO['keywords'];
                \$seo_description = \$_pageSEO['description'];
                
                eval("\\\$str_title = \"\$seo_title\";");
                eval("\\\$str_keywords = \"\$seo_keywords\";");
                eval("\\\$str_description = \"\$seo_description\";");

                \$_pageSEO['title'] = \$str_title;
                \$_pageSEO['keywords'] = \$str_keywords;
                \$_pageSEO['description'] = \$str_description;
            } catch (\Exception \$e) {
                //发送邮件到管理员
                dd(\$e);
            }
                  
        ?>
EOT;
    }

    public static function html($params)
    {
        // 解析成数组
        $bool = $params = json_decode($params, true);
        if (!$bool) {
            throw new Exception('传入的参数不是json字符串');
        }

        $templetId = self::_getTempletId($params);
        unset($params['page']);

        $html = <<< EOT
            <meta http-equiv="content-type" charset="text/html;charset=utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=Edge" >
            <meta content="always" name="referrer">
            <title><?php
                if(isset(\$_pageSEO['title']) && \$_pageSEO['title']){
                    echo \$_pageSEO['title'];
                }else{
                    echo "蚂蚁木匠";
                }
                ?></title>
            <meta name="keywords" content="<?php
            if(isset(\$_pageSEO['keywords']) && \$_pageSEO['keywords']){
                echo \$_pageSEO['keywords'];
            }else{
                echo '蚂蚁木匠';
            }
            ?>">
            <meta name="description" content="<?php
            if(isset(\$_pageSEO['description']) && \$_pageSEO['description']){
                echo \$_pageSEO['description'];
            }else{
                echo '蚂蚁木匠';
            }
            ?>">
EOT;
        return static::data($templetId, $params) . PHP_EOL . $html;
    }

    /**
     * @param $params
     * @return array
     */
    private static function _getTempletId($params)
    {
        $url = request()->getRequestUri();
        $path = request()->path();
        return isset($params['page']) && $params['page'] ? [$params['page']] : [$url, $path];
    }

}
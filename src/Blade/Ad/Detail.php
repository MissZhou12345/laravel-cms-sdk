<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2018/2/27
 * Time: 14:28
 */

namespace QuickCms\SDK\Blade\Ad;


use SimpleShop\Commons\Exceptions\Exception;

class Detail
{
    public static function data($id)
    {
        return <<< EOT
        <?php
            \$adService = app(QuickCms\SDK\AdService::class);
            \$ad = \$adService->getDetail($id);
        ?>
EOT;
    }

    public static function html($params)
    {
        // 解析成数组
        $bool = $params = json_decode($params, true);

        if (! $bool) {
            throw new Exception('传入的参数不是json字符串');
        }

        $height = isset($params['height']) ? $params['height'] : 400;
        $wide = isset($params['wide']) ? $params['wide'] : 400;
        $html = <<< EOT
        <a href="<?php echo \$ad->url?>" target="<?php echo \$ad->target?>" class="img"><img
                                    src="<?php echo \$ad->info ?>?x-oss-process=image/resize,w_<?php echo $wide?>,h_<?php echo $height?>"
                                    alt="<?php echo \$ad->description?>"></a>
EOT;

        return static::data($params['id']) . PHP_EOL . $html;
    }
}
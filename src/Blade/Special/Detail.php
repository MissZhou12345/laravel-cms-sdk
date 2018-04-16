<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2018/2/8
 * Time: 16:27
 */

namespace QuickCms\SDK\Blade\Special;


use QuickCms\SDK\SpecialService;

class Detail
{
    public static function data($id)
    {
         return <<< EOT
            <?php
                \$key = $id;
            ?>
EOT;
    }

    public static function html($id)
    {
        $html = <<< 'EOT'
                <div class="stock_cent">
        <iframe id="iframe" src="/cms/special/<?php echo $key?>/<?php echo config('sys.website_key')?>" frameborder="0"
                scrolling="no" onload="iframeLoad()"></iframe>
    </div>
    <script>
        function iframeLoad() {
            document.getElementById("iframe").height=0;
            document.getElementById("iframe").height=document.getElementById("iframe").contentWindow.document.body.scrollHeight;
            document.getElementById("iframe").width="100%";
            // document.getElementById("iframe").width=document.getElementById("iframe").contentWindow.document.body.scrollWidth;
        }
    </script>
EOT;
        return static::data($id) . PHP_EOL . $html;
    }
}
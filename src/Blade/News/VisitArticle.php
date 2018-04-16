<?php
namespace QuickCms\SDK\Blade\News;

class VisitArticle
{

    public static function html($id)
    {
        $html = <<<'EOT'
        
EOT;
        return static::data($id) . PHP_EOL . $html;
    }

    /**
     * @return string
     */
    public static function data($id)
    {
        return <<<'EOT'
        <?php
            $article = \App::make('QuickCms\SDK\ArticleService');
            $article->updateIncrement($id);
        ?>
EOT;
    }

}
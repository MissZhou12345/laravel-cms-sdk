<?php
namespace QuickCms\SDK\Blade\News;

class Hot
{
    public static function html()
    {
        $html = <<<'EOT'
        
        <div class="side_des">
            <h3 class="tab_h3"><i class="bar"></i>热点排行榜</h3>
            <?php $__currentLoopData = $articleLists->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="side_new">
                    <a class="news" href="{{ route('web.news.detail', ['id' => $item->id]) }}"><i class="tip">●</i><span>{{ $item->_title }}</span></a>
                    <div class="intr on">
                        <p class="txt">{{ str_limit($item->abstract, 80) }}</p>
                        <p class="footer clear"><span class="fl">{{ round((time()-strtotime($item->pub_time))/60/60) }}小时前</span><a class="txt fr"
                                                                                                                                 href="{{ route('web.news.detail', ['id' => $item->id]) }}">查看详情<span>&gt;</span></a></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
EOT;
        return static::data() . PHP_EOL . $html;
    }

    /**
     * @return string
     */
    public static function data()
    {
        return <<<'EOT'
        <?php
            $search = ['status' => 1, 'model_id' => 'news'];
            $sort   = ['page_view' => 'DESC'];
            $pageSize = 10;
            $article = \App::make('QuickCms\SDK\ArticleService');
            $articleLists = $article->search($search, $sort,$pageSize);
        ?>
EOT;
    }
}
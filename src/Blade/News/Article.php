<?php
namespace QuickCms\SDK\Blade\News;

class Article
{
    public static function html()
    {
        $html = <<<'EOT'
        <?php $__currentLoopData = $articleLists->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="zixun_txt clear">
            <a class="fl_img fl" href="{{ route('web.news.detail', ['id' => $item->id]) }}">
                <img src="{{ $item->cover_path }}?x-oss-process=image/resize,m_fixed,h_200,w_270" alt="" title="">
            </a>
            <div class="cont fr">
                <a href="{{ route('web.news.detail', ['id' => $item->id]) }}">
                    <h3>{{ $item->_title }}</h3>
                </a>
                <p>{{ str_limit($item->abstract, 300) }}</p>
                <div class="footer clear">
                    <p class="fl"><span><i class="iconfont icon-tag-copy"></i></span><span>{{ $item->pub_time }}</span>
                    </p>
                </div>
            </div>
            <div class="ov_h"></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
EOT;
        //$html = '';
        return static::data() . PHP_EOL . $html;
    }

    /**
     * @return string
     */
    public static function data()
    {
        return <<<'EOT'
        <?php
            $cateKey = request()->route()->parameter('cate', null);
            $page = request()->input('page',1);
            $pageSize = 25;
            $columns = ['*'];
            
            if (is_null($cateKey)) {
                $search = ['system' => 'www_mayimujiang','cate_pid' => 10000];
                $sort   = ['likes' => 'DESC'];
                $article = \App::make('QuickCms\SDK\ArticleService');
                $articleLists = $article->search($search, $sort, $pageSize, $columns, $page);
            } else {
                $cate = \App::make('QuickCms\SDK\CateService');
                $cateDetail = $cate->detail($cateKey,'www_mayimujiang');
                $search = ['system' => 'www_mayimujiang','cate_id' => $cateDetail->id];
                $sort   = ['id' => 'ASC'];
                $article = \App::make('QuickCms\SDK\ArticleService');
                $articleLists = $article->search($search, $sort, $pageSize, $columns, $page);
            }
        ?>
EOT;
    }
}
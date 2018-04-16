<?php
namespace App\Services\Blade\News;

class Cate
{
    public static function html()
    {
        $html = <<<'EOT'
        <ul class="zixun_nav clear">
        <li <?php if(is_null($cateKey)): ?> class="on"<?php endif;?>>
            <a href="{{ route('web.news.index') }}">推荐</a>
        </li>
        <?php $__currentLoopData = $cateLists->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li <?php if($cateKey == $item->key): ?>  class="on" <?php endif;?>>
                <a href="{{ route('web.news.cate', ['cateKey' => $item->key]) }}">{{ $item->name }}</a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
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
            $cateKey = request()->route()->parameter('cate', null);
            $search = ['system' => 'www_mayimujiang','pid' => 10000];
            $sort   = ['sort' => 'ASC'];
            $cate = \App::make('QuickCms\SDK\CateService');
            $cateLists = $cate->search($search, $sort);
        ?>
EOT;
    }
}
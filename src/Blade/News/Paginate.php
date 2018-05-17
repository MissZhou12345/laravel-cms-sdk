<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2018/2/1
 * Time: 10:48
 */

namespace QuickCms\SDK\Blade\News;


use Illuminate\Pagination\LengthAwarePaginator;

class Paginate
{
    /**
     * @return string
     */
    public static function html($expression)
    {
        $html = <<<'EOT'

            <?php if(is_null($__articleLists->prev_page_url)): ?>
                <a href="javascript:" class="page">&lt;</a>
            <?php else: ?>
                <a href="?page={{$__articleLists->current_page-1}}" class="page">&lt;</a>
            <?php endif; ?>
            
                    <?php for($i=1;$i<=$__articleLists->last_page;$i++):?>
                        <?php if($i == $__articleLists->current_page):?>
                            <a href="javascript:" class="ayes">{{ $i }}</a>
                        <?php else: ?>
                            <a href="?page={{$i}}">{{ $i }}</a>
                        <?php endif; ?>
                    <?php endfor;?>
                    
            <?php if(is_null($__articleLists->next_page_url)): ?>
                <a href="javascript:" class="down">&gt;</a>
            <?php else: ?>
                <a href="?page={{$__articleLists->current_page+1}}" class="down">&gt;</a>
            <?php endif; ?>
EOT;
        //$html = '';
        return static::data($expression) . PHP_EOL . $html;
    }

    /**
     * @return string
     */
    public static function data($expression)
    {
        $bool = $params = json_decode($expression, true);
        if (!$bool) {
            throw new \Exception('[paginate_news]传入的参数不是json字符串');
        }

        $modelKey = isset($params['modelKey']) ? $params['modelKey'] : '';
        $cateKey = isset($params['cateKey']) ? $params['cateKey'] : '';
        $limit = intval($params['limit']) ? intval($params['limit']) : 20;

        $cacheKey = 'cmsSdk' . 'PaginateNews' . md5(json_encode([
                'system' => config('sys.website_key'),
                'search' => $expression,
                'page' => request()->input('page', 1),
            ]));
        $minutes = config('sys.cms_sdk_cache_time');

        return <<< EOT
            <?php
             \$param=request()->route()->parameters();
             \$search = \$param?\$param:[];
             \$search['status'] = 1;
             // 默认排序
             \$sort   = ['id' => 'DESC'];
             
             \$page = request()->input('page',1);
             \$pageSize = {$limit};
             \$columns = ['*'];
             
             if('{$modelKey}'){
                \$search['model_id'] = '{$modelKey}';
             }
             if('{$cateKey}' && !isset(\$search['cateKey'])){
                \$search['cateKey'] = '{$cateKey}';
             }
             
             \$__articleLists = \Cache::remember('{$cacheKey}', {$minutes}, function () use(\$search,\$sort,\$columns,\$pageSize,\$page) {
                 \$cateKey = isset(\$search['cateKey'])?\$search['cateKey']:null;
                 if(\$cateKey){
                    // 传入分类key
                    \$search['cate_id'] = \App::make('QuickCms\SDK\CateService')->detail(\$cateKey)->id;
                 }
                 
                 if(!isset(\$search['cate_id'])){
                    // 推荐---的排序
                    \$sort = ['hot' => 'DESC'];
                 }
                 
                 return \App::make('QuickCms\SDK\ArticleService')->search(\$search, \$sort, \$pageSize, \$columns, \$page);
            });
            ?>
EOT;


    }
}
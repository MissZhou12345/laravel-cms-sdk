<?php
/**
 *------------------------------------------------------
 * SdkServiceProvider.php
 *------------------------------------------------------
 *
 * @author    rongzhenbang@liweijia.com
 * @version   V1.0
 *
 */

namespace QuickCms\SDK;

use QuickCms\SDK\Blade\Ad\Detail as AdDetail;
use QuickCms\SDK\Blade\Cms\Meta as CmsMeta;
use QuickCms\SDK\Blade\Cms\Position as CmsPosition;
use QuickCms\SDK\Blade\Special\Detail as SpecialDetail;
use QuickCms\SDK\Blade\Special\Detail;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Application as LaravelApplication;
use Blade;
use QuickCms\SDK\Blade\News\Cate;
use QuickCms\SDK\Blade\News\Article;
use QuickCms\SDK\Blade\News\ArticleDetail;
use QuickCms\SDK\Blade\News\VisitArticle;
use QuickCms\SDK\Blade\News\Paginate;
use QuickCms\SDK\Blade\News\Hot;
use SimpleShop\Commons\Exceptions\Exception;
use QuickCms\SDK\Blade\ArticleExtension;
use BitPress\BladeExtension\Container\BladeRegistrar;
use BitPress\BladeExtension\Exceptions\InvalidBladeExtension;
use BitPress\BladeExtension\Contracts\BladeExtension;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/../routes/routes.php';
        }

        $this->addMiddlewareAlias('rate', \QuickCms\SDK\Https\Middleware\RateMiddleware::class);

        $this->setupConfig($this->app);
        //$this->setupMigrations($this->app);
        $this->bootCateList();
        $this->bootArticleList();
        $this->bootArticleDetail();
        $this->bootSpecial();
        $this->bootAd();
        $this->bootAdList();
        $this->bootPaginate();
        $this->bootHotList();
        $this->bootPosition();
        $this->bootSheJiJia();
        $this->bootBladeExtension();
        $this->bootVisitArticle();
    }


    /**
     * Register a short-hand name for a middleware. For compatibility
     * with Laravel < 5.4 check if aliasMiddleware exists since this
     * method has been renamed.
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    protected function addMiddlewareAlias($name, $class)
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }


    public function bootBladeExtension()
    {
        foreach ($this->app->tagged('blade.extension') as $extension) {
            if (!$extension instanceof BladeExtension) {
                throw new InvalidBladeExtension($extension);
            }

            foreach ($extension->getDirectives() as $name => $callable) {
                $this->app['blade.compiler']->directive($name, $callable);
            }

            foreach ($extension->getConditionals() as $name => $callable) {
                $this->app['blade.compiler']->if($name, $callable);
            }
        }
    }

    public function bootVisitArticle()
    {
        Blade::directive('visit_article', function ($expression) {
            return VisitArticle::html($expression);
        });
    }

    /**
     * 初始化配置
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    protected function setupConfig(Application $app)
    {
        $source = realpath(__DIR__ . '/../config/config.php');

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => config_path('sys.php')]);
        } elseif ($app instanceof LumenApplication) {
            $app->configure('config');
        }

        $this->mergeConfigFrom($source, 'sys');
    }

    /**
     * 初始化数据库
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    protected function setupMigrations(Application $app)
    {
        $source = realpath(__DIR__ . '/../database/migrations/');

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => database_path('migrations')], 'migrations');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        BladeRegistrar::register(ArticleExtension::class, function () {
            return new ArticleExtension();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * 设计家相关的
     * ----风格
     */
    public function bootSheJiJia()
    {
        //------------------------------获取风格----------------------------------------
        Blade::directive('ShejijiaStyle', function ($expression) {
            // 解析成数组
            $bool = $params = json_decode($expression, true);
            if (!$bool) {
                throw new Exception('【ShejijiaStyle】传入的参数不是json字符串');
            }

            if (!isset($params['model_id'])) {
                throw new Exception('【model_id】参数未传入.');
            }
            if (!isset($params['attr_key'])) {
                throw new Exception('【attr_key】参数未传入.');
            }

            $paramsModelId = isset($params['model_id']) ? $params['model_id'] : '';
            $paramsAttrKey = isset($params['attr_key']) ? $params['attr_key'] : '';

            $cacheKey = 'cmsSdk' . 'ShejijiaStyle' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => $expression,
                ]));
            $minutes = config('sys.cms_sdk_cache_time');

            return <<< EOT
            <?php
            \$__cateCaseStyleLists = \Cache::remember('{$cacheKey}', {$minutes}, function () {
            
                \$__CaseStyleLists = \App::make('QuickCms\SDK\CateService')->getRoomCaseStyle('{$paramsModelId}', '{$paramsAttrKey}');
                \$__cateCaseStyleLists=[];
                foreach(\$__CaseStyleLists as \$style){
                    \$__cateCaseStyleLists[\$style->value]=\$style;
                }

                return \$__cateCaseStyleLists;
            });
            foreach (\$__cateCaseStyleLists  as \$key=>\$item) :
            ?>
EOT;

        });


        Blade::directive('EndShejijiaStyle', function ($expression) {
            return "<?php endforeach; ?>";
        });
        //------------------------------end获取风格----------------------------------------


        //------------------------------获取楼盘----------------------------------------
        Blade::directive('ShejijiaHouseName', function ($expression) {
            // 解析成数组
            $bool = $params = json_decode($expression, true);
            if (!$bool) {
                throw new Exception('【ShejijiaHouseName】传入的参数不是json字符串');
            }

            if (!isset($params['model_id'])) {
                throw new Exception('【model_id】参数未传入.');
            }
            if (!isset($params['attr_key'])) {
                throw new Exception('【attr_key】参数未传入.');
            }

            $paramsModelId = isset($params['model_id']) ? $params['model_id'] : '';
            $paramsAttrKey = isset($params['attr_key']) ? $params['attr_key'] : '';
            $limit = intval($params['limit']) ? intval($params['limit']) : 20;

            $cacheKey = 'cmsSdk' . 'ShejijiaHouseName' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => $expression,
                ]));
            $minutes = config('sys.cms_sdk_cache_time');

            return <<< EOT
            <?php
            \$__cateHouseNameLists = \Cache::remember('{$cacheKey}', {$minutes}, function () {
                return \App::make('QuickCms\SDK\CateService')->getHouseName('{$paramsModelId}', '{$paramsAttrKey}', {$limit});
            });
            foreach (\$__cateHouseNameLists  as \$key=>\$item) :
            ?>
EOT;

        });


        Blade::directive('EndShejijiaHouseName', function ($expression) {
            return "<?php endforeach; ?>";
        });
        //------------------------------end获取楼盘----------------------------------------

    }


    public function bootCateList()
    {
        Blade::directive('Cate', function ($expression) {
            // 解析成数组
            $bool = $params = json_decode($expression, true);
            if (!$bool) {
                throw new Exception('[Cate]传入的参数不是json字符串');
            }

            $paramsCateKey = isset($params['cate_key']) ? $params['cate_key'] : '';
            $limit = intval($params['limit']) ? intval($params['limit']) : 20;

            $cacheKey = 'cmsSdk' . 'CateList' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => $expression,
                ]));
            $minutes = config('sys.cms_sdk_cache_time');

            return <<< EOT
            <?php
            \$search = [];
            \$search['display'] = 1;
            
            \$page = request()->input('page',1);
            \$pageSize = {$limit};
            \$columns = ['*'];
            \$sort   = ['sort' => 'DESC'];
            
            \$__cateaLists = \Cache::remember('{$cacheKey}', {$minutes}, function () use(\$search, \$sort, \$pageSize, \$columns) {
                return \App::make(\QuickCms\SDK\CateService::class)->searchByKey('{$paramsCateKey}', \$search, \$sort, \$pageSize, \$columns);
            });
            
            foreach (\$__cateaLists->data  as \$key=>\$item) :
            ?>
EOT;

        });
        Blade::directive('EndCate', function ($expression) {
            return "<?php endforeach; ?>";
        });
    }

    public function bootArticleDetail()
    {
        Blade::directive('ArticleDetail', function ($expression) {
            return ArticleDetail::html($expression);
        });
    }

    /**
     *  cate_id
     *  cateKey优先级大于cate_id
     *  case_style
     *  house_name
     *  model_id
     */
    public function bootArticleList()
    {
        Blade::directive('ArticleList', function ($expression) {
            // 解析成数组
            $bool = $params = json_decode($expression, true);
            if (!$bool) {
                throw new Exception('[ArticleList]传入的参数不是json字符串');
            }

            $modelKey = isset($params['modelKey']) ? $params['modelKey'] : '';
            $cateKey = isset($params['cateKey']) ? $params['cateKey'] : '';
            $limit = intval($params['limit']) ? intval($params['limit']) : 20;

            $minutes = config('sys.cms_sdk_cache_time');

            return <<<EOT
            <?php
             \$param=request()->route()->parameters();
             \$search = \$param?\$param:[];
             \$search['status'] = 1;
             // 默认排序
             \$sort   = ['id' => 'DESC'];
             if('\$sort' == 'hot'){
                \$sort   = ['hot' => 'DESC'];
             }
             
             \$page = request()->input('page',1);
             \$pageSize = {$limit};
             \$columns = ['*'];
             
             if('{$modelKey}'){
                \$search['model_id'] = '{$modelKey}';
             }
             if('{$cateKey}' && !isset(\$search['cateKey'])){
                \$search['cateKey'] = '{$cateKey}';
             }
             
             
            \$cacheKey = 'cmsSdk' . 'ArticleList' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => json_encode(\$search),
                    'page' => request()->input('page', 1),
                ]));
             
             \$__articleLists = \Cache::remember(\$cacheKey, {$minutes}, function () use(\$search,\$sort,\$columns,\$pageSize,\$page) {
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
             
             foreach (\$__articleLists->data  as \$key=>\$item) :
            ?>
EOT;

        });


        Blade::directive('EndArticleList', function ($expression) {
            return "<?php endforeach; ?>";
        });
    }

    public function bootPaginate()
    {
        Blade::directive('paginate_news', function ($expression) {
            return Paginate::html($expression);
        });
    }


    public function bootSpecial()
    {
        Blade::directive('special', function ($expression) {
            return Detail::html($expression);
        });
    }

    public function bootHotList()
    {
        Blade::directive('HotList', function ($expression) {
            // 解析成数组
            $bool = $params = json_decode($expression, true);
            if (!$bool) {
                throw new Exception('【HotList】传入的参数不是json字符串.');
            }

            if (!isset($params['model_id'])) {
                throw new Exception('【model_id】参数未传入.');
            }

            $model_id = $params['model_id'];
            $limit = isset($params['limit']) ? intval($params['limit']) : 5;

            $cacheKey = 'cmsSdk' . 'HotList' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => $expression,
                ]));
            $minutes = config('sys.cms_sdk_cache_time');

            return <<< EOT
            <?php
            \$search = ['status' => 1, 'model_id' => '{$model_id}'];
            \$sort   = ['page_view' => 'DESC'];
            \$columns = ['*'];
            \$pageSize = {$limit};
            
            \$__articleHotLists = \Cache::remember('{$cacheKey}', {$minutes}, function () use(\$search, \$sort, \$pageSize, \$columns) {
                 return \App::make(\QuickCms\SDK\ArticleService::class)->search(\$search, \$sort, \$pageSize, \$columns, 1);
            });
             
            foreach (\$__articleHotLists->data as \$key=>\$item) :
            ?>
EOT;

        });
        Blade::directive('EndHotList', function ($expression) {
            return "<?php endforeach; ?>";
        });
    }


    public function bootAd()
    {
        Blade::directive('banner', function ($expression) {
            $cacheKey = 'cmsSdk' . 'Banner' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => $expression,
                ]));
            $minutes = config('sys.cms_sdk_cache_time');

            return \Cache::remember($cacheKey, $minutes, function () use ($expression) {
                return AdDetail::html($expression);
            });
        });
    }

    public function bootAdList()
    {
        Blade::directive('BannerList', function ($expression) {
            // 解析成数组
            $bool = $params = json_decode($expression, true);
            if (!$bool) {
                throw new Exception('【BannerList】传入的参数不是json字符串.');
            }

            if (!isset($params['adspace_c_key'])) {
                throw new Exception('【adspace_c_key】参数未传入.');
            }

            $adSpaceKey = $params['adspace_c_key'];
            $system = config('sys.website_key');
            $limit = isset($params['limit']) ? intval($params['limit']) : 5;

            $cacheKey = 'cmsSdk' . 'BannerList' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => $expression,
                ]));
            $minutes = config('sys.cms_sdk_cache_time');

            return <<< EOT
            <?php
            \$__adSpaceList = \Cache::remember('{$cacheKey}', {$minutes}, function () {
                 return app(\QuickCms\SDK\AdService::class)->getList(['adspace_c_key'=>'{$adSpaceKey}','system'=>'{$system}'],['sort'=>'DESC'],{$limit});
            });
             foreach (\$__adSpaceList->data  as \$key=>\$item) :
            ?>
EOT;

        });
        Blade::directive('EndBannerList', function ($expression) {
            return "<?php endforeach; ?>";
        });
    }


    /**
     * cms 推荐位信息
     */
    public function bootPosition()
    {
        Blade::directive('position', function ($expression) {
            // 解析成数组
            $bool = $params = json_decode($expression, true);
            if (!$bool) {
                throw new Exception('【position】传入的参数不是json字符串');
            }

            if (!isset($params['position_key'])) {
                throw new Exception('【position_key】参数未传入.');
            }

            $positionKey = $params['position_key'];
            $limit = intval($params['limit']) ? intval($params['limit']) : 5;

            $cacheKey = 'cmsSdk' . 'PositionList' . md5(json_encode([
                    'system' => config('sys.website_key'),
                    'search' => $expression,
                ]));
            $minutes = config('sys.cms_sdk_cache_time');

            return <<< EOT
            <?php
            \$search = ['key'=>'{$positionKey}'];
            \$sort = ['sort'=>'DESC'];
            \$pageSize = {$limit};
            
            \$__positionList = \Cache::remember('{$cacheKey}', {$minutes}, function () use(\$search,\$sort,\$pageSize) {
                 return \App::make(\QuickCms\SDK\PositionService::class)->searchByKey(\$search,\$sort,\$pageSize);
            });
            
            foreach (\$__positionList->data  as \$key=>\$item) :
            ?>
EOT;


        });
        Blade::directive('endposition', function ($expression) {
            return "<?php endforeach; ?>";
        });
    }


}

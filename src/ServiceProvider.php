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

use App\Services\Tpl\Ad\Detail as AdDetail;
use App\Services\Tpl\Cms\Meta as CmsMeta;
use App\Services\Tpl\Cms\Position as CmsPosition;
use App\Services\Tpl\Special\Detail as SpecialDetail;
use App\Services\Tpl\Special\Detail;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Application as LaravelApplication;
use Blade;
use App\Services\Tpl\News\Cate;
use App\Services\Tpl\News\Article;
use App\Services\Tpl\News\ArticleDetail;
use App\Services\Tpl\News\Paginate;
use App\Services\Tpl\News\Hot;
use SimpleShop\Commons\Exceptions\Exception;

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
        //$this->setupConfig($this->app);
        //$this->setupMigrations($this->app);
        $this->bootCateList();
        $this->bootArticleList();
        $this->bootArticleDetail();
        $this->bootSpecial();
        $this->bootAd();
        $this->bootPaginate();
        $this->bootHotList();
        $this->bootPosition();
        $this->bootMeta();
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

    public function bootCateList()
    {
        Blade::directive('Cate', function ($expression) {
            return Cate::html($expression);
        });
    }

    public function bootArticleList()
    {
        Blade::directive('ArticleList', function ($expression) {
            return Article::html($expression);
        });
    }

    public function bootPaginate()
    {
        Blade::directive('paginate_news', function () {
            return Paginate::html();
        });
    }

    public function bootArticleDetail()
    {
        Blade::directive('ArticleDetail', function ($expression) {
            return ArticleDetail::html($expression);
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
            return Hot::html($expression);
        });
    }


    public function bootAd()
    {
        Blade::directive('banner', function ($expression) {
            return AdDetail::html($expression);
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
            $position_key = intval($params['position_key']);
            $system = intval($params['system']);
            $limit = intval($params['limit']) ? intval($params['limit']) : 5;

            return <<< EOT
            <?php
             \$__positionList = app(\QuickCms\SDK\PositionService::class)->searchByKey(['key'=>{$position_key},'system'=>{$system}],['sort'=>'DESC'],{$limit});
             foreach (\$__positionList->data  as \$key=>\$item) :
            ?>
EOT;
        });
        Blade::directive('endposition', function ($expression) {
            return "<?php endforeach; ?>";
        });
    }

    /**
     * seo META信息
     */
    public function bootMeta()
    {
        Blade::directive('CmsMeta', function ($expression) {
            return CmsMeta::html($expression);
        });
    }


}

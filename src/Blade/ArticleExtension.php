<?php

namespace QuickCms\SDK\Blade;

use BitPress\BladeExtension\Contracts\BladeExtension;

class ArticleExtension implements BladeExtension
{
    public function getDirectives()
    {
        return [
            'ext_article_detail' => [$this, 'getArticleDetail'],
            'ext_visit_article'  => [$this, 'visitArticle'],
        ];
    }

    public function getConditionals()
    {
        return [

        ];
    }

    public function getArticleDetail ($id)
    {
        $article = \App::make('QuickCms\SDK\ArticleService');
        $articleDetail = $article->detail($id);

        $_dataSEO['title']=$articleDetail->_title;

        $cate = \App::make('QuickCms\SDK\CateService');
        $cateDetail = $cate->detail($articleDetail->cate_id);
        $model = \App::make('QuickCms\SDK\ModelService');
        $modelAttrs = $model->getAttrs(['news']);
        foreach ($modelAttrs->news as $val) {
            $attrs[$val->id] = $val->attr_key;
        }
        foreach ($articleDetail->attr_val as $val) {
            $attrsValue[$attrs[$val->attr_id]] = $val;
        }

        $html = <<<EOT
                    <h1>$articleDetail->_title</h1>
                    <div class="zixun_tab clear">
                        <p class="fl">
                            <a class="tag"
                               href="{{ route('web.news.cate', ['cateKey' => '{$cateDetail->key}']) }}">$cateDetail->name</a>
                            <span>发布时间： $articleDetail->pub_time</span>
                            <span>浏览量： $articleDetail->page_view</span>
                        </p>
                    </div>
                    <div class="zixun_lead">
                        <p class="tit">导语</p>
                        <p>{$attrsValue['abstract']->value}</p>
                        <span class="bd"></span>
                    </div>
                    <div class="zixun_info">
                        {$attrsValue['content']->value}
                    </div>
                    <div class="icon_share" ui-share>
                        <span class="fl">分享：</span>
                        <i class="iconfont icon-weibo" data-key="weibo"></i>
                        <i class="iconfont icon-kongjian" data-key="kongjian"></i>
                        <i class="iconfont icon-weixin1" data-key="weixin"></i>
                    </div>
                    <div class="line"></div>
EOT;
        return $html;
    }

    public function visitArticle ($id)
    {
        return $id;
    }

}

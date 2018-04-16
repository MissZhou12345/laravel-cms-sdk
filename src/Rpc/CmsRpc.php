<?php
namespace QuickCms\SDK\Rpc;

use QuickCms\SDK\Utils\Helpers;
use JsonRpc\Client;

class CmsRpc extends Rpc
{

    function baseUrl()
    {
        return config('sys.cms_svr_url');
    }

   public function get($serviceName)
    {
        $rpcUrl = Helpers::buildUrl($this->baseUrl() . '/' . $serviceName, Helpers::getCommonParams());
        return new Client($rpcUrl);
    }

}

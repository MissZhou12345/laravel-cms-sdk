<?php

namespace QuickCms\SDK;

use QuickCms\SDK\Rpc\CmsRpc;

class PositionService
{
    const SERVICE_NAME = 'position';

    private $rpc;

    private $client;

    public function __construct(CmsRpc $rpc)
    {
        $this->rpc = $rpc;
        $this->client = $rpc->get(self::SERVICE_NAME);
    }

    /**
     * @throws \SimpleShop\Commons\RpcException
     */
    public function error()
    {
        $this->rpc->call($this->client, 'error');
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     * @throws \SimpleShop\Commons\RpcException
     */
    public function __call($name, $arguments)
    {
        $return = $this->rpc->call($this->client, $name, $arguments);
        return $return;
    }
}
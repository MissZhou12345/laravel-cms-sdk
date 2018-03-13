<?php
/**
 *------------------------------------------------------
 * BaseService.php
 *------------------------------------------------------
 *
 * @author    rongzhenbang@liweijia.com
 * @version   V1.0
 *
 */
namespace QuickCms\SDK;

use QuickCms\SDK\Rpc\CmsRpc;

abstract class BaseService
{
    protected $serviceName;

    private $rpc;

    private $client;

    function __construct(CmsRpc $rpc)
    {
        $this->rpc = $rpc;
        $this->client = $rpc->get($this->serviceName);
    }

    function __call($name, $arguments)
    {
        try {
            \Log::info('[rpc][info]', [$this->rpc->baseUrl(), $this->serviceName, $name, $arguments]);
            return $this->call($name, $arguments);
        } catch (\Exception $e) {
            \Log::error('[rpc][error]', [$this->serviceName, $name, $arguments, $e->getCode(), $e->getMessage()]);
            throw $e;
        }
    }

    protected function call($method, array $params = [])
    {
        return $this->rpc->call($this->client, $method, $params);
    }

}
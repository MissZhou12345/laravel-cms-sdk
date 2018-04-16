<?php
namespace QuickCms\SDK\Rpc;

use JsonRpc\Client;

abstract class Rpc
{

    abstract function baseUrl();

    public function get($serviceName)
    {
        $rpcUrl = $this->baseUrl() . '/' . $serviceName;
        return new Client($rpcUrl);
    }

    public function call(Client $client, $method, $params = [], $sync = false)
    {
        if ($client->call($method, $params, $sync) OR $sync) {
            return $client->result;
        } else {
            $error = json_decode($client->error);

            if (JSON_ERROR_NONE !== json_last_error() OR !isset($error->code)) {
                throw new RpcException('server error! : ' . $client->error);
            }

            $code = intval($error->code) ?: RpcException::DEFAULT_CODE;
            $message = !empty($error->message) ? $error->message : $error;
            $exception = new RpcException($message, $code);

            if (isset($error->trace)) {
                $exception->setSvrTrace($error->trace);
            }

            if (!empty($error->business)) {
                $exception->setBusiness(true);
            }

            throw $exception;
        }
    }

    public function addHeaders (Client $client, $args = [])
    {
        $client->addHeaders($args);
    }

}

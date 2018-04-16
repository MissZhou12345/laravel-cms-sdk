<?php
namespace QuickCms\SDK\Rpc;

class RpcException extends \Exception
{

    const DEFAULT_CODE = 2;

    protected $code = self::DEFAULT_CODE;

    protected $svrTrace = 'no trace info';

    protected $isBusiness = false;

    public function setSvrTrace($svrTrace) {
        $this->svrTrace = $svrTrace;
    }

    public function getSvrTrace() {
        return $this->svrTrace;
    }

    public function setBusiness($isBusiness) {
        $this->isBusiness = $isBusiness;
    }

    public function isBusiness() {
        return $this->isBusiness;
    }

}

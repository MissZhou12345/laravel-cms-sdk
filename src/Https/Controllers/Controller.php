<?php

namespace QuickCms\SDK\Https\Controllers;

use App\Http\Controllers\Traits\ReturnFormat;
use App\Http\Controllers\Traits\ValidateHandler;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Request;
use Illuminate\Routing\Controller as BaseController;


abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ReturnFormat, ValidateHandler;

    protected $status = FAILURE_CODE;
    protected $msg = '';
    protected $data = null;
    protected $_user;

    public function __construct()
    {
        if (!Request::ajax()) {
            $this->status = SERVER_ERROR;
            //$this->output();
        }

        parent::__construct();
        $this->_initUser();
    }

    /**
     * 正确返回数据
     * @param unknown $data
     */
    protected function toSucess($data = '')
    {
        $this->status = SUCESS_CODE;
        $this->data = $data;
        return $this->output($data);
    }

    protected function toFailure($msg, $data = [])
    {
        $this->status = FAILURE_CODE;
        $this->msg = $msg;
        $this->data = $data;
        return $this->output();
    }

    protected function checkLogin()
    {
        if (!$this->_user) {
            $this->status = NO_PERMISSION;
            $this->msg = '未登录';
            return $this->output();
        }
    }

    protected function output()
    {

        $arr['status'] = $this->status;
        $arr['msg'] = $this->msg;
        $arr['data'] = $this->data;
        $_callback = request()->get("callback");//JSONP
        if ($_callback) {
            return response()->jsonp($_callback, $arr);
        } else {
            return response()->json($arr);
        }
    }

    private function _initUser()
    {
        $this->_user = session(LOGIN_MARK_SESSION_KEY);
        if ($this->_user['name']) {
            $this->_user['nickname'] = $this->_user['name'];
        } elseif ($this->_user['mobile']) {
            $this->_user['nickname'] = hidePartMobile($this->_user['mobile'], 4);
        } elseif ($this->_user['email']) {
            $this->_user['nickname'] = $this->_user['email'];
        }


    }

}

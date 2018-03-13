<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2018/2/8
 * Time: 15:19
 */

namespace QuickCms\SDK\Https\Controllers;


use App\Http\Controllers\Controller;
use QuickCms\SDK\SpecialService;

class SpecialController extends Controller
{
    public function getDetail($key)
    {
        
        return view('template.default.pc.special.detail', ['key' => $key]);
    }
}
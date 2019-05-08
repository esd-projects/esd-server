<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 11:08
 */

namespace GoSwoole\Go;


use GoSwoole\BaseServer\Exception;

class NoSupportRequestMethodException extends Exception
{
    public function __construct()
    {
        parent::__construct("不支持的请求方法", 0, null);
    }
}
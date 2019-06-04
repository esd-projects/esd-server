<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2019/5/30
 * Time: 11:44 AM
 */
namespace ESD\Go\Exception;

use ESD\Core\Plugins\Logger\GetLogger;

class ResponseException extends \Exception{

    use GetLogger;
    function __construct($message = null, $code = 200, \Throwable $previous = null)
    {
        if(is_null($message)){
            $message = "请求失败，请稍后再试";
        }
        $this->warn($message);

        return parent::__construct($message, $code, $previous);
    }
}
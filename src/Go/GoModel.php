<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/5/7
 * Time: 11:35
 */

namespace GoSwoole\Go;


class GoModel
{
    public function __construct($array)
    {
        $this->buildFromArray($array);
    }

    /**
     * 将会自动在驼峰和"_"连接中寻找存在
     * @param array $array
     */
    public function buildFromArray(array $array)
    {
        foreach ($this as $key => $value) {
            $this->$key = $array[$key] ?? $array[$this->changeConnectStyle($key)] ?? $array[$this->changeHumpStyle($key)] ?? null;
        }
    }

    /**
     * @param bool $changeConnectStyle 转换成“_”连接
     * @return array
     */
    public function buildToArray($changeConnectStyle = true)
    {
        $array = [];
        foreach ($this as $key => $value) {
            if($changeConnectStyle){
                $array[$this->changeConnectStyle($key)] = $value;
            }
        }
        return $array;
    }

    /**
     * 驼峰修改为"_"连接
     * @param $var
     * @return mixed
     */
    private function changeConnectStyle($var)
    {
        if (is_numeric($var)) {
            return $var;
        }
        $result = "";
        for ($i = 0; $i < strlen($var); $i++) {
            $str = ord($var[$i]);
            if ($str > 64 && $str < 91) {
                $result .= "_" . strtolower($var[$i]);
            } else {
                $result .= $var[$i];
            }
        }
        return $result;
    }

    /**
     * "_"连接修改为驼峰
     * @param $var
     * @return mixed
     */
    private function changeHumpStyle($var)
    {
        if (is_numeric($var)) {
            return $var;
        }
        $result = "";
        for ($i = 0; $i < strlen($var); $i++) {
            if ($var[$i] == "_") {
                $i = $i + 1;
                $result .= strtoupper($var[$i]);
            } else {
                $result .= $var[$i];
            }
        }
        return $result;
    }
}
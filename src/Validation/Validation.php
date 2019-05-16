<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/5/16
 * Time: 16:35
 */

namespace ESD\Validation;
class Validation extends AbstractValidation
{
    /**
     * @return array
     */
    /*
    public function rules()
    {
        return [
            ['fields', 'validator', arg0, arg1, something ...]
            ['tagId,userId,name,email,freeTime', 'required'],
            ['userId', 'number'],
        ];
    }
    */

    /**
     * @param string $key
     * @param null   $value
     * @return mixed
     */
    public function get(string $key, $value = null)
    {
        return $this->traitGet($key, $value);
    }
}
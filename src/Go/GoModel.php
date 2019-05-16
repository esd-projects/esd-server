<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/7
 * Time: 11:35
 */

namespace ESD\Go;


use ESD\Plugins\Mysql\GetMysql;
use ESD\Plugins\Mysql\MysqlException;
use ESD\Plugins\Mysql\MysqlManyPool;
use Inhere\Validate\Validation;

/**
 * ORM类，这里约定数据库字段无驼峰且用_连接，Model字段为驼峰
 * 即：user_name(数据库) => userName(Model)
 * Class GoModel
 * @package ESD\Go
 */
abstract class GoModel extends Validation
{
    const update = "update";
    const insert = "insert";
    const select = "select";
    const delete = "delete";
    const replace = "replace";

    use GetMysql;

    /**
     * 获取数据库表名
     * @return string
     */
    abstract public static function getTableName(): string;

    /**
     * 获取数据源名
     * @return string
     */
    abstract public static function getDbName(): string;

    /**
     * 获取主键名
     * @return string
     */
    abstract public static function getPrimaryKey(): string;

    public function __construct($array = [])
    {
        parent::__construct($array);
        $this->buildFromArray($array);
        $this->setRules([
            [$this->getPrimaryKey(), 'required', 'on' => "update,replace,delete"]
        ]);
        $this->setMessages([
            'required' => '{attr} 是必填项。'
        ]);
        $pk = static::getPrimaryKey();
        $this->setTranslates([
            $pk => "主键($pk)"
        ]);
    }

    /**
     * 将会自动在驼峰和"_"连接中寻找存在
     * @param array $array
     */
    public function buildFromArray(array $array)
    {
        $this->data = $array;
        foreach ($this as $key => $value) {
            $this->$key = $array[$key] ?? $array[$this->changeConnectStyle($key)] ?? $array[$this->changeHumpStyle($key)] ?? null;
        }
    }

    /**
     * @param bool $ignoreNull
     * @param bool $changeConnectStyle 转换成“_”连接
     * @return array
     */
    public function buildToArray($ignoreNull = true, $changeConnectStyle = true)
    {
        $array = [];
        foreach ($this as $key => $value) {
            if ($key == "_rules" || $key == "_scene") continue;
            if (is_array($value) || is_object($value)) continue;
            if ($ignoreNull && $value == null) continue;
            if ($changeConnectStyle) {
                $array[$this->changeConnectStyle($key)] = $value;
            }
        }
        $this->data = $array;
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

    /**
     * @param $type
     * @param $ignoreNull
     * @throws ValidateException
     */
    protected function sqlValidate($type, $ignoreNull)
    {
        $this->buildToArray($ignoreNull);
        $this->atScene($type);
        if ($this->validate()->failed()) {
            throw new ValidateException($this->lastError());
        }
        $this->atScene("");
        if ($this->validate()->failed()) {
            throw new ValidateException($this->lastError());
        }
    }

    /**
     * 更新数据库
     * @throws ValidateException
     * @throws \ESD\BaseServer\Exception
     */
    public function update()
    {
        $this->sqlValidate(self::update, false);
        $pk = $this->getPrimaryKey();
        $this->mysql($this->getDbName())
            ->where($pk, $this->data[$pk])
            ->update($this->getTableName(), $this->data);
    }

    /**
     * 更新数据库排除null
     * @throws ValidateException
     * @throws \ESD\BaseServer\Exception
     */
    public function updateSelective()
    {
        $this->sqlValidate(self::update, true);
        $pk = $this->getPrimaryKey();
        $this->mysql($this->getDbName())
            ->where($pk, $this->data[$pk])
            ->update($this->getTableName(), $this->data);
    }

    /**
     * 替换数据库
     * @throws ValidateException
     * @throws \ESD\BaseServer\Exception
     */
    public function replace()
    {
        $this->sqlValidate(self::replace, false);
        $pk = $this->getPrimaryKey();
        $this->mysql($this->getDbName())
            ->where($pk, $this->data[$pk])
            ->replace($this->getTableName(), $this->buildToArray(false));
    }

    /**
     * 替换数据库排除null
     * @throws ValidateException
     * @throws \ESD\BaseServer\Exception
     */
    public function replaceSelective()
    {
        $this->sqlValidate(self::replace, true);
        $pk = $this->getPrimaryKey();
        $this->mysql($this->getDbName())
            ->where($pk, $this->data[$pk])
            ->replace($this->getTableName(), $this->buildToArray());
    }

    /**
     * 插入
     * @throws ValidateException
     * @throws \ESD\BaseServer\Exception
     */
    public function insert()
    {
        $this->sqlValidate(self::insert, false);
        $this->mysql($this->getDbName())
            ->insert($this->getTableName(), $this->buildToArray(false));
    }

    /**
     * 插入排除null
     * @throws ValidateException
     * @throws \ESD\BaseServer\Exception
     */
    public function insertSelective()
    {
        $this->sqlValidate(self::insert, true);
        $this->mysql($this->getDbName())
            ->insert($this->getTableName(), $this->buildToArray());
    }

    /**
     * 删除
     * @throws ValidateException
     * @throws \ESD\BaseServer\Exception
     */
    public function delete()
    {
        $this->sqlValidate(self::delete, false);
        $pk = $this->getPrimaryKey();
        $this->mysql($this->getDbName())
            ->where($pk, $this->data[$pk])
            ->delete($this->getTableName());
    }

    /**
     * @param $pid
     * @return static|null
     * @throws MysqlException
     * @throws \ESD\BaseServer\Exception
     */
    public static function select($pid)
    {
        $pk = static::getPrimaryKey();
        $name = static::getDbName();
        $db = getContextValue("MysqliDb:$name");
        if ($db == null) {
            $mysqlPool = getDeepContextValueByClassName(MysqlManyPool::class);
            if ($mysqlPool instanceof MysqlManyPool) {
                $db = $mysqlPool->getPool($name)->db();
            } else {
                throw new MysqlException("没有找到名为{$name}的mysql连接池");
            }
        }
        $result = $db->where($pk, $pid)
                ->get(static::getTableName(), 1)[0] ?? null;
        if ($result == null) {
            return null;
        } else {
            return new static($result);
        }
    }
}
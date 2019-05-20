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
use ESD\Plugins\Validate\Annotation\Filter;
use ESD\Plugins\Validate\Annotation\Validated;
use ESD\Plugins\Validate\ValidationException;


/**
 * ORM类，这里约定数据库字段无驼峰且用_连接，Model字段为驼峰
 * 即：user_name(数据库) => userName(Model)
 * Class GoModel
 * @package ESD\Go
 */
abstract class GoModel
{
    /**
     * @var \ReflectionClass[]
     */
    private static $modelReflectionClass = [];
    const update = "update";
    const insert = "insert";
    const select = "select";
    const delete = "delete";
    const replace = "replace";
    use GetMysql;

    /**
     * @var array
     */
    private $_data;
    /**
     * @var array
     */
    private $_messages = [];
    /**
     * @var array
     */
    private $_roles = [];

    /**
     * @var array
     */
    private $_translates = [];

    /**
     * @var \ReflectionClass
     */
    private $_reflectionClass;

    /**
     * 获取数据库表名
     * @return string
     */
    abstract public static function getTableName(): string;

    /**
     * 获取主键名
     * @return string
     */
    abstract public static function getPrimaryKey(): string;

    /**
     * GoModel constructor.
     * @param array $array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\Plugins\Validate\ValidationException
     * @throws \ReflectionException
     */
    public function __construct($array = [])
    {
        $this->buildFromArray($array);
        $this->_roles = [static::getPrimaryKey(), "required", "on" => "update,replace"];
        $this->_translates = [];
        $this->_messages = [];
        if (isset(self::$modelReflectionClass[static::class])) {
            $this->_reflectionClass = self::$modelReflectionClass[static::class];
        } else {
            $this->_reflectionClass = new \ReflectionClass(static::class);
            self::$modelReflectionClass[static::class] = $this->_reflectionClass;
        }
    }

    public function setMessages($messages = [])
    {
        $this->_messages = $messages;
    }

    public function setTranslates($translates = [])
    {
        $this->_translates = $translates;
    }

    /**
     * 将会自动在驼峰和"_"连接中寻找存在
     * @param array $array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\Plugins\Validate\ValidationException
     * @throws \ReflectionException
     */
    public function buildFromArray(array $array)
    {
        if (empty($array)) return;
        //转换key格式
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$this->changeHumpStyle($key)] = $value;
        }
        //验证
        $newArray = Filter::filter(static::class, $newArray);
        Validated::valid(static::class, $newArray);
        //设置值
        foreach ($this->_reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isPublic()) {
                $key = $reflectionProperty->name;
                $this->$key = $newArray[$key] ?? null;
            }

        }
        $this->_data = $newArray;
    }

    /**
     * @param bool $ignoreNull
     * @param bool $changeConnectStyle 转换成“_”连接
     * @return array
     */
    public function buildToArray($ignoreNull = true, $changeConnectStyle = true)
    {
        $array = [];
        foreach ($this->_reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isPublic()) {
                $key = $reflectionProperty->name;
                $value = $this->$key;
                if (is_array($value) || is_object($value)) continue;
                if ($ignoreNull && $value == null) continue;
                if ($changeConnectStyle) {
                    $array[$this->changeConnectStyle($key)] = $value;
                } else {
                    $array[$key] = $value;
                }
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

    /**
     * @param $type
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    protected function sqlValidate($type)
    {
        $this->_data = $this->buildToArray(true, false);
        //情景验证
        Validated::valid(static::class, $this->_data, $this->_roles, $this->_messages, $this->_translates, $type);
    }

    /**
     * 更新数据库
     * @param string $selectDb
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function update($selectDb = "default")
    {
        $this->sqlValidate(self::update);
        $data = $this->buildToArray(false);
        $pk = $this->getPrimaryKey();
        $this->mysql($selectDb)
            ->where($pk, $data[$pk])
            ->update($this->getTableName(), $data);
    }

    /**
     * 更新数据库排除null
     * @param string $selectDb
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function updateSelective($selectDb = "default")
    {
        $this->sqlValidate(self::update);
        $data = $this->buildToArray(true);
        $pk = $this->getPrimaryKey();
        $this->mysql($selectDb)
            ->where($pk, $data[$pk])
            ->update($this->getTableName(), $data);
    }

    /**
     * 替换数据库
     * @param string $selectDb
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function replace($selectDb = "default")
    {
        $this->sqlValidate(self::replace);
        $data = $this->buildToArray(false);
        $pk = $this->getPrimaryKey();
        $this->mysql($selectDb)
            ->where($pk, $data[$pk])
            ->replace($this->getTableName(), $data);
    }

    /**
     * 替换数据库排除null
     * @param string $selectDb
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function replaceSelective($selectDb = "default")
    {
        $this->sqlValidate(self::replace);
        $data = $this->buildToArray(true);
        $pk = $this->getPrimaryKey();
        $this->mysql($selectDb)
            ->where($pk, $data[$pk])
            ->replace($this->getTableName(), $data);
    }

    /**
     * 插入
     * @param string $selectDb
     * @return void
     * @throws MysqlException
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function insert($selectDb = "default")
    {
        $this->sqlValidate(self::insert);
        $data = $this->buildToArray(false);
        $id = $this->mysql($selectDb)
            ->insert($this->getTableName(), $data);
        if ($id === false) {
            throw new MysqlException($this->mysql($selectDb)->getLastError(),$this->mysql($selectDb)->getLastErrno());
        }
        $pk = $this->getPrimaryKey();
        $this->$pk = $id;
    }

    /**
     * 插入排除null
     * @param string $selectDb
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function insertSelective($selectDb = "default")
    {
        $this->sqlValidate(self::insert);
        $data = $this->buildToArray(true);
        $id = $this->mysql($selectDb)
            ->insert($this->getTableName(), $data);
        $pk = $this->getPrimaryKey();
        $this->$pk = $id;
    }

    /**
     * 删除
     * @param string $selectDb
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function delete($selectDb = "default")
    {
        $this->sqlValidate(self::delete);
        $pk = $this->getPrimaryKey();
        $this->mysql($selectDb)
            ->where($pk, $this->$pk)
            ->delete($this->getTableName());
    }

    /**
     * @param $pid
     * @param string $selectDb
     * @return static|null
     * @throws MysqlException
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public static function select($pid, $selectDb = "default")
    {
        $pk = static::getPrimaryKey();
        $name = $selectDb;
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
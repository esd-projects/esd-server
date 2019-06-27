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
use ESD\Plugins\Validate\Annotation\ValidatedFilter;
use ESD\Plugins\Validate\ValidationException;
use ESD\Psr\Tracing\TracingInterface;


/**
 * ORM类，这里约定数据库字段无驼峰且用_连接，Model字段为驼峰
 * 即：user_name(数据库) => userName(Model)
 * Class GoModel
 * @package ESD\Go
 */
abstract class GoModel implements TracingInterface
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
     * @throws \ESD\Plugins\Validate\ValidationException
     */
    public function __construct($array = [])
    {
        $this->_roles = [
            [static::getPrimaryKey(), "required", "on" => "update,replace"]
        ];
        $this->_translates = [];
        $this->_messages = [];
        if (array_key_exists(static::class, self::$modelReflectionClass) && self::$modelReflectionClass[static::class] != null) {
            $this->_reflectionClass = self::$modelReflectionClass[static::class];
        } else {
            $this->_reflectionClass = new \ReflectionClass(static::class);
            self::$modelReflectionClass[static::class] = $this->_reflectionClass;
        }
        $this->buildFromArray($array);
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->_roles = array_merge($this->_roles, $roles);
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
     * @throws \ESD\Plugins\Validate\ValidationException
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
        $this->_data = ValidatedFilter::valid(static::class, $newArray, $this->_roles, $this->_messages, $this->_translates);
        //设置值
        foreach ($this->_reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isPublic()) {
                $key = $reflectionProperty->name;
                $this->$key = $this->_data[$key] ?? null;
            }
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
        foreach ($this->_reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isPublic()) {
                $key = $reflectionProperty->name;
                $value = $this->$key;
                if (is_array($value) || is_object($value)) continue;
                if ($ignoreNull && $value === null) continue;
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
     * 刷新data
     */
    protected function refreshData()
    {
        //重新赋值$this->_data
        $this->_data = $this->buildToArray(false, false);
    }

    /**
     * @param $type
     * @throws ValidationException
     */
    protected function sqlValidate($type)
    {
        $this->refreshData();
        //情景验证
        $this->_data = ValidatedFilter::valid(static::class, $this->_data, $this->_roles, $this->_messages, $this->_translates, $type);
    }

    /**
     * 更新数据库
     * @param string $selectDb
     * @throws ValidationException
     * @throws MysqlException
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
     * @throws MysqlException
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
     * @throws MysqlException
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
     * @throws MysqlException
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
     */
    public function insert($selectDb = "default")
    {
        $this->sqlValidate(self::insert);
        $data = $this->buildToArray(false);
        $id = $this->mysql($selectDb)
            ->insert($this->getTableName(), $data);
        if ($id === false) {
            throw new MysqlException($this->mysql($selectDb)->getLastError(), $this->mysql($selectDb)->getLastErrno());
        }
        $pk = $this->getPrimaryKey();
        $this->$pk = $id;
    }

    /**
     * 插入排除null
     * @param string $selectDb
     * @throws MysqlException
     * @throws ValidationException
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
     * @throws MysqlException
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
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
     * 获取数据库表名
     * @return string
     */
    abstract public static function getTableName(): string;

    /**
     * 获取数据源名
     * @return string
     */
    abstract public static function getSelectDb(): string;

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
        foreach ($this as $key => $value) {
            $this->$key = $newArray[$key] ?? null;
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
        foreach ($this as $key => $value) {
            if ($key == "_data") continue;
            if (is_array($value) || is_object($value)) continue;
            if ($ignoreNull && $value == null) continue;
            if ($changeConnectStyle) {
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

    /**
     * @param $type
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    protected function sqlValidate($type)
    {
        //默认验证id是否设置
        $pk = $this->getPrimaryKey();
        if ($this->$pk == null) throw new ValidationException("主键不能为空");
        //情景验证
        Validated::valid(static::class, $this->_data, [], $type);
    }

    /**
     * 更新数据库
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function update()
    {
        $this->sqlValidate(self::update);
        $data = $this->buildToArray(false);
        $pk = $this->getPrimaryKey();
        $this->mysql($this::getSelectDb())
            ->where($pk, $data[$pk])
            ->update($this->getTableName(), $data);
    }

    /**
     * 更新数据库排除null
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function updateSelective()
    {
        $this->sqlValidate(self::update);
        $data = $this->buildToArray(true);
        $pk = $this->getPrimaryKey();
        $this->mysql($this::getSelectDb())
            ->where($pk, $data[$pk])
            ->update($this->getTableName(), $data);
    }

    /**
     * 替换数据库
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function replace()
    {
        $this->sqlValidate(self::replace);
        $data = $this->buildToArray(false);
        $pk = $this->getPrimaryKey();
        $this->mysql(static::getSelectDb())
            ->where($pk, $data[$pk])
            ->replace($this->getTableName(), $data);
    }

    /**
     * 替换数据库排除null
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function replaceSelective()
    {
        $this->sqlValidate(self::replace);
        $data = $this->buildToArray(true);
        $pk = $this->getPrimaryKey();
        $this->mysql(static::getSelectDb())
            ->where($pk, $data[$pk])
            ->replace($this->getTableName(), $data);
    }

    /**
     * 插入
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function insert()
    {
        $this->sqlValidate(self::insert);
        $data = $this->buildToArray(false);
        $this->mysql(static::getSelectDb())
            ->insert($this->getTableName(), $data);
    }

    /**
     * 插入排除null
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function insertSelective()
    {
        $this->sqlValidate(self::insert);
        $data = $this->buildToArray(true);
        $this->mysql(static::getSelectDb())
            ->insert($this->getTableName(), $data);
    }

    /**
     * 删除
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function delete()
    {
        $this->sqlValidate(self::delete);
        $pk = $this->getPrimaryKey();
        $this->mysql(static::getSelectDb())
            ->where($pk, $this->$pk)
            ->delete($this->getTableName());
    }

    /**
     * @param $pid
     * @return static|null
     * @throws MysqlException
     * @throws ValidationException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public static function select($pid)
    {
        $pk = static::getPrimaryKey();
        $name = static::getSelectDb();
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
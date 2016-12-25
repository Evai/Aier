<?php

/**
 * Class MysqlPDO
 */
class MysqlPDO
{
    /**
     * @var string or array
     */
    protected $db_host;
    /**
     * @var string
     */
    protected $db_user;
    /**
     * @var string
     */
    protected $db_password;
    /**
     * 数据库名称
     * @var string
     */
    protected $db_name;
    /**
     * 表名
     * @var string
     */
    protected $tableName;
    /**
     * 主键
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * 条件字段
     * @var string
     */
    private $condition;
    /**
     * 条件值
     * @var array
     */
    private $whereArray;
    /**
     * 查询的字段
     * @var string
     */
    private $field = '*';
    /**
     * 排序字段
     * @var string
     */
    private $orderColumn;
    /**
     * 分页数
     * @var int
     */
    private $page = 0;
    /**
     * 每页显示数量
     * @var int
     */
    private $pageSize;
    /**
     * 分页开关
     * @var bool
     */
    private $take = false;
    /**
     * 去重
     * @var string
     */
    private $groupColumn;
    /**
     * 分组
     * @var string
     */
    private $havingField;
    /**
     * 内连接
     * @var string
     */
    private $innerJoin;
    /**
     * 左连接
     * @var string
     */
    private $leftJoin;
    /**
     * 悲观锁
     * @var bool
     */
    private $lockUpdate = false;
    /**
     * 共享锁
     * @var bool
     */
    private $shareLock = false;

    /**
     * 主服务器
     * @var null
     */
    private $conn;
    private $stmt;

    /**
     * 从服务器
     * @var null
     */
    private $read_conn;
    private $read_stmt;
    /**
     * 调试模式
     * @var bool
     */
    public $debug = true;
    /**
     * 返回值
     * @var array
     */
    public $resArr = ['code' => -1, 'msg' => 'fail'];

    /**
     * MysqlPDO constructor.
     * @param string $db_host   主机ip (也可填写从机ip，格式为['read'=>'192.168.0.1','write'=>'192.168.0.2'])
     * @param string $db_user   用户名
     * @param string $db_password   密码
     * @param string $db_name   数据库名
     */
    function __construct($db_host = '', $db_user = '', $db_password = '', $db_name = '')
    {

        try {

            $this->db_host = $db_host ? $db_host : $this->db_host;

            $this->db_user = $db_user ? $db_user : $this->db_user;

            $this->db_password = $db_password ? $db_password : $this->db_password;

            $this->db_name = $db_name ? $db_name : $this->db_name;

            if (is_array($this->db_host))
            {

                $read_host = $this->db_host['read'];

                $write_host = $this->db_host['write'];

                $this->conn = new PDO('mysql:host=' . $write_host . ';dbname=' . $this->db_name, $this->db_user, $this->db_password);

                $this->read_conn = new PDO('mysql:host=' . $read_host . ';dbname=' . $this->db_name, $this->db_user, $this->db_password);

                // 设置 PDO 错误模式为异常
                $this->read_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }
            else
            {

                $this->conn = new PDO('mysql:host=' . $this->db_host . ';dbname=' . $this->db_name, $this->db_user, $this->db_password);

            }
            // 设置 PDO 错误模式为异常
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //$this->conn->exec('set names utf8');
        }

        catch(Exception $e)

        {

            $this->conn = null;

            $this->read_conn = null;

            $this->getError($e);

        }

    }

    function __destruct()
    {

        $this->conn = null;

        $this->read_conn = null;

    }

    private function getError(Exception $e)
    {
        if ($this->debug) {

            $this->resArr = array(

                'code' => $e->getCode(),

                'msg' => 'SQL Query Error In File '.$e->getFile().' On Line '.$e->getLine().' : '.$e->getMessage()
            );

        }

        exit(json_encode($this->resArr));

    }

    /**
     * 执行入口
     * @param $sql  执行语句
     * @param null $values
     */
    private function run($sql, $values = null)
    {
        try {

            $sql = trim($sql);

            //如果没有配置从服务器，所有操作都在主服务器上
            if (empty($this->read_conn))
            {

                $this->stmt = $this->conn->prepare($sql);

                $this->stmt->execute($values);

            }
            else
            {

                $this->separate($sql, $values);

            }
        }
        catch (Exception $e)
        {
            if ($this->conn->inTransaction()) $this->conn->rollBack();

            $this->conn = null;

            if ($this->read_conn) $this->read_conn = null;

            $this->getError($e);

        }

    }

    /**
     * 主从分离
     * @param $sql  执行语句
     * @param null $values
     */
    private function separate($sql, $values = null) {

        $queryStr = substr($sql, 0, 6);

        //如果是查询语句就去从服务器查询
        if('select' === strtolower($queryStr))
        {

            $this->read_stmt = $this->read_conn->prepare($sql);

            $this->read_stmt->execute($values);
        }
        else
        {
            //不是查询语句去主服务器操作
            $this->stmt = $this->conn->prepare($sql);

            $this->stmt->execute($values);
        }

    }

    /**
     * 开始事务
     */
    function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * 提交事务
     */
    function commit()
    {
        $this->conn->commit();
    }
    /**
     * 回滚事务
     */
    function rollBack()
    {
        $this->conn->rollBack();
    }
    /**
     * 表名
     * @param string $name
     * @return $this
     */
    function table($name = '') {

        $this->tableName = $name;

        return $this;

    }

    /**
     * 操作条件
     * @param string $condition 'id = ? and name = ?'
     * @param array $whereArray  [1, 'hello']
     * @return $this
     * @throws Exception
     */
    function where($condition = '', array $whereArray)
    {
        $this->condition = $condition;

        if (empty($whereArray)) {

            throw new Exception('not empty array');

        }

        $this->whereArray = $whereArray;

        return $this;
    }

    /**
     * 选择的字段
     * @param string $field
     * @return $this
     */
    function select($field = '*')
    {
        $this->field = $field;

        return $this;
    }
    /*
     * 排序
     */
    function orderBy($column)
    {
        $this->orderColumn = $column;

        return $this;
    }

    /**
     * 限制或分页
     * @param int $page 取多少条
     * @param int $pageSize 分页数
     * @return $this
     */
    function limit($page = 0, $pageSize = 0)
    {
        $this->page = $page;

        $this->pageSize = $pageSize ? $pageSize : 0;

        $this->take = true;

        return $this;
    }

    /**
     * 去重
     * @param $column
     * @return $this
     */
    function groupBy($column)
    {
        $this->groupColumn = $column;

        return $this;
    }
    /*
     * 分组
     */
    function having($fields)
    {
        $this->havingField = $fields;

        return $this;
    }

    /**
     * 内连接
     * @param $sentence 'users as u on u.userid = s.userid'
     * @return $this
     */
    function innerJoin($sentence)
    {
        $this->innerJoin .= $sentence ? ' INNER JOIN '.$sentence : '';

        return $this;
    }

    /**
     * 左连接
     * @param $sentence 'users as u on u.userid = s.userid'
     * @return $this
     */
    function leftJoin($sentence)
    {
        $this->leftJoin .= $sentence ? ' LEFT JOIN '.$sentence : '';

        return $this;
    }

    /**
     * 悲观锁，可以修改数据
     * @return $this
     */
    function lockForUpdate()
    {
        $this->lockUpdate = true;

        return $this;
    }

    /**
     * 共享锁，不能修改数据
     * @return $this
     */
    function sharedLock()
    {
        $this->shareLock = true;

        return $this;
    }

    /**
     * 清空字段
     */
    function clear()
    {
        $this->tableName = $this->innerJoin = $this->leftJoin = $this->condition = $this->whereArray = $this->groupColumn = $this->orderColumn = $this->pageSize = $this->havingField = null;

        $this->lockUpdate = false;

        $this->shareLock = false;

        $this->take = false;

        $this->field = '*';

        $this->page = 0;

    }

    /**
     * 查询多条记录
     * @return array (object)
     */
    function get()
    {
        $condition = '';

        $whereArray = null;

        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {

            $condition = ' WHERE '.$this->condition;

            $whereArray = $this->whereArray;

        }

        $groupBy = $this->groupColumn ?  ' GROUP BY '.$this->groupColumn : '';

        $orderBy = $this->orderColumn ?  ' ORDER BY '.$this->orderColumn : '';

        $having = $this->havingField ?  ' HAVING '.$this->havingField : '';

        $limit = '';

        if ($this->take)
        {
            $limit = $this->pageSize > 0 ? ' LIMIT '.$this->page.','.$this->pageSize : ' LIMIT '.$this->page;
        }

        $innerJoin = $this->innerJoin ? $this->innerJoin : '';

        $leftJoin = $this->leftJoin ? $this->leftJoin : '';

        $lockUpdate = $this->lockUpdate ? ' FOR UPDATE' : '';

        $shareLock = $this->shareLock ? ' LOCK IN SHARE MODE' : '';

        $sql = 'SELECT '.$this->field.' FROM '.$this->tableName.$innerJoin.$leftJoin.$condition.$groupBy.$orderBy.$having.$limit.$lockUpdate.$shareLock;

        $this->run($sql, $whereArray);

        $this->stmt->setFetchMode(PDO::FETCH_OBJ);

        $data = $this->stmt->fetchAll();

        $this->clear();

        return $data;
    }

    /**
     * 查询一条记录
     * @return mixed
     */
    function first()
    {
        $condition = '';

        $whereArray = null;
        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {
            $condition = ' WHERE '.$this->condition;

            $whereArray = $this->whereArray;

        }

        $orderBy = $this->orderColumn ?  ' ORDER BY '.$this->orderColumn : '';

        $having = $this->havingField ?  ' HAVING '.$this->havingField : '';

        $innerJoin = $this->innerJoin ? $this->innerJoin : '';

        $leftJoin = $this->leftJoin ? $this->leftJoin : '';

        $lockUpdate = $this->lockUpdate ? ' FOR UPDATE' : '';

        $shareLock = $this->shareLock ? ' LOCK IN SHARE MODE' : '';

        $sql = 'SELECT '.$this->field.' FROM '.$this->tableName.$innerJoin.$leftJoin.$condition.$orderBy.$having.$lockUpdate.$shareLock;

        $this->run($sql, $whereArray);

        $data = $this->stmt->fetchObject();

        $this->clear();

        return $data;
    }

    /**
     * 插入记录并返回最后一条自增id
     * @param array $insertValues 插入的字段名和值 ['name'=>'evai']
     * @return bool|string
     */
    function insertGetId(array $insertValues)
    {
        if (!is_array($insertValues))
        {
            return false;
        }

        $key_array = array_keys($insertValues);

        $key_array = implode(', ', $key_array);

        $param_array = array_values($insertValues);

        $where = '';

        foreach ($insertValues as $key => $value)
        {
            $where = '('.$this->parameterize($param_array).')';
        }

        $sql = "INSERT INTO $this->tableName ($key_array) VALUES $where";

        $this->run($sql, $param_array);

        $res = $this->stmt->rowCount();

        if ($res > 0) return $this->conn->lastInsertId();

        return false;
    }

    /**
     * 插入多条记录
     * @param array $insertValues [['name'=>'evai'],['name'=>'other']]
     * @return bool
     */
    function insertBatch(array $insertValues)
    {
        if (!is_array($insertValues))
        {
            return false;
        }
        elseif (!is_array(reset($insertValues)))
        {
            return false;
        }

        //[[], []]
        $key_array = array_keys(reset($insertValues));

        $key_array = implode(', ', $key_array);

        $parameters = [];

        $bindValues = [];

        foreach ($insertValues as $record)
        {
            $parameters[] = '('.$this->parameterize($record).')';
        }

        $parameters = implode(',', $parameters);

        foreach ($insertValues as $value)
        {
            foreach ($value as $val)
            {
                $bindValues[] = $val;
            }
        }

        $sql = "INSERT INTO $this->tableName ($key_array) VALUES $parameters";

        $this->run($sql, $bindValues);

        $res = $this->stmt->rowCount();

        if ($res > 0) return true;

        return false;
    }

    /**
     * 更新记录
     * @param array $updatetValues
     * @return bool|int
     */
    function update(array $updatetValues)
    {
        $column = '';

        $values = [];

        $condition = '';

        foreach ($updatetValues as $key => $value)
        {
            $column .= $key.'= ?,';

            $values[]= $value;
        }

        $column = rtrim($column, ",");

        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {
            $condition = ' WHERE '.$this->condition;

            foreach ($this->whereArray as $value)
            {
                $values[] = $value;
            }
        }

        $sql = 'UPDATE '.$this->tableName.' SET '.$column.$condition;

        $this->run($sql, $values);

        $res = $this->stmt->rowCount();

        $this->clear();

        return $res;

    }

    /**
     * 删除记录
     * @return bool|int
     */
    function delete()
    {
        $condition = '';

        $values = [];

        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {
            $condition = ' WHERE '.$this->condition;

            foreach ($this->whereArray as $value)
            {
                $values[] = $value;
            }
        }
        $limit = '';

        if ($this->take)
        {
            $limit = $this->pageSize > 0 ? ' LIMIT '.$this->page.','.$this->pageSize : ' LIMIT '.$this->page;
        }

        $sql = 'DELETE FROM '.$this->tableName.$condition.$limit;

        $this->run($sql, $values);

        $res = $this->stmt->rowCount();

        $this->clear();

        return $res;

    }

    /**
     * 参数序列化
     * @param array $values
     * @return string
     */
    private function parameterize(array $values)
    {
        return implode(', ', array_map([$this, 'parameter'], $values));
    }
    /*
     * 预处理绑定
     */
    private function parameter()
    {
        return '?';
    }

    /**
     * 清空表
     * @return bool
     */
    function truncate()
    {
        if (empty($this->tableName))
        {
            return false;
        }

        $sql = 'TRUNCATE TABLE '.$this->tableName;

        $this->run($sql);

        return true;
    }

    /**
     * 表条数总计
     * @param string $column
     * @return array
     */
    function count($column = '*')
    {
        if (!is_array($column) || count($column) <= 0)
        {
            $column = [$column];
        }

        $count_param = [];

        foreach ($column as $value)
        {
            $count_param[] = 'COUNT('.$value.')';
        }

        $count_param = implode(', ', $count_param);

        $condition = '';

        $whereArray = null;
        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {
            $condition = ' WHERE '.$this->condition;

            $whereArray = $this->whereArray;
        }
        $groupBy = $this->groupColumn ?  ' GROUP BY '.$this->groupColumn : '';

        $orderBy = $this->orderColumn ?  ' ORDER BY '.$this->orderColumn : '';

        $limit = '';

        if ($this->take)
        {
            $limit = $this->pageSize > 0 ? ' LIMIT '.$this->page.','.$this->pageSize : ' LIMIT '.$this->page;
        }

        $innerJoin = $this->innerJoin ? $this->innerJoin : '';

        $leftJoin = $this->leftJoin ? $this->leftJoin : '';

        $sql = 'SELECT '.$count_param.' FROM '.$this->tableName.$innerJoin.$leftJoin.$condition.$groupBy.$orderBy.$limit;

        $this->run($sql, $whereArray);

        $this->stmt->setFetchMode(PDO::FETCH_OBJ);

        $data = $this->stmt->fetchAll();

        $this->clear();

        return $data;
    }

    /**
     * 表条数总计
     * @param $column
     * @return array
     */
    function sum($column)
    {
        if (!is_array($column))
        {
            $column = [$column];
        }

        $sum_param = [];

        foreach ($column as $value)
        {
            $sum_param[] = 'SUM('.$value.')';
        }

        $sum_param = implode(', ', $sum_param);

        $condition = '';

        $whereArray = null;

        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {
            $condition = ' WHERE '.$this->condition;
            $whereArray = $this->whereArray;
        }

        $groupBy = $this->groupColumn ?  ' GROUP BY '.$this->groupColumn : '';

        $orderBy = $this->orderColumn ?  ' ORDER BY '.$this->orderColumn : '';

        $limit = '';

        if ($this->take)
        {
            $limit = $this->pageSize > 0 ? ' LIMIT '.$this->page.','.$this->pageSize : ' LIMIT '.$this->page;
        }

        $innerJoin = $this->innerJoin ? $this->innerJoin : '';

        $leftJoin = $this->leftJoin ? $this->leftJoin : '';

        $sql = 'SELECT '.$sum_param.' FROM '.$this->tableName.$innerJoin.$leftJoin.$condition.$groupBy.$orderBy.$limit;

        $this->run($sql, $whereArray);

        $this->stmt->setFetchMode(PDO::FETCH_OBJ);

        $data = $this->stmt->fetchAll();

        $this->clear();

        return $data;
    }

    /**
     * 自增并更新数据
     * @param array $incrementValues  自增字段和值
     * @param array $updatetValues  更新字段和值
     * @return bool|int
     */
    function increment(array $incrementValues, $updatetValues = array())
    {
        $column = '';

        $values = [];

        $condition = '';

        foreach ($incrementValues as $key => $value)
        {
            $column .= "$key = $key + ?,";
            $values[] = $value;
        }

        if (is_array($updatetValues) && count($updatetValues) > 0)
        {
            foreach ($updatetValues as $key => $value)
            {
                $column .= "$key = ?,";
                $values[] = $value;
            }
        }

        $column = rtrim($column, ",");

        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {
            $condition = ' WHERE '.$this->condition;

            foreach ($this->whereArray as $value)
            {
                $values[] = $value;
            }
        }

        $sql = 'UPDATE '.$this->tableName.' SET '.$column.$condition;

        $this->run($sql, $values);

        $res = $this->stmt->rowCount();

        $this->clear();

        if ($res > 0) return $res;

        return false;
    }

    /**
     * 自减并更新数据
     * @param array $decrementValues  自减字段和值
     * @param array $updatetValues  更新字段和值
     * @return bool|int
     */
    function decrement(array $decrementValues, $updatetValues = array())
    {
        $column = '';

        $values = [];

        $condition = '';

        foreach ($decrementValues as $key => $value)
        {
            $column .= "$key = $key - ?,";

            $values[] = $value;
        }
        if (is_array($updatetValues) && count($updatetValues) > 0)
        {
            foreach ($updatetValues as $key => $value)
            {
                $column .= "$key = ?,";

                $values[] = $value;
            }
        }

        $column = rtrim($column, ",");

        //执行条件
        if ($this->condition && count($this->whereArray) > 0)
        {
            $condition = ' WHERE '.$this->condition;

            foreach ($this->whereArray as $value)
            {
                $values[] = $value;
            }
        }
        $sql = 'UPDATE '.$this->tableName.' SET '.$column.$condition;

        $this->run($sql, $values);

        $res = $this->stmt->rowCount();

        $this->clear();

        if ($res > 0) return $res;

        return false;
    }

    /**
     * 更改表名
     * @param $from 旧的表名
     * @param $to   新的表名
     * @return bool
     */
    function renameTable($from, $to)
    {
        $sql = "ALTER TABLE {$from} RENAME {$to}";

        $this->run($sql);

        return true;
    }

    /**
     * 删除表
     * @param $tableName  表名
     * @return bool
     */
    function dropTable()
    {
        if (empty($this->tableName))
        {
            return false;
        }

        $sql = "DROP TABLE {$this->tableName}";

        $this->run($sql);

        return true;
    }

    /**
     * 查看存在的表
     * @param null $database  数据库名称
     * @return array
     */
    function hasTable($database = null) {

        $database = $database ? $database : $this->db_name;

        $sql = "SHOW TABLES FROM {$database}";

        $this->run($sql);

        $this->stmt->setFetchMode(PDO::FETCH_OBJ);

        $data = $this->stmt->fetchAll();

        return $data;

    }

    /**
     * 查看表中存在的字段
     * @param null $tableName  表名
     * @return array
     */
    function hasColumn($tableName = null)
    {
        $this->tableName = $tableName ? $tableName : $this->tableName;

        if (empty($this->tableName))
        {
            return array();
        }

        $sql = "DESC {$this->tableName}";

        $this->run($sql);

        $this->stmt->setFetchMode(PDO::FETCH_OBJ);

        $data = $this->stmt->fetchAll();

        return $data;
    }

    /**
     * 新建字段
     * @param string $column
     * @return bool
     */
    function addColumn($column = '')
    {
        if (empty($this->tableName) || empty($column))
        {
            return false;
        }

        $sql = "ALTER TABLE {$this->tableName} ADD COLUMN {$column}";

        $this->run($sql);

        return true;
    }

    /**
     * 修改字段属性
     * @param string $column
     * @return bool
     */
    function modifyColumn($column = '')
    {
        if (empty($this->tableName) || empty($column))
        {
            return false;
        }

        $sql = "ALTER TABLE {$this->tableName} MODIFY COLUMN {$column}";

        $this->run($sql);

        return true;
    }

    /**
     * 更换字段
     * @param null $oldName 旧的字段名
     * @param string $column 新的字段名
     * @return bool
     */
    function changeColumn($oldName = null, $column = '')
    {
        if (empty($this->tableName) || empty($column) || empty($oldName))
        {
            return false;
        }

        $sql = "ALTER TABLE {$this->tableName} CHANGE COLUMN `{$oldName}` {$column}";

        $this->run($sql);

        return true;
    }

    /**
     * 删除字段
     * @param null $column  字段名
     * @return bool
     */
    function dropColumn($column = null)
    {
        if (empty($this->tableName) || empty($column))
        {
            return false;
        }

        $sql = "ALTER TABLE {$this->tableName} DROP COLUMN {$column}";

        $this->run($sql);

        return true;
    }

    /**
     * 添加唯一索引
     * @param null $column  字段名
     * @return bool
     */
    function addUnique($column = array())
    {
        if (empty($this->tableName) || empty($column))
        {
            return false;
        }

        if (!is_array($column))
        {
            $column = [$column];
        }

        $column = implode(', ', $column);

        $sql = "ALTER TABLE {$this->tableName} ADD UNIQUE($column)";

        $this->run($sql);

        return true;
    }

    /**
     * 添加普通索引
     * @param array $column
     * @return bool
     */
    function addIndex($column = array())
    {
        if (empty($this->tableName) || empty($column))
        {
            return false;
        }

        if (!is_array($column))
        {
            $column = [$column];
        }

        $column = implode(', ', $column);

        $sql = "ALTER TABLE {$this->tableName} ADD INDEX($column) ";

        $this->run($sql);

        return true;
    }

    /**
     * 删除索引
     * @param null $column  索引名
     * @return bool
     */
    function dropIndex($indexName = null)
    {
        if (empty($this->tableName) || empty($indexName))
        {
            return false;
        }

        $sql = "ALTER TABLE {$this->tableName} DROP INDEX {$indexName}";

        $this->run($sql);

        return true;
    }

    /**
     * 添加主键
     * @param null $column  字段名
     * @return bool
     */
    function addPrimary($column = null)
    {
        if (empty($this->tableName) || empty($column))
        {
            return false;
        }

        $sql = "ALTER TABLE {$this->tableName} ADD PRIMARY KEY($column)";

        $this->run($sql);

        return true;
    }

    /**
     * 删除主键
     * @return bool
     */
    function dropPrimary()
    {
        if (empty($this->tableName))
        {
            return false;
        }

        $sql = "ALTER TABLE {$this->tableName} DROP PRIMARY KEY";

        $this->run($sql);

        return true;
    }

    function showIndex()
    {
        if (empty($this->tableName))
        {
            return false;
        }

        $sql = "SHOW INDEX FROM {$this->tableName}";

        $this->run($sql);

        $this->stmt->setFetchMode(PDO::FETCH_OBJ);

        $data = $this->stmt->fetchAll();

        return $data;
    }

    /**
     * 主键查询
     * @param $id
     * @return mixed
     */
    /*function find($id)
    {
        return $this->table($this->tableName)->where($this->primaryKey . ' = ?', [$id])->first();
    }*/

}

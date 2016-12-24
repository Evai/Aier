<?php

class Users extends MysqlPDO
{
    protected $db_host = 'localhost';
    protected $db_user = 'root';
    protected $db_password = 'admin123';
    protected $db_name = 'weishenghuo';
    protected $primaryKey = 'userid';

    //表名
    protected $tableName = 'users';
    public $debug = 1;

}

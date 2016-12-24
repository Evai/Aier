<?php

use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{

    public $timestamps = false;
    public $table = 'users';
    public $primaryKey = 'userid';


    public static function mysqli()
    {
        $servername = "localhost";
        $username = "root";
        $password = "admin123";
        // 创建连接
        $conn = mysqli_connect($servername, $username, $password);
        // 检测连接
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $conn->set_charset('utf8');
        $conn->select_db('weishenghuo');

        $conn->close();
    }

}

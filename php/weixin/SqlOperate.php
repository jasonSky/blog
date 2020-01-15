<?php
@header('Content-type: text/plain;charset=UTF-8');

class SqlOperate{

static $host = "localhost:3309";//服务器地址
static $root = "root";//用户名
static $pass = "!QAZ2wsx";//密码
static $dbbase = "SpiderData";//数据库名

public static function conn(){
	$link = @mysql_connect(self::$host,self::$root,self::$pass) or die("数据库连接失败" . mysql_error());
	mysql_query("set names utf8");
	mysql_select_db(self::$dbbase,$link);
    return $link;
}

//随机取一条数据
public static function query($con, $table='tb_poems'){
    $con->query('set names utf8;'); 
    $sql = "SELECT * FROM ".$table." ORDER BY RAND() LIMIT 1";  
    $result = $con->query($sql);  
    $data=array();
    while ($tmp=mysqli_fetch_assoc($result)) {
        $data[]=$tmp;
    }
    return $data;
}

}

//$con =SqlOperate::conn();
//SqlOperate::query($con);
//mysql_close($con);

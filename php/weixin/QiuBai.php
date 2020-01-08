<?php

function getData($idx){
    //ini_set("display_errors", "On");
    //error_reporting(E_ALL | E_STRICT);
    header("Content-type:text/html;charset=utf-8");
    //连接数据库
    $con = mysql_connect("localhost:3309","dbuser","dbpass");
    if ($con) {
        //echo "connect success";
        //选择数据库
        mysql_select_db("data_ipt",$con);
        mysql_query("set names 'utf8'"); //select 数据库之后加多这一句
        //获得GET里面的值
        $name = $_GET['name'];
        //查询数据库
        $query = mysql_query("SELECT * FROM content where id=".$idx);
        //数据库查询结果保存为数组（注意第二个参数）
        // MYSQL_ASSOC - 关联数组
        // MYSQL_NUM - 数字数组
        // MYSQL_BOTH - 默认。同时产生关联和数字数组
        $num=mysql_num_rows($query);
    	for ($i=0; $i <$num ; $i++) { 
             $row = mysql_fetch_array($query,MYSQL_ASSOC);
             $result = $row['title']."\r\n".$row['content'];
    	}
        return $result;
    }else{
        return "";
    }
 
    mysql_close($con);
}
?>

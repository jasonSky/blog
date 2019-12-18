<?php
/**
 * Created by PhpStorm.
 * User: yangjian
 * Date: 17-10-14
 * Time: 下午5:28
 */
print '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
require_once "SimpleDB.php";

$page = $_GET["page"];
$db = new SimpleDB("test");
/*
$t = 100000000;
for($i = 0; $i < 100; $i++) {
    $db->putLine($t+$i);
}
printf("数据插入完毕！\n");
*/
$items = $db->getDataList($page, 10);
print_r($items);


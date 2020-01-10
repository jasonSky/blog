<?php
@header('Content-type: text/plain;charset=UTF-8');

require("Utils.php");
require("ZhConvert.php");

$data = Utils::getOnePoem();
var_dump($data);
echo $data["title"]."\n";
echo ZhConvert::zh_hant_to_zh_hans($data["title"])."\n";
echo ZhConvert::zh_hant_to_zh_hans_old($data["title"]);
//echo ZhConvert::zh_hans_to_zh_hant($data["title"]);




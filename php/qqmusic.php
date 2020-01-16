<?php
@header('Content-type: text/plain;charset=UTF-8');

require_once("HttpUtils.php");
require_once("Utils.php");
//require_once("QiuBai.php");

//$tokenFile = "./access_token.json";
//$res = file_get_contents($tokenFile);
//print $res;
//print Utils::getToken();
//$str = file_get_contents("./idx");
//print $str+1
//print getData($str+1);
//file_put_contents("./idx", $str+1);
//print $_SERVER['DOCUMENT_ROOT']
//$res=Utils::getMediaId("image");
//print explode("\t", $res)[0] ;

$url="http://ws.stream.qqmusic.qq.com/C400003hwrpu3erQcz.m4a?guid=2531283263&vkey=78D3B3EAB441D3C74A75212E0A8B17E7A294D08748F6DD49842A8CB63F6ED5FBF945108FCFB2E5229497F46E456E61C2FEE48D4F4D7EE965&uin=0&fromtag=999"
$result = https_request($url);
print $result;

function getPurl($songmid){
    $data='{"req_0":{"module":"vkey.GetVkeyServer","method":"CgiGetVkey","param":{"guid":"2531283263","songmid":["'.$songmid.'"],"songtype":[0],"uin":"0","loginflag":1,"platform":"20"}},"comm":{"uin":"0","format":"json","ct":24,"cv":0}}';
    $url2 = 'https://u.y.qq.com/cgi-bin/musicu.fcg?format=json&data='.urlencode($data);
    $result = https_requesth($url2,null,$header);
    print $result; 
    $obj = json_decode($result,true);
    $data = $obj["req_0"]["data"];
    //var_dump($obj);
    //$purl = $data["midurlinfo"]["purl"];
    //$fileName = $data["midurlinfo"][0]["filename"];
    //$sip = $data["sip"][0];
    //print $sip."\n";//http://ws.stream.qqmusic.qq.com/
    //print $fileName;//"C400".$songMediaId.".m4a"
}

function getVkey($songmid, $songMediaId){
   $guid = Utils::getGuid();
   $url2 = "https://c.y.qq.com/base/fcgi-bin/fcg_music_express_mobile3.fcg?format=json&cid=205361747&callback=callback&uin=0&songmid=".$songmid."&filename=C400".$songMediaId.".m4a&guid=".$guid;
   print $url2."\n";
   $result = https_request($url2);
   print $result;
}

//getPurl($songmid);
//getVkey($songmid,$songMediaId);

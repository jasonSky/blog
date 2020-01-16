<?php
require_once("SqlOperate.php");
require("ZhConvert.php");
require_once("HttpUtils.php");

class Utils
{
    public static function traceHttp()
    {
        $content = date('Y-m-d H:i:s')."\nremote_ip：".$_SERVER["REMOTE_ADDR"].
            "\n".$_SERVER["QUERY_STRING"]."\n\n";
        $max_size = 1000;
        $log_filename = "./log/query.xml";
        if (file_exists($log_filename) and (abs(filesize($log_filename))) > $max_size){
            unlink($log_filename);
        }else {
 
        }
        file_put_contents($log_filename, $content, FILE_APPEND);
    }
 
    public static function logger($log_content, $type = '用户')
    {
        $max_size = 300000;
        $log_filename = "./log/log.xml";
        if (file_exists($log_filename) and (abs(filesize($log_filename)) >
                $max_size)) {
            unlink($log_filename);
        }
        file_put_contents($log_filename, "$type  ".date('Y-m-d H:i:s')."\n".$log_content."\n",
            FILE_APPEND);
    }

    public static function getMediaId($type){
        $f = './temp_'.$type;//文件名
        $a = file($f);//把文件的所有内容获取到数组里面
        $n = count($a);//获得总行数
        $rnd = rand(0,$n);//产生随机行号
        $rnd_line = $a[$rnd];//获得随机行
        return $rnd_line;
    }
     
   public static function randFloat($min=0, $max=1){
        return $min + mt_rand()/mt_getrandmax() * ($max-$min);
    }

    public static function getGuid(){    
        return intval(self::randFloat() * 2147483647) * time()*1000 % 10000000000;
    }

    //成员变量
    //公众号
    //public static $appid = "wx6d6d705e0542b8cb"; //需替换成你的appID的值
    //public static $appsecret = "ead7f54dbadb57edb81a381ce37d7bf0";  //需替换成你的appsecret的值
    //测试号
    public static $appid = "wx47cd8daa57d1c17d";
    public static $appsecret = "e00ec1a36075d5486d59744de452db1a";
    /*
    *  获取access_token
    */
    public static function getToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&"."appid=".self::$appid."&secret=".self::$appsecret;
        //echo self::$appid;
        //echo $url;
        //本地写入 缓存access_token
        $tokenFile = "./access_token.json";
        $res = file_get_contents($tokenFile);
        $result = json_decode($res, true);
        $expires_time = $result["expires_time"];
        $access_token = $result["access_token"];
        if (time() > ($expires_time + 3600)){
            $output = https_request($url);
            //echo "\n"
            $jsoninfo = json_decode($output, true);
            $access_token = $jsoninfo["access_token"];
            file_put_contents($tokenFile, '{"access_token": "'.$access_token.'", "expires_time": '.time().'}');
        }
        return $access_token;
    }

    public static function getJsApiToken()
    {
        Utils::logger("getJsApi");
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".Utils::getToken();
        //本地写入 缓存jsapi_token
        Utils::logger($url);
        $tokenFile = "./jsapi_token.json";
        $res = file_get_contents($tokenFile);
        $result = json_decode($res, true);
        $expires_time = $result["expires_time"];
        $jsapi_token = $result["ticket"];
        if (time() > ($expires_time + 7200)){
            $output = https_request($url);
            Utils::logger($output);
            $jsoninfo = json_decode($output, true);
            $jsapi_token = $jsoninfo["ticket"];
            file_put_contents($tokenFile, '{"ticket": "'.$jsapi_token.'", "expires_time": '.time().'}');
        }
        return $jsapi_token;
    }

    public static function getSignPackage() {
        $jsapiTicket = Utils::getJsApiToken();
        $url = "https://$_SERVER[HTTP_HOST]".substr("$_SERVER[REQUEST_URI]",5);
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=".$nonceStr."&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
          "appId"     => self::$appid,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string
        );
        Utils::logger(print_r($signPackage, true)); 
        return $signPackage; 
    }
      
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    //保存图片到本地
    public static function save_weixin_file($filename, $fileConent)
    {
        $local_file = fopen($filename, "w");
        if (false !== $local_file){
            if (false !== fwrite($local_file, $fileConent)){
                fclose($local_file);
            }
        }
    }
    
    public static function getMusicUrl($txt){
        $url1 = "https://c.y.qq.com/soso/fcgi-bin/client_search_cp?n=1&w=".urlencode($txt)."&format=json";
        $result = https_request($url1);
        $obj = json_decode($result,true);
        $songlist = $obj["data"]["song"]["list"];
        $songmid = $songlist[0]["songmid"];
        $songMediaId = $songlist[0]["strMediaMid"];
        //print $songmid."\n";
        //print $songMediaId."\n";
        $result = file_get_contents("./music/".$songmid.".txt");
        $obj = json_decode($result,true);
        $data = $obj["req_0"]["data"];
        $wifiurl = $data["midurlinfo"][0]["wifiurl"];
        //$sip = $data["sip"][0];
        //print $sip."\n";//http://ws.stream.qqmusic.qq.com/
        //print $purl;//"C400".$songMediaId.".m4a"
        return "http://ws.stream.qqmusic.qq.com/".$wifiurl;
    } 
     
    public static function getOnePoem(){
       $con=SqlOperate::conn();
       $data=SqlOperate::query($con);
       mysql_close($con);
       //var_dump($data[0]);
       return $data[0];
    }

    public static function getOneJuzi(){
       $con=SqlOperate::conn();
       $data=SqlOperate::query($con, "tb_juzi");
       mysql_close($con);
       //var_dump($data[0]);
       return $data[0];
    }

    public static function getOneCaimi(){
       $con=SqlOperate::conn();
       $data=SqlOperate::query($con, "tb_miyu");
       mysql_close($con);
       //var_dump($data[0]);
       return $data[0];
    }

    public static function getOneYizhi($index){
       $con=SqlOperate::conn();
       $data=SqlOperate::queryByIndex($con, "tb_yizhi", $index);
       mysql_close($con);
       //var_dump($data[0]);
       return $data[0];
    }


    public static function convertChinese($str){
        return ZhConvert::zh_hant_to_zh_hans_old($str); 
    }
  
}

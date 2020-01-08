<?php
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
    //public static $appid = ""; //需替换成你的appID的值
    //public static $appsecret = "";  //需替换成你的appsecret的值
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
     
}

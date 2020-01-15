<?php

//设置时区
date_default_timezone_set("Asia/Shanghai");
define("TOKEN", "jasonsky");
require_once("Utils.php");
require_once("HttpUtils.php");
require_once("QiuBai.php");
//打印请求的URL查询字符串到query.xml
Utils::traceHttp();

function checkSignature()
{
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $echostr = $_GET["echostr"];
	
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1( $tmpStr ); 
   
    if( $tmpStr == $signature ){
        print $echostr;
    }else{
        print "error";
    }
}


    /**
     * 如果有"echostr"字段，说明是一个URL验证请求
    */
    function responseMsg()
    {
        //获取post过来的数据，它一个XML格式的数据
        $postStr = file_get_contents('php://input');
        //file_get_contents('php://input');
        //$GLOBALS["HTTP_RAW_POST_DATA"];
        //将数据打印到log.xml
        Utils::logger("post:".$postStr);
        if (!empty($postStr)){
            //将XML数据解析为一个对象
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            //消息类型分离
            switch($RX_TYPE){
                case "event":
                    $result = receiveEvent($postObj);
                    break;
                case "text":
                    $txt = trim($postObj->Content);
                    Utils::logger("消息:".$txt);
                    if($txt=="老婆"){//temp资源 mediaid才可以 永久资源的mediaId报错
                        //$mediaId = Utils::getMediaId("image");
                        $str = Utils::getMediaId("image");
                        $result = transmitImage($postObj, explode("\t", $str)[0]);
                    }else if($txt=="歌单"){
                        $a = file('./music/txt');
                        foreach($a as $line => $content){
                           $strs .= explode(' ', $content)[0]."\n";
                        }
                        $result = transmitText($postObj, $strs);
                    }else if(mb_strpos($txt,"音乐")!==false || mb_strpos($txt,"歌曲")!==false){//本地
                        $title = substr($txt,9);
                        //$url = Utils::getMusicUrl($title);
                        $url = "https://blog.jasonsky.com.cn/php/weixin/music/".urlencode($title).".m4a";
                        $result = transmitMusic($postObj, $title, $title, $url, "XirC7cn0q3zEGv01E0cU-wYdSRLeFqOh2JudQAkdzqU6T7Zfm0aOD0WxYf6Ntpu9");
                    }else if(mb_strpos($txt, "AV")!==false || mb_strpos($txt, "av")!==false){
                         //$keyword = substr($txt,9);
                         //$url_av="https://photo.jasonsky.com.cn/api/index.php?name=".$keyword;
                         //Utils::logger($url_av);
                         //$resobj = https_request($url_av);
                         //$jsoninfo = json_decode($resobj, true);
                         //if(count($jsoninfo["data"])>1){//txt消息长度限制
                         //      $jsoninfo["data"] = array_slice($jsoninfo["data"],0,1);
                         //}
                         //foreach($jsoninfo["data"] as $obj){
                         //    $content .= $obj["name"].":".$obj["live"]." ";
                         //}
                         $keyword = substr($txt,5);
                         $content="https://photo.jasonsky.com.cn/api/index.php?name=".$keyword; 
                         $result = transmitText($postObj, $content);
                    }else{
                        $url_define="http://api.qingyunke.com/api.php?key=free&appid=0&msg=".urlencode($txt);
                        Utils::logger($url_define);
                        $resobj = https_request($url_define);
                        Utils::logger($resobj);
                        $jsoninfo = json_decode($resobj, true); 
                        $result = transmitText($postObj, $jsoninfo["content"]);
                    }
                    break;
                case "image":
                    $mediaId = trim($postObj->MediaId);
                    $result = transmitImage($postObj, $mediaId); 
                    break;
                default:
                    $result = "unknow msg type:".$RX_TYPE;
                    break;
            }
            //打印输出的数据到log.xml
            Utils::logger($result, '公众号');
            echo $result;
        }else{
            Utils::logger("请求的xml为空！", '用户');
            exit;
        }
    }
 
    /**
     * 接收事件消息
     */
    function receiveEvent($object)
    {
        switch ($object->Event){
            //关注公众号事件
            case "subscribe":
                $content = "欢迎关注";
                break;
            case "CLICK":
                $content = $object->EventKey; // 获取key
                if($content=='qiushi'){
                   //返回内容
                   $str = file_get_contents("./idx");
                   $content=getData($str+1);
                   file_put_contents("./idx", $str+1);
                }else if($content=='sici'){
                   $data=Utils::getOnePoem();
                   Utils::logger("poem:".print_r($data,true));
                   $content=Utils::convertChinese($data['title']."\n"."\t\t\t\t作者: ".$data['author']."\n".$data['content']);
                }else if($content=='sicibig5'){
                   $data=Utils::getOnePoem();
                   Utils::logger("poem:".print_r($data,true));
                   $content=$data['title']."\n"."\t\t\t\t作者: ".$data['author']."\n".$data['content'];
                }else if($content=='juzi'){
                   $data=Utils::getOneJuzi();
                   Utils::logger("poem:".print_r($data,true));
                   $content= "\t".$data['content'];
                }else if($content=='miyu'){
                   $data=Utils::getOneCaimi();
                   Utils::logger("poem:".print_r($data,true));
                   $content= "\t".$data['content'];
                }
                break;
            case "MASSSENDJOBFINISH":
            
            default:
                $content = "";
                break;
        }
        $result = transmitText($object, $content);
        return $result;
    }
 
    /**
     * 回复文本消息
     */
    function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime><![CDATA[%s]]></CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    
    function transmitImage($object, $mediaId)
    {
        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime><![CDATA[%s]]></CreateTime>
            <MsgType><![CDATA[image]]></MsgType>
            <Image><MediaId><![CDATA[%s]]></MediaId></Image>
            </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $mediaId);
        return $result;
    }

    function transmitMusic($object, $title, $description, $url, $thumbId)
    {
        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime><![CDATA[%s]]></CreateTime>
            <MsgType><![CDATA[music]]></MsgType>
            <Music>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[%s]]></Description>
                <MusicUrl><![CDATA[%s]]></MusicUrl>
                <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
            </Music> 
            </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $title, $description, $url, $url, $thumbId);
        return $result;
    }



//消息处理
if (isset($_GET["echostr"])){
    checkSignature();
}else {
    Utils::logger("begin response"); 
    responseMsg();
}

?>
<?php


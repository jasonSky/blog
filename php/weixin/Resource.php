<?php
@header('Content-type: text/plain;charset=UTF-8');
require_once('./Utils.php');
require_once('./HttpUtils.php');

//https://blog.jasonsky.com.cn/php/weixin/Resource.php?str=addNew
$str=$_GET["str"];
if($str=="addImg"){
    print ResourceUtil::uploadMaterial("./img/a.jpeg", "image");
}else if($str=="addNew"){
    print ResourceUtil::uploadNews("bp2jLXpIOU1Sj-VTwS5xjIsTm6FCXGG1f8vZRarrhQs");
}else if($str=="list"){//资源list str=list&type=image
    $type = $_GET["type"]==""?"news":$_GET["type"];
    $result = ResourceUtil::getMaterialContent($type);
    print $result;
    $jsonObj = json_decode($result, true);
    foreach($jsonObj["item"] as $x){ 
        file_put_contents("./res_".$type, $x["media_id"]."\n", FILE_APPEND | LOCK_EX);     
    }
    
}else if($str=="delMaterial"){
    $media = $_GET["media"];
    print ResourceUtil::delMaterial($media);
}else if($str=="getMaterial"){
    $media = $_GET["media"];
    print ResourceUtil::getMaterial($media);
}else if($str=="addTempImgs"){
    print ResourceUtil::uploadTempImgs("img/favicon.png");
}else if($str=="addTempFile"){
    $arr = array("a","b","c","d","e","f","g","h");
    foreach($arr as $name){
        print ResourceUtil::uploadTempResource("./img/".$name.".jpeg");
    }
}else if($str=="view"){//查看临时资源
    $media = $_GET["media"];
    print  ResourceUtil::getMediaFile($media);
}else{//所有资源计数
    print ResourceUtil::getMaterialcount();
}

class ResourceUtil
{
    public static $header = array('Content-Type: multipart/form-data');//关键一步
    public static $mediaFile = "./media";
    //上传临时素材 - 媒体文件在微信后台保存时间为3天
    //分别有图片（image）、语音（voice）、视频（video），普通文件(file) 
    //图片（image）:2MB，支持JPG,PNG格式
    //语音（voice）：2MB，播放长度不超过60s，支持AMR格式
    //视频（video）：10MB，支持MP4格式
    //普通文件（file）：20MB
    public static function uploadTempResource($path, $type=image)
    {
        if (class_exists('CURLFile')) {
                //PHP5.5及以上
                //realpath返回绝对路径
                $filedata = array('media' => new CURLFile(realpath($path)));
        } else {
                //PHP5.4及以下
                $filedata = array('media' => '@'.realpath($path));
        }
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".Utils::getToken()."&type=".$type;
        $result = https_request($url, $filedata, self::$header);
        Utils::logger("uploadTempResource");
        Utils::logger($result);
        $jsoninfo = json_decode($result, true);
        $mid = $jsoninfo["media_id"];
        file_put_contents("./temp_".$type, $mid."\t".date('YmdHis')."\n", FILE_APPEND | LOCK_EX);
        return $result;
    }
    
    //上传多张图片
    //上传的图片不占用公众号的素材库中图片数量的5000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下
    public static function uploadTempImgs($str)
    {
        $url= "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".Utils::getToken();
        $pathArr = explode(",", $str);//分割字符串
        $resultArr = array();
        forEach ($pathArr as $path)
        {
            if (class_exists('CURLFile')) {
                //PHP5.5及以上
                //realpath返回绝对路径
                $filedata = array('media' => new CURLFile(realpath($path)));
            } else {
                //PHP5.4及以下
                $filedata = array('media' => '@'.realpath($path));
            }
            $result = https_request($url, $filedata, self::$header);
            array_push($resultArr, $result);
        }
        Utils::logger("uploadTempImgs");
        $resultStr = var_export($resultArr,true);
        Utils::logger($resultStr);
        return $resultStr;
    }

    //获取临时素材消息
    public static function getTempFile($media_id)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".Utils::getToken()."&media_id=".$media_id;
        $fileInfo = request_image($url);
        $filename = date('Y-m-dHis').".jpg";
        //$fileInfo["body"]保存了图片的内容
        Utils::save_weixin_file($filename, $fileInfo["body"]);
    }
    
    //上传永久素材 
    //媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
    //"images/test.jpg"
    public static function uploadMaterial($str, $type)
    {
        if (class_exists('\CURLFile')) {
            //PHP5.5及以上
            //realpath返回绝对路径
            echo realpath($str);
            $filedata = array('media' => new \CURLFile(realpath($str)));
        } else {
            //PHP5.4及以下
            //echo "xxx:".realpath($str);
            $filedata = array('media' => '@'.realpath($str));
        }        
        $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".Utils::getToken()."&type=$type";
        //var_dump($filedata);
        $result = https_request($url, $filedata, self::$header);
        Utils::logger("uploadMaterial");
        Utils::logger($result);
        $jsoninfo = json_decode($result, true);
        $mid = $jsoninfo["media_id"];
        file_put_contents(self::$mediaFile, $mid."\n", FILE_APPEND | LOCK_EX);
        //新增图片素材时返回
        //$img_src = $jsoninfo["url"];
        return $media_id;
    }
    
    public static function getMaterial($media_id)
    {
        $mediaId = '{"media_id": "'.$media_id.'"}';
        $url = "https://api.weixin.qq.com/cgi-bin/material/get_material?"."access_token=".Utils::getToken();
        $result = https_request($url, $mediaId);
        Utils::logger($result);
        return $result;
    }
    
    public static function delMaterial($mid)
    {
        $mediaId = '{"media_id": "'.$mid.'"}';
        $url = "https://api.weixin.qq.com/cgi-bin/material/del_material?"."access_token=".Utils::getToken();
        $result = https_request($url, $mediaId);
        Utils::logger($result);
        file_put_contents(self::$mediaFile, $mid."\n ---", FILE_APPEND | LOCK_EX);    
        return $result;
    }
    
    //上传图文消息
    public static function uploadNews($mediaId)
    {
        $newsjson = '{"articles": [
            {
                "title": "美图查看",
                "thumb_media_id": "'.$mediaId.'",
                "author": "jason",
                "digest": "美图",
                "show_cover_pic": 1,
                "content": "<p>美照1<p>
<img src=\"http://mmbiz.qpic.cn/mmbiz_jpg/VJGMJzaIUfWwRE43DvcFoj7d8xia2xeZ1tpvqmR7RRicXo6D4sZ5Wlibdh8ZuwDHy78K6tt8Cm1RXuFIBRiazHrJicA/0\" />
<p>美照2<p>
<img src=\"http://mmbiz.qpic.cn/mmbiz_jpg/VJGMJzaIUfWwRE43DvcFoj7d8xia2xeZ1u0ZpcJToykKbzHUQDujnpBCYTia6hwMgiasWiciaF2JxS4WoyrvVT5ichVg/0\" />
",
                "content_source_url": "https://blog.jasonsky.com.cn"
            }
        ]}';
        $url = "https://api.weixin.qq.com/cgi-bin/material/add_news?"."access_token=".Utils::getToken();
        Utils::logger($url);
        Utils::logger($newsjson);
        $result = https_request($url, $newsjson);
        Utils::logger($result);
        $jsoninfo = json_decode($result, true);
        $mid = $jsoninfo["media_id"];
        file_put_contents(self::$mediaFile, $mid."\n", FILE_APPEND | LOCK_EX);        
        return $result;
    }
    
    //更新图文消息
    public static function updateNews($mediaId)
    {
        $newsjson = '{
            "media_id": "'.$mediaId.'",
            "index": 0,
            "articles":
            {
                "title": "第一个图文永久素材",
                "thumb_media_id": "FrsRJ3g3BHR-pIkuFLARnHjI9Cq9lDFas4Kp8otlAUQ",
                "author": "Perter",
                "digest": "第一个图文永久素材摘要",
                "show_cover_pic": 1,
                "content": "123",
                "content_source_url": "http://www.baidu.com"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/material/update_news?"."access_token=".Utils::getToken();
        Utils::logger($url);
        $result = https_request($url, self::$newsjson);
        Utils::logger($result);
        return $result;
    }
    
    public static function getMaterialcount()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/material/get_materialcount?"."access_token=".Utils::getToken();
        $result = https_request($url);
        Utils::logger($result);
        return $result;
    }
    
    //素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
    public static function getMaterialContent($type)
    {
        $data = '{
            "type":"'.$type.'",
            "offset":0,
            "count":10
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?"."access_token=".Utils::getToken();
        Utils::logger($url);
        Utils::logger($data);
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }

}



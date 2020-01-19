<?php
@header('Content-type: text/plain;charset=UTF-8');
require_once('./Utils.php');
require_once('./HttpUtils.php');

$openid="oQbJC1BI8SN0IbNOeNGXHmRnQxfo";
$mediaid="bp2jLXpIOU1Sj-VTwS5xjPfrW2t_ktC18NjouI9rTsM";//news
$groupid="102";
$str = $_GET["str"];
if($str=="preview"){
    print SendFun::preview($openid, $mediaid);
    //SendFun::preview($openid, null, "link");
    //SendFun::preview($openid, "aaaaa", "text");
}else if($str=="all"){//不能发多媒体
    print SendFun::sendAll($groupid, $mediaid);
}else if($str=="delete"){
    print SendFun::delete("msgid"); //删除下发
}else if($str=="status"){
    print SendFun::getStatus("msgid");//下发状态
}else{//好像只能发text, image/link/news都报48008
    //print "xx";
    //print SendFun::send($openid, $mediaid);    
    //print SendFun::send($openid, "test", "text");
    print SendFun::send($openid, "bp2jLXpIOU1Sj-VTwS5xjOTIOctLGu10S1BDUD33gKk", "image");
}

class SendFun{
    public static $header = array('Content-Type: application/json');

    //文本消息{"touser":"OPENID","msgtype":"text","text":{"content":"Hello World"}}
    //图片消息{"touser":"OPENID","msgtype":"image","image":{"media_id":"MEDIA_ID"}}
    //图文消息{"touser":"OPENID","msgtype":"mpnews","mpnews":{"media_id":"11111sssss"}}
    //图文链接{"touser":"OPENID","msgtype":"link","link":{"title":"HappyDay","description":"IsReallyAHappyDay","url":"URL","thumb_url":"THUMB_URL"}}
    public static function send($openid, $mediaId, $type="mpnews") //touser
    {
        $msg = array('touser'=>array($openid,""));
        $msg['msgtype'] = $type;
        if($type=="text"){//text
            $msg[$type] = array('content'=> $mediaId);
        }else if($type=="link"){//link
            $msg[$type] = array("title"=>"博客","description"=>"JasonSky的个人博客","url"=>"https://blog.jasonsky.com.cn","thumb_url"=>"http://mmbiz.qpic.cn/mmbiz_png/VJGMJzaIUfWwRE43DvcFoj7d8xia2xeZ1qb14tnaSqrgGqS0cx6g2sd7bJjEicjice6oPicqwdMMibYsib552ToKwbZw/0");
        }else{//image + mpnews
            $msg[$type] = array('media_id'=> $mediaId);
        }
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?"."access_token=".Utils::getToken();
        Utils::logger(json_encode($msg));
        $result = https_request($url, json_encode($msg), self::$header);
        Utils::logger($result);
        return $result;
    }
    
    //tagid groupid
    //高级群发接口的每日调用限制为10次
    public static function sendAll($tagId,$mediaId, $type="mpnews")//filter
    {
        $msg = array('filter'=>array('is_to_all'=>false,'tag_id'=>$tagId));
        $msg['msgtype'] = $type;
        if($type=="text"){//text
            $msg[$type] = array('content'=> $mediaId);
        }else if($type=="link"){//link
            $msg[$type] = array("title"=>"博客","description"=>"JasonSky的个人博客","url"=>"https://blog.jasonsky.com.cn","thumb_url"=>"http://mmbiz.qpic.cn/mmbiz_png/VJGMJzaIUfWwRE43DvcFoj7d8xia2xeZ1qb14tnaSqrgGqS0cx6g2sd7bJjEicjice6oPicqwdMMibYsib552ToKwbZw/0");
        }else{//image + mpnews
            $msg[$type] = array('media_id'=> $mediaId);
        }
        Utils::logger(json_encode($msg));
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?"."access_token=".Utils::getToken();
        Utils::logger($url);
        $result = https_request($url, json_encode($msg), self::$header);
        Utils::logger($result);
        return $result;
    }
    
    //每天100次限制
    public static function preview($openid,$mediaId=null, $type="mpnews")//发送给某个用户进行preview
    {
        $msg = array('touser'=>array($openid));
        $msg['msgtype'] = $type;
        if($type=="text"){//text
            $msg[$type] = array('content'=> $mediaId);
        }else if($type=="link"){//link
            $msg[$type] = array("title"=>"博客","description"=>"JasonSky的个人博客","url"=>"https://blog.jasonsky.com.cn","thumb_url"=>"http://mmbiz.qpic.cn/mmbiz_png/VJGMJzaIUfWwRE43DvcFoj7d8xia2xeZ1qb14tnaSqrgGqS0cx6g2sd7bJjEicjice6oPicqwdMMibYsib552ToKwbZw/0");
        }else{//image + mpnews
            $msg[$type] = array('media_id'=> $mediaId);
        }
        Utils::logger(json_encode($msg));
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?"."access_token=".Utils::getToken();
        $result = https_request($url, json_encode($msg), self::$header);
        Utils::logger($result);
        return $result;
    }
    
    public static function delete($msgId)
    {
        $data = '{"msg_id":'.$msgId.'}';
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/delete?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }
    
    public static function getStatus($msgId)
    {
        $data = '{"msg_id":'.$msgId.'}';
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/get?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }

}

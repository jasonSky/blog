<?php
@header('Content-type: text/plain;charset=UTF-8');
require_once('./Utils.php');
require_once('./HttpUtils.php');

$str=$_GET["str"];
if($str=="addto"){
    $tagId=User::createLabel("相亲相爱");
    print User::addUserToLabel($tagId);
}else if($str=="tagsU"){//用户所在目录
    print User::getLablesFrom();
}else if($str=="usersU"){
    print User::getLableUsers(102);
}else if($str=="del"){
    print User::delLabel(101); 
}else if($str=="tag"){//所有组
    print User::getLabel();
}else{
    print User::getUserInfo();
}

class User
{
    public static $openId = "oQbJC1BI8SN0IbNOeNGXHmRnQxfo";

    public static function getUserInfo()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".Utils::getToken()."&openid=".self::$openId."&lang=zh_CN ";
        $result = https_request($url);
        Utils::logger($result);
        return $result;
    }
    
    public static function getUserMsg()
    {
            $data = '{
            "user_list": [
               {
                   "openid": "'.self::$openId.'",
                   "lang": "zh_CN"
               }
           ]
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }
    
    public static function createLabel($tagName)
    {
        $data = '{
            "tag" : {
                "name" : "'.$tagName.'"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        $jsoninfo = json_decode($result, true);
        $tagId = $jsoninfo["tag"]["id"];
        return $tagId;
    }
    
    public static function getLabel()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".Utils::getToken();
        $result = https_request($url);
        Utils::logger($result);
        return $result;
    }
    
    public static function updateLabel($id)
    {
        $data = '{
            "tag" : {
                "id" : '.$id.',
                "name" : "好朋友"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/update?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }
    
    public static function delLabel($id)
    {
        $data = '{
            "tag" : {
                "id" : '.$id.'
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/delete?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }
   
    public static function addUserToLabel($id)
    {
        $data = '{
            "openid_list" : [
                "'.self::$openId.'"
            ],
          "tagid" : '.$id.'
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }
    
    public static function getLableUsers($id)
    {
        $data = '{
          "tagid" : '.$id.',
          "next_openid":""
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/user/tag/get?"."access_token=".Utils::getToken();
        Utils::logger($data);
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }
    
    public static function getLablesFrom()
    {
        $data = '{
          "openid" : "'.self::$openId.'"
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/getidlist?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }
    
    public static function cancelUserLable($id)
    {
        $data = '{
            "openid_list" : [
                "'.self::$openId.'"
            ],
          "tagid" : '.$id.'
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }

    public static function blackUsers()
    {
        $data = '{
            "openid_list" : [
                "'.self::$openId.'"
            ]
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }

    public static function BlackListUsers()
    {
        $data = '{
            "begin_openid":""
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }

    public static function cancelBlackUser()
    {
        $data = '{
            "openid_list" : [
                "'.self::$openId.'"
            ]
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchunblacklist?"."access_token=".Utils::getToken();
        $result = https_request($url, $data);
        Utils::logger($result);
        return $result;
    }

}

 


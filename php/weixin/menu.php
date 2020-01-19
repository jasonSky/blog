<?php
@header('Content-type: text/plain;charset=UTF-8');
require_once('./Utils.php');
require_once('./HttpUtils.php');

$val = $_GET['val'];
if($val=="create" || $val=="add"){
   print MenuOperate::createMenu();
}else if($val=="del"){
   print MenuOperate::delMenu();
}else{
   print MenuOperate::queryMenu();
}

class MenuOperate
{ 

//{
                //    "type": "click",
                //    "name": "轻松一下",
                //    "key": "qiushi"
                //}, 
  //菜单字符串
  public static $menujson = '{
    "button": [
        {
            "name": "小功能",
            "sub_button": [
                {
                    "type": "click",
                    "name": "少儿益智2000问",
                    "key": "yizhi"
                },
                {
                    "type": "click",
                    "name": "文摘句子",
                    "key": "juzi"
                },
                {
                    "type": "click",
                    "name": "猜谜语",
                    "key": "miyu"
                },
                {
                    "type": "click",
                    "name": "古诗词",
                    "key": "sici"
                },
                {
                    "type": "click",
                    "name": "古诗词-繁体",
                    "key": "sicibig5"
                }
            ]
        },
        {
           "name": "网站",
           "sub_button":[
             {
             "type": "view",
             "name": "博客",
             "url": "https://blog.jasonsky.com.cn"
             },
             {
             "type": "view",
             "name": "相册",
             "url": "https://photo.jasonsky.com.cn"
             },
             {
             "type": "view",
             "name": "JSSDK",
             "url": "https://blog.jasonsky.com.cn/php/weixin/jssdk.php"
             }
           ]
        },
        {
             "type": "view",
             "name": "简介",
             "url": "https://info.jasonsky.com.cn"
        }

    ]
}';

  //定制化的菜单 - 匹配特定
  public static $subcribmenu = '{
	"button":[
 	{
    	"type":"click",
    	"name":"单击",
     	"key":"V1001_TODAY_MUSIC"
	}],
 "matchrule":{
  "sex":"1"
  }
}';

   public static function createMenu(){
      //创建菜单
      $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".Utils::getToken();
      $result = https_request($url, self::$menujson);
      return $result;
   }
   
   public static function queryMenu(){
      $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".Utils::getToken();
      //查询菜单
      $result = https_request($url);
      return $result;
   }
   
   public static function delMenu(){
      $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".Utils::getToken();
      //删除菜单
      $result = https_request($url);
      return $result;
   }

   ////////定制化菜单
   public static function createConditionMenu()
   {
	$url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=".Utils::getToken();
	//创建个性化菜单
	$result = https_request($url, self::$subcribmenu);
	//返回返回menuid表示成功
	return $result;
   }

   public static function delConditionMenu($menuid) 
   {
	$url = "https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=".Utils::getToken();
	//menuid，个性化菜单的menuid
	$menuID = '{"menuid":'.$menuid.'}';
	$result = https_request($url, $menuID);
	return $result;
   }

   public static function trymatchConditionMenu()
   {
	$url = "https://api.weixin.qq.com/cgi-bin/menu/trymatch?access_token=".Utils::getToken();
	//user_id可以是粉丝的OpenID，也可以是粉丝的微信号
	$userID = '{"user_id":"jasonttms"}';
	$result = https_request($url, $userID);
	return $result;
   }

}

<?php
require_once("Utils.php");
$signPackage = Utils::GetSignPackage();
@header("Content-Type:text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
</head>
<body>
<button id="qrcode" onclick="scanCode()" style="width:200px;height:80px" ><b>扫 码</b></button>
<div id="result"></div>
</body>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  // 注意：所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。 
  // 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
  // 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html';

  wx.config({
    debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
        'onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ',  
                            'onMenuShareWeibo','onMenuShareQZone','chooseImage',  
                            'uploadImage','downloadImage','startRecord','stopRecord',  
                            'onVoiceRecordEnd','playVoice','pauseVoice','stopVoice',  
                            'translateVoice','openLocation','getLocation','hideOptionMenu',  
                            'showOptionMenu','closeWindow','hideMenuItems','showMenuItems',  
                            'showAllNonBaseMenuItem','hideAllNonBaseMenuItem','scanQRCode'
    ]
  });
  wx.ready(function () {//用户主动触发的动作不用写在这里
    // 在这里调用 API
  });
  wx.error(function(res){
    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体
    //错误信息可以打开config的debug模式查看，也可以在返回的res参数中查
    //看，对于SPA可以在这里更新签名。
  });

function scanCode() {
    wx.scanQRCode({
        needResult:1,
        scanType: ["qrCode","barCode"],
        success:function (res) {
            console.log(res)
            var result = res.resultStr;
            document.getElementById("result").innerHTML="<a>"+result+"</a>"; 
            //setTimeout(callback(result),500);
        },

        error:function(res){
            alert(JSON.stringify(res))
            if(res.errMsg.indexOf('function_not_exist') >0){
                alert('版本过低请升级')
            }
        }
    });
}  


</script>
</html>


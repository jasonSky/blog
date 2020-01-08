<?php
@header('Content-type: text/plain;charset=UTF-8');

function https_request($url, $data = null, $header = null)
{
    $curl = curl_init();
    if (class_exists ( '/CURLFile' )) {
        //php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同  
        curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, TRUE );  
    } else {  
        if (defined ( 'CURLOPT_SAFE_UPLOAD' )) {  
            curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, FALSE );  
        }  
    }  
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if (!empty($header)){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function https_requesth($url, $data = null, $header = null)
{
    $curl = curl_init();
    if (class_exists ( '/CURLFile' )) {
        //php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
        curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, TRUE );
    } else {
        if (defined ( 'CURLOPT_SAFE_UPLOAD' )) {
            curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, FALSE );
        }
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if (!empty($header)){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


function https_get($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function request_image($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_NOBODY, 0); //只取body头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //$package是返回的图片
    $package = curl_exec($ch);
    //$httpinfo 响应头
    $httpinfo = curl_getinfo($ch);
    curl_close($ch);
    //将body和响应头合并到一个数组
    $imageAll = array_merge(array("header"=>$httpinfo), array("body"=> $package));
    return $imageAll;
}

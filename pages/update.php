<?php
/**
 * Template name: 安卓App用户更新
 * Description:   Android app update
 */
header('Access-Control-Allow-Origin: *');//允许访问的域名
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header("content-type:text/javascript;charset=utf-8");
$updatepage_appid = get_option('updatepage_appid');//获取数据库中的变量值
$updatepage_version = get_option('updatepage_version');
$updatepage_status = get_option('updatepage_status');
$updatepage_title = get_option('updatepage_title');
$updatepage_note = get_option('updatepage_note');
$updatepage_appurl = get_option('updatepage_appurl');
$updatepage_level = get_option('updatepage_level');
if(empty($_GET['version']) || empty($_GET['version'])){
 $rsp = array('status' => 0);//默认返回值，不需要升级
 exit(json_encode($rsp));
}
if($_GET['version'] != $updatepage_version || $_GET['version'] > $updatepage_version ){//客户端版本号   
        if($version != "$updatepage_version"){  //判断客户端版本是否符合最新
            $rsp['status'] = "$updatepage_status";  //内部版本号
            $rsp['title'] = "$updatepage_title";  //更新标题
            $rsp['note'] = "$updatepage_note";//更新内容（release notes），支持以\n形式换行  
            $rsp['url'] = "$updatepage_appurl";//应用升级包下载地址  
            $rsp['level'] = "$updatepage_level";
        }    
}else{
$rsp = array('status' => 0);//默认返回值，不需要升级 
}	
exit(json_encode($rsp));
?>
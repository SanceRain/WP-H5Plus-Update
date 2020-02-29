# WP H5Plus Update
## 简介
通过Wordpress，dcloud旗下的h5+应用（包括但不限于5+、wap2app等格式）可以借助此插件灵活地在线检测新版本。
插件由[山茨昕雨](https://sancerain.com)开发并开源，使用MIT协议进行分发。
## 快速上手
### 配置参数
在阁下Wordpress网站后台中，进入 设置>APP参数修改，按需修改以下参数：

 - **应用ID**
 - **应用版本号**
 - **内部版本号**
 - **更新标题**
 - **更新日志**
 此项目可用php换行符“\n”对文本进行换行
 - **下载链接**
 - **重要性**
 此项目分两种，字符“1”为重要，字符“0”为不重要

以上参数中部分可以在你的应用源码中的mainfest.json文件中查看，保存更新后需要手动更新页面来查看你配置的参数
### 新建页面
在Wordpress中新建一个页面，选择页面类型为“安卓更新接口模板”，设置好Url即可开始请求
### 调试页面
在阁下的Url后面添加并修改以下字符：?appid=**阁下的应用ID**&version=**阁下的应用版本号**
例如：https://exmaple.com/check/update?appid=__W2A__exmaple.com&version=1.0
## 客户端配置
JavaScript代码分两种模式，本代码仅包含Core，配置时请更改检查更新地址，其他业务实现请自行编辑代码。
### 开屏自动更新

    var ua = navigator.userAgent;
    //Html5Plus环境，但不是流应用环境  
    if (ua.indexOf('Html5Plus') > -1 && ua.indexOf('StreamApp') == -1) {
    	var url = "https://exmaple.com/check/update"; //检查更新地址  
    	var req = { //升级检测数据
    		"appid": plus.runtime.appid,
    		"version": plus.runtime.version
    	};
    	wap2app.ajax.get(url, req, function(rsp) {
    		if (rsp.level == 1) { //判断是否重要（是1就更新
    			if (rsp && rsp.status) {
    				//需要更新，提示用户
    				plus.nativeUI.confirm(rsp.note, function(event) {
    					if (0 == event.index) { //用户点击了“立即更新”按钮  
    						plus.runtime.openURL(rsp.url);
    					}
    				}, rsp.title, ["立即更新", " ", "取消"]);
    			}
    		}
    	});
    }

该模式仅在App更新级别为重要时才会进行更新
### 关于页检测更新

    var ua = navigator.userAgent;
    //Html5Plus环境，但不是流应用环境  
    if (ua.indexOf('Html5Plus') > -1 && ua.indexOf('StreamApp') == -1) {
    	var url = "https://exmaple.com/check/update"; //检查更新地址  
    	var req = { //升级检测数据  
    		"appid": plus.runtime.appid,
    		"version": plus.runtime.version
    	};
    	wap2app.ajax.get(url, req, function(rsp) {
    		if (rsp && rsp.status) {
    			//需要更新，提示用户  
    			plus.nativeUI.confirm(rsp.note, function(event) {
    				if (0 == event.index) { //用户点击了“立即更新”按钮  
    					plus.runtime.openURL(rsp.url);
    				}
    			}, rsp.title, ["立即更新", " ", "取消"]);
    		} else {
    			plus.nativeUI.toast("没有可用的版本更新");
    		}
    	});
    }

该模式可以检查最新级别的更新，如无更新会弹出原生提示
## 版权信息
本协议采用MIT进行分发，项目官方地址为：https://github.com/sancerain/wp-h5plus-update
项目作者：[@finderz](https://github.com/xtgjzwj)&[@江程训](https://github.com/censujiang)
所属组织：廊坊市山茨网络科技有限公司 Sancerain LLC
> 使用 [StackEdit](https://stackedit.io/)进行Markdown编辑
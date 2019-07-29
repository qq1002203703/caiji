<?php
//系统必须配置项
return array(
    //'OPEN_RESTFUL'=>false,
	//时区
    'timezone'=>'Asia/Shanghai',
	//路由默认相关项
	'router'=>[
		'module'=>'portal',
		'ctrl'=>'index',
		'action'=>'index',
		'suffix'=>'.html'
	],
    //微信公众号配置
    'wechat' => [
        'app_id' => 'wx0e89ec295bc43f2a',
        'secret' => 'e7dd4502826fd0f32ddecb62e5a43f7a',
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',
        'token'=>'kssddRR4565655Jsulll65EWW5',
        'log' => [
            'level' => 'debug',
            'file' => ROOT.'/cache/wechat/wechat.log',
        ],
    ],
    //缓存种类
    'cache_type'=>'file',
);
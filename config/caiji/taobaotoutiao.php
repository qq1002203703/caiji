<?php
//淘宝头条
return [
    'options'=>[
        'table'=>'taobaotoutiao',
        'downloadTable'=>'taobaotoutiao_more',
        'callback'=>'\core\caiji\driver\Page'
    ],
    'page'=>[
        //'name'=>'taobaotoutiao',//string|int,rule_id 采集规则id
        //'url'=>'',//string 起始页url
        'isloop'=>true,//bool 是否循环拖动
        'nextPage'=>'', //string 定位下一页按钮的css，默认为空不用点击分页
        'nextPageInterval'=>1,//string 间隔多少次点一下nextPage,默认1次，注：只有nextPage不为空时，此项才起作用
        'scroll'=>false,  //是否要慢慢滚动到底部，默认false即一下子滚动到底部
        'iscookie'=>false,//是否要读取文件的cookie
        'cookieFile'=>'',//cookie文件名，如果为空会被$this->setCookie()设为cache/caiji/cookie/{$caijiRuleId}.txt
        //chrome浏览器设置项
        'chrome'=>[],
        'outType'=>2,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plug_save'=>'\shell\caiji\plugin\Taobaotoutiao@pageSave',
        'plug_befor_selector'=>'',
        'reTimes'=>0,
        'rule'=>[
            'type'=>'reg',//匹配方式：分别为'reg'、'xpath'和'json'
            'cut'=>'',//截取中间内容
            'reg'=>'#<div class="image group">\s*<div class="grid images_3_of_1">\s*<a href="([^">]+)" title="[^">]+" target="_blank"><img src="([^">]+)"[^>]*></a>\s*</div>#i',//标签匹配正则
            'tags'=>['url'],//标签
            'filter'=>[
                //'url'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>','union{%|||%}http://www.aaa.com/{%xxoo%}']
                'url'=>['trueurl{%|||%}0'],
            ],
            'notEmpty'=>['url'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
            /*'file'=>[
                'thumb'=>[
                    'type'=>'2',//种类：1=>为图片，2=>为文件
                    'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                ]
            ],*/
        ]
    ],

];
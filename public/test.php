<?php

define('ROOT',realpath(__DIR__.'/../'));

include ROOT.'/system/helper.php';
include ROOT.'/system/core/Container.php';
include ROOT.'/system/extend/Helper.php';
include ROOT.'/system/core/Conf.php';
$options=[

];
echo "\n\n";
echo '---------------------------------------------------------------------------';
echo "\n";
$page=[
    'type'=>1,//1单层,2,多层，3单页循环
    'retimes'=>0,//重复多少次停止
    //第0项：0为公差，1为公比，2字母，第1项：开始页数，第2项：总页数，第3项：步进（公差或公比数），第4项是否不倒转(默认倒转),第5项：是否补零(默认不补)，
    'page_url'=>'http://www.anyv.net/index.php/categoryyuedu-19-page-{%0,1,10,1,0,0%}',
    'plugin_befor'=>'',
    'plugin_after'=>'',
    'plugin_single'=>'',
    'rules'=>[
        [
            'type'=>'reg',//匹配方式：分别为'reg'、'xpath'和'json'
            'cut'=>'/<h1>([\s\S]+?)<div id="content-pagenation">/i',//截取中间内容
            'list_area'=>'',//循环分割区域
            'reg'=>'#<div class="image group">\s*<div class="grid images_3_of_1">\s*<a href="([^">]+)" title="[^">]+" target="_blank"><img src="([^">]+)"[^>]*></a>\s*</div>#i',//标签匹配正则
            'tags'=>['source','thumb'],//标签
            'plugin'=>[//使用插件
                'befor'=>'',
                'after'=>''
            ],
            //过滤器，如果tags为空，此项是一唯数组，否则为二维数组
            'filter'=>[
                //'url'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>','union{%|||%}http://www.aaa.com/{%xxoo%}']
                'source'=>['trueurl{%|||%}0'],
                'thumb'=>['trueurl{%|||%}0'],
            ],
            'not_empty'=>['source'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
            'file'=>[
                'thumb'=>[
                    'type'=>'2',//种类：1=>为图片，2=>为文件
                    'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                ]
            ],
            /* 'multi_page'=>[ //采集分页，如果不采集分页可以删除此项
                 'isdo'=>false,//是否要采集分页,必须声明此项为真才会采集分页
                 'max'=>0,//最大页数，0为不限制
                 'type'=>'reg',
                 'cut'=>'',
                 'reg'=>'',
                 'filter'=>['trueurl{%|||%}0'],
                 'tags'=>'',
                 'not_empty'=>true,
             ]*/
        ],
    ],
    'curl'=>[
        'setting'=>[
            'login'=>false,
            //'match'=>'',
            'timeOut'=>[7,15],
            //'tryTimes'=>3
        ],
        'options'=>[
            'opt'=>[
                //CURLOPT_REFERER=>'htttps//:www.baidu.com',
            ],
            //'cookieFile'=>'',
            //'proxy'=>[],
            'method'=>'get',
            //'header'=>[],
        ]
    ]
];
echo '$page=>'.PHP_EOL. PHP_EOL.json_encode($page,JSON_UNESCAPED_SLASHES);
echo "\n\n";
echo '---------------------------------------------------------------------------';
echo "\n";
$content=[
    //格式：'类名@方法名'、'类名::静态方法名'、'函数名'，在未开始采集前，传递参数：array,各项原始数据记录; 返回值：array
    'plugin_all_before'=>'',
    //格式：'类名@方法'，各项都采集完后、入库前，传递参数：array,各项数据; 返回值：array \shell\task\caiji\plugin\PluginCommon@task001
    'plugin_all_after'=>'',
    //保存数据时调用
    'plugin_save'=>'',
    'caiji'=>[
        'content'=>[
            //是否不能为空
            'not_empty'=>true,
            //为采集时的规则
            'match'=>[
                'from'=>'html',// 'html'源码中匹配,'page'列表页采集,url'网址中匹配，'tags'标签组合,'fixed'固定值,'function'函数求值， 默认从html,
                /**
                 * from取'tags'、'fixed'或'function',本项才起作用
                 * tags时:例如：取title和content两个标签组合，'{%title%}{%content%}'，标签的两头还可以附加其他任意固定的字符串
                 * fixed时：任意固定的字符串
                 * function时：格式：'函数名|||参数1@参数2[...@参数]',例如要返回现在的日期和时间  'date|||Y-m-d h:i:s'
                 */
                'remak'=>'',
                // 数组：截取两个字符间的内容 格式 ['开始的字符串','结尾的字符串']，不使用本项可以删除本项、留空或空数组都可以
                'cut'=>'',
                /**
                 * 字符串：第一项填正则
                 *  '/<a[^<>]*?href="([\s\S]+?)"[^<>]*?\>/i'
                 */
                'reg'=>'#<div class="rich_media_content " id="js_content">([\s\S]+?)</div>\s+<script nonce=#i',
            ],
            //是否开启循环匹配
            'is_loop'=>false,
            //循环匹配规则
            'loop'=>[],
            //是否开启多分页采集
            'is_multipage'=>false,
            //多分页规则
            'multipage'=>[],
            /**
             * 是否使用插件， 留空为不使用，支持两个地方的插件，['before'=>'类名@方法名','after'=>'类名@方法名']
            'before'=>'pluginName'  刚采集完未开始处理数据前; 传递参数：获取到的原始html，返回值：string,处理好的html
            'after'=>'pluginName' 在每项刚匹配完后、未过滤前; 传递参数：各项单独数据; 返回值:string,处理完各项单独数据
             */
            'plugin'=>[],
            /**
             * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
             * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
             */
            'filter'=>['html{%|||%}<p><br>','reg{%|||%}/<p [^>]*>/i{%|||%}<p>','reg{%|||%}/<br [^>]*>/i{%|||%}<br>','reg{%|||%}/\s{2,}/{%|||%} ','reg{%|||%}#<p>(\s*|<br>)*</p>#{%|||%}','reg{%|||%}/\s{2,}/{%|||%} '],
            'files'=>[
                'type'=>'0',//种类：1=>为图片，2=>为文件
                //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                //'pre_url'=>''//链接网址前缀
            ]
        ],
        'title'=> [
            //是否不能为空
            'not_empty'=>true,
            //为采集时的规则
            'match'=>[
                'from'=>'html',
                'remak'=>'',
                // 数组：截取两个字符间的内容 格式 ['开始的字符串','结尾的字符串']，不使用本项可以删除本项、留空或空数组都可以
                'cut'=>'',
                'reg'=>'#<h1>(.+)</h1>#i',
            ],
            //是否开启循环匹配
            'is_loop'=>false,
            //循环匹配规则
            'loop'=>[],
            //是否开启多分页采集
            'is_multipage'=>false,
            //多分页规则
            'multipage'=>[],
            'plugin'=>[],
            /**
             * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
             * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
             */
            'filter'=>['html{%|||%}<p><img>','reg{%|||%}/<p [^>]*>/i{%|||%}<p>','reg{%|||%}/\s{2,}/{%|||%} '],
            'files'=>[
                'type'=>'0',//种类：1=>为图片，2=>为文件
                //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                //'pre_url'=>''//链接网址前缀
            ]
        ],
        'weixinhao'=> [
            //是否不能为空
            'not_empty'=>false,
            //为采集时的规则
            'match'=>[
                'from'=>'html',
                'remak'=>'',
                // 数组：截取两个字符间的内容 格式 ['开始的字符串','结尾的字符串']，不使用本项可以删除本项、留空或空数组都可以
                'cut'=>'',
                'reg'=>'#<ul class="user_group">\s+<li>\s*<a [^>]+>(?:.+):([^<]+)</a></li>#i',
            ],
            //是否开启循环匹配
            'is_loop'=>false,
            //循环匹配规则
            'loop'=>[],
            //是否开启多分页采集
            'is_multipage'=>false,
            //多分页规则
            'multipage'=>[],
            'plugin'=>[],
            /**
             * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
             * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
             */
            //'filter'=>['html{%|||%}<p><img>','reg{%|||%}/<p [^>]*>/i{%|||%}<p>','reg{%|||%}/\s{2,}/{%|||%} '],
            'files'=>[
                'type'=>'0',//种类：1=>为图片，2=>为文件
                //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                //'pre_url'=>''//链接网址前缀
            ]
        ],
        'gongzhonghao'=> [
            //是否不能为空
            'not_empty'=>false,
            //为采集时的规则
            'match'=>[
                'from'=>'html',
                'remak'=>'',
                // 数组：截取两个字符间的内容 格式 ['开始的字符串','结尾的字符串']，不使用本项可以删除本项、留空或空数组都可以
                'cut'=>'',
                'reg'=>'#<meta name="author" content="(.+?)微信公众号"/>#',
            ],
            //是否开启循环匹配
            'is_loop'=>false,
            //循环匹配规则
            'loop'=>[],
            //是否开启多分页采集
            'is_multipage'=>false,
            //多分页规则
            'multipage'=>[],
            'plugin'=>[],
            /**
             * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
             * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
             */
            //'filter'=>['html{%|||%}<p><img>','reg{%|||%}/<p [^>]*>/i{%|||%}<p>','reg{%|||%}/\s{2,}/{%|||%} '],
            'files'=>[
                'type'=>'0',//种类：1=>为图片，2=>为文件
                //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                //'pre_url'=>''//链接网址前缀
            ]
        ],
        'thumb' => [
            //是否不能为空
            'not_empty'=>false,
            //为采集时的规则
            'match'=>[
                'from'=>'page',
            ],
        ],
    ],
    'curl'=>[
        'setting'=>[
            'login'=>false,
            //'match'=>'',
            'timeOut'=>[7,15],
            //'tryTimes'=>3
            'opt'=>[
                //CURLOPT_COOKIE=>''
            ]
        ],
        'options'=>[
            'opt'=>[
                //CURLOPT_REFERER=>'htttps//:www.baidu.com',
            ],
            //'cookieFile'=>'',
            //'proxy'=>[],
            'method'=>'get',
            //'header'=>[],
        ]
    ],
    'table'=>'weixinqun',
];
echo '$content=>'.PHP_EOL.PHP_EOL.json_encode($content,JSON_UNESCAPED_SLASHES);
echo "\n\n";
echo '---------------------------------------------------------------------------';
echo "\n";
$download=[
    'save_path'=>'public/uploads/images/gzh',
    'replace_path'=>'{%Y%}/{%m%}/{%d%}/{%u%}',
    'plugin_after'=>'\shell\task\caiji\plugin\PluginCommon@task001_down',
    'curl'=>[
        'setting'=>[
            'login'=>false,
            //'match'=>'',
            'timeOut'=>[7,25],
            //'tryTimes'=>3
            'opt'=>[
                //CURLOPT_COOKIE=>''
            ]
        ],
        'options'=>[
            'opt'=>[
                //CURLOPT_REFERER=>'htttps//:www.baidu.com',
            ],
            //'cookieFile'=>'',
            //'proxy'=>[],
            'method'=>'get',
            //'header'=>[],
        ]
    ],
];
echo '$download=>'.PHP_EOL.PHP_EOL.json_encode($download,JSON_UNESCAPED_SLASHES);
echo "\n\n";
echo '---------------------------------------------------------------------------';
echo "\n";

$fabu=[
    'run_max'=>10,//每次发布的条数，0表示不限制
];
echo '$fabu=>'.PHP_EOL.PHP_EOL. json_encode($fabu,JSON_UNESCAPED_SLASHES);
echo "\n\n";
echo '---------------------------------------------------------------------------';
echo "\n";













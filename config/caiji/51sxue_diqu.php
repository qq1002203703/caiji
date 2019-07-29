<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * zuanke8.com 赚客吧采集规则
 * 评论过少的去掉  没什么用
第一次采  未采集的且未终结的
更新时    已采集的且未终结的

什么时候终结：1）3次都采集不到东西    2）主贴发布超过1个月
哪些帖子是没用的：1）超过1个月 没有评论的    2）手动审核
哪些帖子能发布：已经终结的，状态是有用的
哪些帖子需要手动审核：
 * ======================================*/
return [
    'options'=>[
        'table'=>'caiji_51sxue',
        'downloadTable'=>'caiji_51sxue_download',
        'callback'=>'\core\caiji\normal\\',
        'http'=>[
            'curl'=>[
                'tryTimes'=>3,
                'login'=>false,
                'match'=>'',
                'cookieFile'=>'',
                'header'=>[],
                'opt'=>[
                    CURLOPT_TIMEOUT=>15,//下载时应该按目标文件大小设置大一点
                    CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                    CURLOPT_REFERER=>'http://xuexiao.51sxue.com/',
                ]
            ],
            'options'=>[
                'proxy'=>[], //代理ip 端口 种类 格式 ['ip'=>'8.8.8.5','port'=>80,'type'=>'http']
                'checkProxyPlugin'=>'',//检测代理ip的插件
                'getProxyPlugin'=>'', //获取代理ip的插件
                //'checkResultPlugin'=>'\shell\tools\Xuexiao::check_result_page',//检测结果是否正常
                'isProxy'=>false, //是否使用代理ip访问
                'isProxyFix'=>false, //是否使用固定的代理ip
                'ipExpirationTime'=>280, //ip过期时间 单位秒
                'curlTimeInterval'=>800, //curl每次访问的最小时间间隔 单位毫秒
                'isRandomUserAgent'=>false, //是否使用随机ua
                'isAutoReferer'=>false, //是否需要自动获取来路
                'waitNoProxy'=>30, //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
                'waitIpLock'=>1000*60*5, //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
                'encoding'=>'GB2312', //指定源网页的编码
            ],
            'method'=>'get',
            'data'=>[],
        ]
    ],
    'page'=>[
        'type'=>1, //种类
        'retimes'=>0,//重复多少次停止
        'outType'=>1,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plugBeforeCaiji'=>'',
        'plugBeforeSelector'=>'',
        'plugCheckLogin'=>'',
        'curl'=>[],
        'rules'=>[
            [
                'type'=>'regex,map',//匹配方式
                'selector'=>'%<div id="list\d+" class="reply_box"[^>]+>(?P<area>[\s\S]+?)</ul>%',//区域匹配正则
                'tags'=>[//每个标签匹配正则
                    'url'=>'%<h3><a href="(?P<url>[^"]+)"%',
                    'from_id'=>'%<h3><a href="http://xuexiao\.51sxue\.com/detail/id_(?P<from_id>\d+).html"%',
                    'title'=>'%<h3><a href="[^"]+" title="(?P<title>[^"]+)"%',
                    'shuxing'=>'%<li>属性:<b>(?P<shuxing>[^<]+)</b></li>%',
                    'xingzhi'=>'%<ol>\s+性质:<b>(?P<xingzhi>[^<]+)</b>\s+</ol>%',
                    'leixing'=>'%<ol>\s+类型:<b>(?P<leixing>[^<]+)</b>\s+</ol>%'
                ],
                'cut'=>'<div class="school_main">{%|||%}<div class="school_page">',//截取中间内容
               /* 'selector'=>'%<ul>(?P<area>[\s\S]+?)</ul>%',//区域匹配正则
                'tags'=>[//每个标签匹配正则
                    'url'=>'%<div class="pl_shool_l">对\s*<a href="(?P<url>[^"]+)" target="_blank">%',
                    'from_id'=>'%<div class="pl_shool_l">对\s*<a href="http://pinglun\.51sxue\.com/school/comment/id_(?P<from_id>\d+)\.html" target="_blank">%',
                    'title'=>'%<div class="pl_shool_l">对\s*<a [^>]+>(?P<title>[^<]+)</a>%',
                ],
                'cut'=>'<div class="sxue_pl_l">{%|||%}<div class="sxue_pl_r">',//截取中间内容*/
                'filter'=>[
                    //'url'=>['replace{%|||%}http://pinglun.51sxue.com/school/comment/{%|||%}http://xuexiao.51sxue.com/detail/']
                ],
                'notEmpty'=>['url','from_id','title'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
            ],
        ]
    ],
    'content'=>[
        //格式：'类名@方法名'、'类名::静态方法名'、'函数名'，在未开始采集前，传递参数：array,各项原始数据记录; 返回值：array
        'pluginBefore'=>'',
        //格式：'类名@方法'，各项都采集完后、入库前，传递参数：array,各项数据; 返回值：array \shell\task\caiji\plugin\PluginCommon@task001
        'pluginAfter'=>'',
        //'isdownload'=>0, //特殊项，改变下载状态
        //保存数据时调用
        'pluginSave'=>'',
        'caiji'=>[
            'diqu'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>[],
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,multi',
                    'remak'=>'',
                    'selector'=>'%<li>\s*<a href="http://xuexiao\.51sxue.com/slist/\?t=\d+&areaCodeS=(?P<area_id>\d+)" target="_blank">(?P<area_name>[^<]+)</a> >\s*</li>%',
                    'tags'=>'area_id,area_name',//type=reg或regm时必填，type=cut时可以为空,
                     'cut'=>'<li>位置:</li>{%|||%}<ol class=',
                ],
                //是否开启循环匹配
                'isLoop'=>true,
                //是否开启多分页采集
                'isMultipage'=>false,
                //多分页规则
                'multiPage'=>[],
                'plugin'=>[],
                /**
                 * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
                 * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
                 */
                'filter'=>[
                    'area_name'=>[
                        'trim{%|||%}x'
                    ],
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
        ],
        'curl'=>[],
    ],
    'download'=>[
        'save_path'=>'uploads/images/xuexiao',
        'replace_path'=>'{%Y%}/{%m%}/{%d%}/{%u%}',
        'plugin_after'=>'',
        'plugin_before'=>'',
        'date_from'=>'',//时间取自哪个标签
        'tryTimes'=>4,//下载失败重试次数
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
    ],

    'fabu'=>[
        'run_max'=>0,//每次发布的条数，0表示不限制
        'plug'=>[],//插件
        'target'=>'http://www.wezhubo.my/portal/fabu/start?pwd=Djidksl$$EER4ds58cmO', //发布的目标
        'fields'=>'update_time,create_time',
        //'where1'=>' WHERE isfabu=0 and iscaiji=1 and isdownload=1 and isend=1 and isshenhe=1 and islaji=0',
        'where1'=>' WHERE isfabu=0 and iscaiji=1 and isdownload=1 and islaji=0 order by from_id',
        'where2'=> [['isfabu','eq',0],['iscaiji','eq',1],['isdownload','eq',1],['islaji','eq',0]],
    ],
    'getlist'=>[
        'url'=>'http://www.lovehzb.com/1.html',
        'plug'=>'\shell\caiji\plugin\Getlist::wezhubo',
        'rule_00'=>[
            'type'=>'regex,multi',//匹配方式
            'selector'=>'#<a href="(?P<url>[^"]+)" class="pic1-box">#',//标签匹配正则
            'tags'=>['url'],//标签
            'cut'=>'<ul class="page-list clearfix">{%|||%}</ul>',//截取中间内容
            'filter'=>[],
            'notEmpty'=>['url'],
        ],
        'rule_01'=>[
            /**
             * 支持多个，每行一个，格式：http://www.xxx.com/archiver/?fid-15.html&page={%0,1,40236,1,1,0%}
            {%%}里面的参数代表的意思：
            第1项：取值为0，1或2，0表示公差，1表示公比，2表示字母
            第2项：开始页数
            第3项：总页数
            第4项：步进（公差或公比数）
            第5项：值是0或1，表示是否不倒转，倒转后会从后面的页数开始(不填时，默认为0倒转)
            第6项：值是0或1，表示是否补零(不填时，默认为0不补)
             */
            1=>0,
            2=>1,
            3=>'caiji',
            4=>1,
            5=>0,
            6=>0,
            'type'=>'regex,multi',//匹配方式
            'selector'=>'#<a href="(?P<url>[^"]+)" class="pic1-box">#',//标签匹配正则
            'tags'=>['url','page_num'],//标签
            'cut'=>'<ul class="page-list clearfix">{%|||%}</ul>',//截取中间内容
            'filter'=>[],
            'notEmpty'=>['url'],
        ]
    ],
];
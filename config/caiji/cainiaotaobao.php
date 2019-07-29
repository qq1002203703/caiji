<?php

return [
    'options'=>[
        'table'=>'caiji_cainiaotaobao',
        'downloadTable'=>'caiji_cainiaotaobao_download',
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
                    CURLOPT_REFERER=>'http://www.kebao5.com/',
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
                'curlTimeInterval'=>3800, //curl每次访问的最小时间间隔 单位毫秒
                'isRandomUserAgent'=>false, //是否使用随机ua
                'isAutoReferer'=>false, //是否需要自动获取来路
                'waitNoProxy'=>30, //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
                'waitIpLock'=>1000*60*5, //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
                'encoding'=>'', //指定源网页的编码
            ],
            'method'=>'get',
            'data'=>[],
        ]
    ],
    'page'=>[
        //'name'=>'',//string,rule_name 此项不用写，采集规则名（独一无二的）
        //string 起始页url 后面参数 第0项（0为公差，1为公比，2字母），第1项（开始页数），第2项（总页数）第3项：步进（公差或公比数），第4项是否不倒转(默认0倒转),第5项：是否补零(默认不补)，
        //'url'=>'http://www.anyv.net/index.php/categoryyuedu-19-page-{%0,1,40236,1,0,0%}',
        'type'=>1, //种类
        'retimes'=>500,//重复多少次停止
        'outType'=>2,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plugSave'=>'',
        'plugBeforeCaiji'=>'',
        'plugBeforeSelector'=>'',
        'plugCheckLogin'=>'',
        'curl'=>[],
        'rules'=>[
            [
                'type'=>'regex,multi',//匹配方式
                'selector'=>'#<tr data-id="\d+" class="J_item">\s+<td class="title J_tags">\s+<a href="(?P<url>/detail.html\?postId=(?P<from_id>\d+))"[\s\S]+?</tr>#i',//标签匹配正则
                'tags'=>['url','from_id'],//标签
                'cut'=>'<th class="title">话题</th>{%|||%}<div class="rain-page">',//截取中间内容
                'filter'=>[
                    //'url'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>','union{%|||%}http://www.aaa.com/{%xxoo%}']
                    //'url'=>['union{%|||%}http://www.zuanke8.com/archiver/{%xxoo%}'],
                    'url'=>['trueurl{%|||%}0'],
                ],
                'notEmpty'=>['url','from_id'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
                /*'file'=>[
                    'thumb'=>[
                        'type'=>'2',//种类：1=>为图片，2=>为文件
                        'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    ]
                ],*/
                /*'multi_page'=>[ //采集分页，如果不采集分页可以删除此项
                    'isdo'=>false,//是否要采集分页,必须声明此项为真才会采集分页
                    'max'=>0,//最大页数，0为不限制
                    'type'=>'regex,multi',
                    'cut'=>'',
                    'selector'=>'',
                    'tags'=>'',
                    'filter'=>['trueurl{%|||%}0'],
                    'notEmpty'=>true,
                ],*/
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
            'title'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>true,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<li class="h3">{%|||%}</li>',
                    'tags'=>''//type=reg或regm时必填，type=cut时可以为空
                ],
                //是否开启循环匹配
                'isLoop'=>false,
                //是否开启多分页采集
                'isMultipage'=>false,
                //多分页规则
                'multipage'=>[],
                'plugin'=>[],
                /**
                 * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
                 * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
                 */
                'filter'=>[],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'content'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>true,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<div class="J_lpContent">{%|||%}</div>',
                    'tags'=>''//type=regex必填
                ],
                //是否开启循环匹配
                'isLoop'=>false,
                //是否开启多分页采集
                'isMultipage'=>false,
                //多分页规则
                'multipage'=>[],
                'plugin'=>[],
                /**
                 * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
                 * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
                 */
                'filter'=>[
                    //'replace{%|||%}赚客吧{%|||%}u惠吧',
                    //'replace{%|||%}赚吧{%|||%}u惠吧',
                    'reg{%|||%}#<div[^>]*>#{%|||%}<p>',
                    'replace{%|||%}</div>{%|||%}</p>',
                    'html{%|||%}<p><br>',
                    'reg{%|||%}#<p>\s*</p>#{%|||%}',
                    'replace{%|||%}<br />{%|||%}<br>',
                    'reg{%|||%}#\s{2,}#{%|||%} ',
                    'reg{%|||%}/\{:[^:\}]*:\}/{%|||%}',
                    'trim{%|||%}x',
                    'length{%|||%}5'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'reply'=>[
                //html常规采集  page列表页已采  url从网址中匹配 、tags标签组合、fixed 固定、function函数求值
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>[],
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,map',
                    'selector'=>'%<div class="reply">(?P<area>[\s\S]+?)</dl>%',
                    'tags'=>[
                        'username'=>'%<a href="[^"]+" target="_blank">(?P<username>.*)</a>发表于%',
                        'content'=>'%<div class="message J_lpContent">(?P<content>[\s\S]+)</div>%',
                    ],
                    'cut'=>'<div class="line"></div>{%|||%}<div class="rain-page">',
                    /**
                     * from取'tags'、'fixed'或'function',本项才起作用
                     * tags时:例如：取title和content两个标签组合，'{%title%}{%content%}'，标签的两头还可以附加其他任意固定的字符串
                     * fixed时：任意固定的字符串
                     * function时：格式：'函数名|||参数1@参数2[...@参数]',例如要返回现在的日期和时间  'date|||Y-m-d h:i:s'
                     */
                    'remak'=>'',
                ],
                //是否开启循环匹配
                'isLoop'=>false,
                //是否开启多分页采集
                'isMultipage'=>false,
                //多分页规则
                'multiPage'=>[
                ],
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
                'filter'=>[
                    'content'=>[
                        'reg{%|||%}#<div[^>]*>#{%|||%}<p>',
                        'replace{%|||%}</div>{%|||%}</p>',
                        'html{%|||%}<p><br>',
                        'reg{%|||%}#<p>\s*</p>#{%|||%}',
                        'replace{%|||%}<br />{%|||%}<br>',
                        'reg{%|||%}#\s{2,}#{%|||%} ',
                        'reg{%|||%}/\{:[^:\}]*:\}/{%|||%}',
                        'trim{%|||%}x',
                        'length{%|||%}5'
                    ]
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

    ],
    'fabu'=>[
        'run_max'=>0,//每次发布的条数，0表示不限制
        'plug'=>[],//插件
        'target'=>'http://www.uuhuihui.com/bbs/fabu/start?pwd=Djidksl$$EER4ds58cmO', //发布的目标
        'fields'=>'login,update_time',
        //'where1'=>' WHERE isfabu=0 and iscaiji=1 and isdownload=1 and isend=1 and isshenhe=1 and islaji=0',
        'where1'=>' WHERE isfabu=0 and iscaiji=1 and isdownload=1 and isend=1 and islaji=0',
        'where2'=> [['isfabu','eq',0],['iscaiji','eq',1],['isdownload','eq',1],['isend','eq',1],['islaji','eq',0]],
    ]
];
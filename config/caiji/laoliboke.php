<?php
return [
    'options'=>[
        'table'=>'laoliboke',
        'downloadTable'=>'laoliboke_download',
        'callback'=>'\core\caiji\normal\\',
    ],
    'page'=>[
        //'name'=>'',//string,rule_name 此项不用写，采集规则名（独一无二的）
        //string 起始页url 后面参数 第0项（0为公差，1为公比，2字母），第1项（开始页数），第2项（总页数）第3项：步进（公差或公比数），第4项是否不倒转(默认0倒转),第5项：是否补零(默认不补)，
        //'url'=>'http://www.anyv.net/index.php/categoryyuedu-19-page-{%0,1,40236,1,0,0%}',
        'type'=>1, //种类
        'retimes'=>0,//重复多少次停止
        'outType'=>1,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plugBeforeCaiji'=>'',
        'plugBeforeSelector'=>'',
        'plugCheckLogin'=>'',
        'plugSingle'=>'',
        'curl'=>[],
        'rules'=>[
            [
                'type'=>'regex,map',//匹配方式
                'selector'=>'%<article\s*class="post"\s*data-id="[0-9]+">(?P<area>[\s\S]+?)</article>%',//区域匹配正则 subjects
                'tags'=>[//每个标签匹配正则
                    'url'=>'%<h2><a target="_blank" href="(?P<url>[^"]+)"%',
                    'from_id'=>'%<h2><a target="_blank" href="https://www.laoliboke.com/post/(?P<from_id>\d+)\.html"%',
                    'title'=>'%<h2><a [^>]+>(?P<title>.+)</a></h2>%',
                    'thumb'=>'%<mip-img src="(?P<thumb>[^"]+)"%'
                ],
                'cut'=>'<div class="article">{%|||%}<nav class="pagination">',//截取中间内容
                'filter'=>[
                    //'url'=>['reg{%|||%}/\?tid\-(\d+)\.html/i{%|||%}http://www.zuanke8.com/thread-$1-1-1.html']
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
        'curlTimeInterval'=>1000,
        'caiji'=>[
            'title'=>[
                'from'=>'page',
                //是否不能为空
                'notEmpty'=>true,
                /**
                 * 过滤 'filter'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>'],
                 * trueurl:第一项种类0为url，1字符串，第二项是否保留锚点
                 */
                'filter'=>[
                    'replace{%|||%}老李博客{%|||%}U惠社区',
                    'replace{%|||%}老李{%|||%}小惠',
                    'replace{%|||%}李校长{%|||%}小惠',
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                ]
            ],
            'thumb'=>[
                'from'=>'page',
                'notEmpty'=>false,
                'files'=>[
                    'type'=>2,
                    'replace_path'=>'{%i%}/thumb_{%id%}',
                    'pre_url'=>''//链接网址前缀
                ],
            ],
            'shijian'=>[
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<i class="fa fa-clock-o fa-fw"></i>{%|||%}</span>',
                    'tags'=>''//type=reg或regm时必填，type=cut时可以为空
                ],
                //是否开启循环匹配
                'isLoop'=>false,
                //是否开启多分页采集
                'isMultipage'=>false,
                //多分页规则
                'multipage'=>[],
                'plugin'=>[],
                'filter'=>[
                    'replace{%|||%}年{%|||%}-',
                    'replace{%|||%}月{%|||%}-',
                    'replace{%|||%}日{%|||%}',
                    'trim{%|||%}x',
                ],
            ],
            'tag'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<p class="post-tag">标签：{%|||%}</p>',
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
                'filter'=>[
                    'reg{%|||%}#<a[^>]+>#{%|||%}',
                    'replace{%|||%}</a>{%|||%},',
                    'trim{%|||%}x',
                    'reg{%|||%}#,$#{%|||%}',
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'content'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<article class="article-content">{%|||%}<div class="bdshare">',
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
                    //'html{%|||%}<br>',
                    'reg{%|||%}#<mip-img [^>]*src="https://www.laoliboke.com/zb_users/upload/2019/03/201903191552964644787350.jpg"[^>]*>.*?</mip-img>#{%|||%}',
                    'replace{%|||%}<mip-img{%|||%}<img',
                    'replace{%|||%}</mip-img>{%|||%}',
                    'replace{%|||%}</br>{%|||%}',
                    'reg{%|||%}#<br [^>]*>#{%|||%}<br>',
                    'reg{%|||%}#<p [^>]*>#{%|||%}<p>',
                    'reg{%|||%}%<img [^>]*src="([^"]*)"[^>]*>%{%|||%}<img src="$1">',
                    //'replace{%|||%}　{%|||%} ',//中文空格替换为英文
                    'html{%|||%}<p><img><br>',
                    'replace{%|||%}&nbsp;{%|||%} ',
                    'reg{%|||%}#[\s]{2,}#{%|||%} ',
                    'reg{%|||%}#<p>(\s|<br>)*</p>#{%|||%}',
                    'trim{%|||%}x',
                    'replace{%|||%}老李博客{%|||%}U惠社区',
                    'replace{%|||%}老李{%|||%}小惠',
                    'replace{%|||%}李校长{%|||%}小惠',
                ],
                'files'=>[
                    'type'=>1,//种类：1=>为图片，2=>为文件
                    'replace_path'=>'{%i%}/{%u%}',
                    //'pre_url'=>'/'//链接网址前缀
                ]
            ],
        ],
        /*'curl'=>[],*/
       /* 'http'=>[
            'curl'=>[
                'tryTimes'=>3,
                'login'=>false,
                'match'=>'',
                'cookieFile'=>'',
                'header'=>[],
                'opt'=>[
                    CURLOPT_TIMEOUT=>15,//下载时应该按目标文件大小设置大一点
                    CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                    CURLOPT_REFERER=>'https://m.movie.douban.com/',
                ]
            ],
            'options'=>[
                'proxy'=>[], //代理ip 端口 种类 格式 ['ip'=>'8.8.8.5','port'=>80,'type'=>'http']
                'checkProxyPlugin'=>'\shell\caiji\plugin\Proxy::douban_check_proxy',//检测代理ip的插件
                'getProxyPlugin'=>'\shell\caiji\plugin\Proxy::douban_get_proxy', //获取代理ip的插件
                'checkResultPlugin'=>'\shell\caiji\plugin\Proxy::douban_check_result',//检测结果是否正常
                'isProxy'=>true, //是否使用代理ip访问
                'isProxyFix'=>false, //是否使用固定的代理ip
                'ipExpirationTime'=>280, //ip过期时间 单位秒
                'curlTimeInterval'=>1000, //curl每次访问的最小时间间隔 单位毫秒
                'isRandomUserAgent'=>true, //是否使用随机ua
                'isAutoReferer'=>true, //是否需要自动获取来路
            ],
            'method'=>'get',
            'data'=>[],
        ]*/
    ],
    'download'=>[
        'save_path'=>'uploads/images/laoli',
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
    /*'fabu'=>[
        'run_max'=>0,//每次发布的条数，0表示不限制
        'plug'=>[],//插件
        'target'=>'start?pwd=Djidksl$$EER4ds58cmO', //发布的目标
        'fields'=>'update_time,create_time',
        //'where1'=>' WHERE isfabu=0 and iscaiji=1 and isdownload=1 and isend=1 and isshenhe=1 and islaji=0',
        'where1'=>' WHERE isfabu=0 and iscaiji=1 and isdownload=1 and islaji=0 order by from_id',
        'where2'=> [['isfabu','eq',0],['iscaiji','eq',1],['isdownload','eq',1],['islaji','eq',0]],
    ],*/
];
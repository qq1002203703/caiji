<?php
return [
    'options'=>[
        'table'=>'caiji_douban',
        'downloadTable'=>'caiji_douban_download',
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
                'type'=>'json,multi',//匹配方式
                'selector'=>'data',//区域匹配正则 subjects
                'tags'=>[//每个标签匹配正则
                    'url'=>'url',
                    'from_id'=>'id',
                    //'title'=>'title',
                    //'thumb'=>'cover'
                ],
                'cut'=>'',//截取中间内容
                'filter'=>[
                    //'url'=>['reg{%|||%}/\?tid\-(\d+)\.html/i{%|||%}http://www.zuanke8.com/thread-$1-1-1.html']
                ],
                'notEmpty'=>['url','from_id'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
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
        'pluginSave'=>'\shell\caiji\plugin\Save::douban_content',
        'curlTimeInterval'=>4000,
        'caiji'=>[
            'title'=>[
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>true,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<title>{%|||%}</title>',
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
                    //'html{%|||%}',
                    'replace{%|||%}(豆瓣){%|||%}',
                    'trim{%|||%}x',
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'director'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class=\'pl\'>导演</span>:{%|||%}</span><br/>',
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
                    'html{%|||%}',
                    'trim{%|||%}x',
                    'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'producer'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span ><span class=\'pl\'>编剧</span>:{%|||%}</span><br/>',
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
                    'html{%|||%}',
                    'trim{%|||%}x',
                    'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'actor'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class=\'pl\'>主演</span>:{%|||%}</span><br/>',
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
                    'html{%|||%}',
                    'trim{%|||%}x',
                    'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'tag'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class="pl">类型:</span>{%|||%}<br/>',
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
                    'html{%|||%}',
                    'trim{%|||%}x',
                    'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'area'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class="pl">制片国家/地区:</span>{%|||%}<br/>',
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
                    'trim{%|||%}x',
                    'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'other_name'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class="pl">又名:</span>{%|||%}<br/>',
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
                    //'html{%|||%}',
                    'trim{%|||%}x',
                    'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'imdb'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class="pl">IMDb链接:</span>{%|||%}<br>',
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
                    'html{%|||%}',
                    'trim{%|||%}x',
                    //'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'score'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<strong class="ll rating_num" property="v:average">{%|||%}</strong>',
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
                    //'html{%|||%}',
                    'trim{%|||%}x',
                    //'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'date_published'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'"datePublished": "{%|||%}",',
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
                    //'html{%|||%}',
                    'trim{%|||%}x',
                    //'replace{%|||%} / {%|||%}$$$'
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
                    'selector'=>'<div class="indent" id="link-report">{%|||%}</div>',
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
                    'html{%|||%}<br>',
                    'replace{%|||%}<br />{%|||%}<br>',
                    'replace{%|||%}&copy;豆瓣{%|||%}',
                    'replace{%|||%}(展开全部){%|||%}',
                    'replace{%|||%}　{%|||%} ',//中文空格替换为英文
                    'reg{%|||%}#[\s]{2,}#{%|||%} ',
                    'trim{%|||%}x',
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%i%}/{%u%}',
                    //'pre_url'=>'/'//链接网址前缀
                ]
            ],
            'thumb'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,single',
                    'remak'=>'',
                    'selector'=>'#<a class="nbgnbg" [^>]+>\s+<img src="(?P<thumb>[^>"]+)"#s',
                    'tags'=>'thumb'//type=regex必填
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
                    'replace{%|||%}<img src="{%|||%}',
                    'trim{%|||%}x',
                    'reg{%|||%}#\.webp$#i{%|||%}.jpg',
                ],
                'files'=>[
                    'type'=>2,//种类：1=>为图片，2=>为文件
                    'replace_path'=>'{%i%}/{%u%}',
                    //'pre_url'=>'/'//链接网址前缀
                ]
            ],
            'type'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class="pl">首播:</span>{%|||%}<br/>',
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
                    'html{%|||%}',
                    'trim{%|||%}x',
                    //'replace{%|||%} / {%|||%}$$$'
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
        ],
        'curl'=>[],
        'http'=>[
            'setting'=>[
                'tryTimes'=>3,
                'login'=>false,
                'match'=>'',
                'cookieFile'=>'',
                'header'=>[],
                'opt'=>[
                    CURLOPT_TIMEOUT=>6,//下载时应该按目标文件大小设置大一点
                    CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                    CURLOPT_REFERER=>'https://m.movie.douban.com/',
                    CURLOPT_HTTPPROXYTUNNEL=>false,
                    CURLOPT_PROXYTYPE=>CURLPROXY_HTTP,
                    //CURLOPT_PROXY=>'http://http-dyn.abuyun.com:9020', //动态
                    CURLOPT_PROXY=>'http://http-cla.abuyun.com:9030', //经典
                    //CURLOPT_PROXY=>'http://http-pro.abuyun.com:9010', //专业
                    CURLOPT_PROXYAUTH=>CURLAUTH_BASIC,
                    CURLOPT_PROXYUSERPWD=>'H52E23WF8U60619C:B7AD4EEC38365C24',
                ]
            ],
            'options'=>[
                'proxy'=>[], //代理ip 端口 种类 格式 ['ip'=>'8.8.8.5','port'=>80,'type'=>'http']
                'checkProxyPlugin'=>'\shell\caiji\Douban::check_proxy',//检测代理ip的插件
                'getProxyPlugin'=>'\shell\caiji\Douban::get_proxy', //获取代理ip的插件
                'checkResultPlugin'=>'\shell\caiji\Douban::check_result',//检测结果是否正常
                'isProxy'=>false, //是否使用代理ip访问
                'ipExpirationTime'=>280, //ip过期时间 单位秒
                'isOpenCurlTimeInterval'=>true,//是否开启curl访问时间间隔控制
                'curlTimeInterval'=>200, //curl每次访问的最小时间间隔 单位毫秒
                'isRandomUserAgent'=>true, //是否使用随机ua
                'isAutoReferer'=>true, //是否需要自动获取来路
                'waitNoProxy'=>20, //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
                'waitIpLock'=>10000, //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
                'waitCurlFalse'=>4000,//当curl获取结果为false时 等待多少时间才重新发起下次请求(单位毫秒)
                'tryTimes'=>30,
                'encoding'=>'',
            ],
            'method'=>'get',
            'data'=>[],
        ],
    ],
    'download'=>[
        'save_path'=>'uploads/images/video',
        'replace_path'=>'{%Y%}/{%m%}/{%d%}/{%u%}',
        'plugin_after'=>'',
        'plugin_before'=>'\shell\caiji\Douban::check_img',
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
    ],
];
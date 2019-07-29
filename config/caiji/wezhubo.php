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
        'table'=>'wezhubo',
        'downloadTable'=>'wezhubo_download',
        'callback'=>'\core\caiji\normal\\',
    ],
    'page'=>[
        //'name'=>'',//string,rule_name 此项不用写，采集规则名（独一无二的）
        //string 起始页url 后面参数 第0项（0为公差，1为公比，2字母），第1项（开始页数），第2项（总页数）第3项：步进（公差或公比数），第4项是否不倒转(默认0倒转),第5项：是否补零(默认不补)，
        //'url'=>'http://www.anyv.net/index.php/categoryyuedu-19-page-{%0,1,40236,1,0,0%}',
        'type'=>1, //种类
        'retimes'=>10,//重复多少次停止
        'outType'=>2,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plugBeforeCaiji'=>'',
        'plugBeforeSelector'=>'',
        'plugCheckLogin'=>'',
        'curl'=>[],
        'rules'=>[
            [
                'type'=>'regex,map',//匹配方式
                'selector'=>'%<li class="col-25 col-m-12 mb15">(?P<area>[\s\S]+?)</li>%',//区域匹配正则
                'tags'=>[//每个标签匹配正则
                    'url'=>'%<a href="(?P<url>[^"]+)" class="img-item mb5">%',
                    'from_id'=>'%<a href="http://www.lovehzb.com/post/(?P<from_id>\d+)\.html" class="img-item mb5">%',
                    'title'=>'%<h3 class="f-14 txt-ov">(?P<title>.+)</h3>%',
                    'thumb'=>'%<img src="(?P<thumb>[^"]+)"%'
                ],
                'cut'=>'<ul class="row">{%|||%}<div class="pagebar">',//截取中间内容
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
        'pluginAfter'=>'shell\caiji\plugin\PluginAfter::wezhubo',
        //'isdownload'=>0, //特殊项，改变下载状态
        //保存数据时调用
        'pluginSave'=>'',
        'caiji'=>[
            'category'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<div class="place mb15">当前位置：{%|||%}</div>',
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
                    'reg{%|||%}#\s+#{%|||%}',
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
                    'selector'=>'<div class="info-con tx-box mb15 pd15">{%|||%}<div class="info-vip-box ta-c">',
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
                    'replace{%|||%}<p><p>{%|||%}<p>',
                    'replace{%|||%}#<p>\s*<br\/?>\s*</p>#{%|||%}',
                    'reg{%|||%}#\s{2,}#{%|||%} ',
                    'trim{%|||%}x',
                    'reg{%|||%}#<p>$#{%|||%}'
                ],
                'files'=>[
                    'type'=>1,//种类：1=>为图片，2=>为文件
                    'replace_path'=>'{%i%}/{%u%}',
                    'pre_url'=>'/'//链接网址前缀
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
        ],
        'curl'=>[],
    ],
    'download'=>[
        'save_path'=>'uploads/images/wzb',
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
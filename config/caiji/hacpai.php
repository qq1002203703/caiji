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
        'table'=>'caiji_hacpai',
        'downloadTable'=>'caiji_hacpai_download',
        'callback'=>'\core\caiji\normal\\',
    ],
    'page'=>[
        //string 起始页url 后面参数 第0项（0为公差，1为公比，2字母），第1项（开始页数），第2项（总页数）第3项：步进（公差或公比数），第4项是否不倒转(默认0倒转),第5项：是否补零(默认不补)，
        //'url'=>'http://www.anyv.net/index.php/categoryyuedu-19-page-{%0,1,40236,1,0,0%}',
        'type'=>1, //种类
        'retimes'=>100,//重复多少次停止
        'outType'=>2,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        //'plugSave'=>'\shell\caiji\plugin\Save::zuanke8',
        'plugBeforeCaiji'=>'',
        'plugBeforeSelector'=>'\shell\caiji\plugin\Page::hacpaiBeforeSelector',
        'plugCheckLogin'=>'',
        'curl'=>[],
        'rules'=>[
            [
                'type'=>'regex,map',//匹配方式
                'selector'=>'#<li class="article-list__item">(?P<area>[\s\S]*?)<div class="fn-relative">#',//标签匹配正则
                'tags'=>[
                    'url'=>'#<a data-id="\d+"[\s]+data-type="0"[\s]+href="(?P<url>[^"]+)"[^>]+>#',
                    'title'=>'#<a data-id="\d+"[^>]+>(?P<title>[\s\S]+?)</a>#',
                    'from_id'=>'#<a data-id="(?P<from_id>\d+)"#'
                ],//标签
                'cut'=>'',//截取中间内容
                'filter'=>[
                    //'url'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>','union{%|||%}http://www.aaa.com/{%xxoo%}']
                    //'url'=>['union{%|||%}http://www.zuanke8.com/archiver/{%xxoo%}'],
                    //'title'=>['html{%|||%}<h1>']
                ],
                'notEmpty'=>['url','title','from_id'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
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
        'pluginSave'=>'\shell\caiji\plugin\Save::hacpai',
        'caiji'=>[
            'content'=>[
                'from'=>'html',
                'notEmpty'=>true,
                'rule'=>[
                    'type'=>'regex,single',
                    'remak'=>'',
                    'selector'=>'#<div class="content-reset article-content"[^>]+>(?P<content>[\s\S]+?)</div>\s+<div class="article-tail">#',
                    'cut'=>'',
                    'tags'=>'content'//type=reg或regm时必填，type=cut时可以为空
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
                    'reg{%|||%}#<script[^>]*>[\s\S]*?</script>#i{%|||%}',
                    'reg{%|||%}#<ins[^>]*>[\s\S]*?</ins>#i{%|||%}',
                    'reg{%|||%}#<span[^>]+>#i{%|||%}',
                    'replace{%|||%}</span>{%|||%}',
                    'reg{%|||%}#<div[^>]*>#{%|||%}<p>',
                    'replace{%|||%}</div>{%|||%}</p>',
                    'reg{%|||%}#<i>该文章同步自[\s\S]+?</i>#{%|||%}',
                    'html{%|||%}<p><img><pre><code><br><li><ul>',
                    'reg{%|||%}#<p>\s*</p>#{%|||%}',
                    'replace{%|||%}<br />{%|||%}<br>',
                    'reg{%|||%}#\s{2,}#{%|||%} ',
                    'trim{%|||%}x',
                    //'length{%|||%}500'
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
        /*'run_max'=>10,//每次发布的条数，0表示不限制
        'plug'=>[],//插件
        'target'=>'http://www.uuhuihui.com/bbs/fabu/start?pwd=Djidksl$$EER4ds58cmO', //发布的目标
        'fields'=>'login,update_time',
        'where1'=>' WHERE isfabu=0 and iscaiji=1 and isdownload=1 and isend=1 and isshenhe=1 and islaji=0',
        'where2'=> [['isfabu','eq',0],['iscaiji','eq',1],['isdownload','eq',1],['isend','eq',1],['isshenhe','eq',1],['islaji','eq',0]],*/
    ]
];
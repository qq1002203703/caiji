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
        'table'=>'zuanke8',
        'downloadTable'=>'zuanke8_download',
        'callback'=>'\core\caiji\normal\\',
    ],
    'page'=>[
        //'name'=>'',//string,rule_name 此项不用写，采集规则名（独一无二的）
        //string 起始页url 后面参数 第0项（0为公差，1为公比，2字母），第1项（开始页数），第2项（总页数）第3项：步进（公差或公比数），第4项是否不倒转(默认0倒转),第5项：是否补零(默认不补)，
        //'url'=>'http://www.anyv.net/index.php/categoryyuedu-19-page-{%0,1,40236,1,0,0%}',
        'type'=>1, //种类
        'retimes'=>5000,//重复多少次停止
        'outType'=>2,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plugSave'=>'\shell\caiji\plugin\Save::zuanke8',
        'plugBeforCaiji'=>'',
        'plugBeforSelector'=>'',
        'plugCheckLogin'=>'',
        'curl'=>[],
        'rules'=>[
            [
                'type'=>'regex,multi',//匹配方式
                'selector'=>'#<li><a href="(?P<url>\?tid\-(?P<from_id>\d+)\.html)">.+?</a> \((?P<comments_num>\d+)篇回复\)#i',//标签匹配正则
                'tags'=>['url','from_id','comments_num'],//标签
                'cut'=>'<ul type="1" start="1">{%|||%}</ul>',//截取中间内容
                'filter'=>[
                    //'url'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>','union{%|||%}http://www.aaa.com/{%xxoo%}']
                    //'url'=>['union{%|||%}http://www.zuanke8.com/archiver/{%xxoo%}'],
                    'url'=>['reg{%|||%}/\?tid\-(\d+)\.html/i{%|||%}http://www.zuanke8.com/thread-$1-1-1.html']
                ],
                'notEmpty'=>['url','tid'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
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
        'pluginSave'=>'\shell\caiji\plugin\Save::zuanke8',
        'caiji'=>[
            'title'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>true,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span id="thread_subject" title="{%|||%}">',
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
            'comments_num'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,cut',
                    'remak'=>'',
                    'selector'=>'<span class="xg1">回复:</span> <span class="xi1">{%|||%}</span>',
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
                    'replace{%|||%}赚客吧{%|||%}u惠吧',
                    'replace{%|||%}赚吧{%|||%}u惠吧',
                ],
                'files'=>[
                    'type'=>0,//种类：1=>为图片，2=>为文件
                    //'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                    //'pre_url'=>''//链接网址前缀
                ]
            ],
            'tu'=> [
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>false,
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,multi',
                    'remak'=>'',
                    'selector'=>'#<div class="mbn savephotop">\s*<img [^><]*?file="(?P<tu>[^"]+)"[^>]*>\s*</div>#',
                    'cut'=>'</h1>{%|||%}<form method="post" autocomplete="off" name="modactions" id="modactions">',
                    'tags'=>'tu'
                ],
                //是否开启循环匹配
                'isLoop'=>true,
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
            'content'=>[
                //html常规采集  page列表页已采  url从网址中匹配 、tags标签组合、fixed 固定、function函数求值
                'from'=>'html',
                //是否不能为空
                'notEmpty'=>['content','username','avatar','shijian'],
                //为采集时的规则
                'rule'=>[
                    'type'=>'regex,map',
                    'selector'=>'%<table id="pid(\d+)" class="plhin" summary="pid\1" cellspacing="0" cellpadding="0">(?P<area>[\s\S]+?)</td>\s*</tr>\s*</table>%',
                    'tags'=>[
                        'shijian'=>'%<em id="authorposton\d+">发表于 (?P<shijian>[\d:\- ]+)</em>%',
                        'username'=>'%<div class="authi"><a [^<>]+class="xw1">(?P<username>.*)</a>\s*</div>%',
                        'content'=>'%<table cellspacing="0" cellpadding="0">\s*<tr>\s*<td class="t_f" id="postmessage_\d++">(?P<content>[\s\S]++)%',
                    ],
                    'cut'=>'</h1>{%|||%}<form method="post" autocomplete="off" name="modactions" id="modactions">',
                    /**
                     * from取'tags'、'fixed'或'function',本项才起作用
                     * tags时:例如：取title和content两个标签组合，'{%title%}{%content%}'，标签的两头还可以附加其他任意固定的字符串
                     * fixed时：任意固定的字符串
                     * function时：格式：'函数名|||参数1@参数2[...@参数]',例如要返回现在的日期和时间  'date|||Y-m-d h:i:s'
                     */
                    'remak'=>'',
                ],
                //是否开启循环匹配
                'isLoop'=>true,
                //是否开启多分页采集
                'isMultipage'=>true,
                //多分页规则
                'multiPage'=>[
                    'firstPage'=>[
                        'type'=>'current',// 正则时为 'regex,single'；取当前页url为'current'
                        'selector'=>'{%current%}', //正则时为 对应的正则规则；取当前页url时为替换组合
                        'tags'=>'', //正则时为'firstLink'；取当前页url时为空
                        'cut'=>'', // 正则时为 截取两头的规则；取当前页url时为空
                        'num'=>1//起始页数
                    ],
                    'type'=>'regex,multi',
                    'selector'=>'%<a href="(?P<pageUrl>http://www\.zuanke8\.com/thread\-\d+\-(?P<pageNum>\d+)\-1\.html)"[^>]*>%',
                    'cut'=>'<div class="pgt"><div class="pg">{%|||%}<label>',
                    'tags'=>'pageUrl,pageNum',
                    'max'=>10,
                    'increase'=>1
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
                        'reg{%|||%}#<div class="quote">[\s\S]*</div><br />#{%|||%}',
                        'reg{%|||%}#<div class="tip tip_4( aimg_tip)?"[^>]*>[\s\S]+?<div class="tip_horn"></div>\s+</div>#{%|||%}',
                        'reg{%|||%}#<span style="white-space: nowrap" id="attach_\d+"[^>]*>[\s\S]*?</span>#{%|||%}',
                        'reg{%|||%}#<div[^>]*>#{%|||%}<p>',
                        'replace{%|||%}</div>{%|||%}</p>',
                        'html{%|||%}<p><img><br>',
                        'reg{%|||%}#<img [^><]*?file="([^"]+)"[^>]*>#i{%|||%}<img src="$1">',
                        'reg{%|||%}#<img[^><]*?src="static/image/[^"]+"[^>]*>#{%|||%}',
                        'reg{%|||%}#<p>\s*</p>#{%|||%}',
                        'replace{%|||%}<br />{%|||%}<br>',
                        'reg{%|||%}#\s{2,}#{%|||%} ',
                        'reg{%|||%}/\{:[^:\}]*:\}/{%|||%}',
                        'trim{%|||%}x',
                        'replace{%|||%}赚客吧{%|||%}u惠吧',
                        'leng{%|||%}5'
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

    ]
];
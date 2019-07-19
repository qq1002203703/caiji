<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/

namespace shell\caiji;

use core\Conf;
use extend\Helper;
use shell\Spider;

class Bilibili extends Spider {
    public $appName='';
    public $fieldsFilter=['iscaiji','isend','isfabu','isdownload','isdone','times','caiji_name'];
    protected $fileBodyName='bilibili';
    protected $error=[
        1=>'视频已失效',
        -1=>'采集结果false',
        -2=>'采集结果http_code不是200',
        -3=>'采集结果json转数组出错',
        -4=>'采集结果格式不正确',
        -5=>'文本过长超过了字段最大值',
        -6=>'保存到数据库失败'
    ];
    //当前正在进行的操作，用来辅助判断出错的位置
    protected $currentAction='';

    protected function _init(){
        $this->_setCommandOptions(['-n'=>['appName']],$this->param);
    }

    //搜索采集========================================================
    public function search(){
        $config=Conf::all($this->appName,false,'config/bilibili/');
        if(!$config)
            exit('配置文件不存在'.PHP_EOL);
        $this->init([],'http_init');
        switch ($config['search_type']){
            case 'video'://视频
                $code=$this->searchVideo($config['search_keyword'],$config['app_name'],$config['app_keywords']);
                break;
            case 'bangumi'://番剧
                break;
            case 'article'://文章
                break;
            case 'pgc'://影视
                break;
            default:
                exit('不存在的search_type！');
        }
        if($code!==0)
            echo '  采集出错，error:'.$this->error[$code].PHP_EOL;
        else
            echo '  采集成功'.PHP_EOL;
    }

    protected function searchVideo($keyword,$app_name,$app_keywords){
        $this->currentAction='searchVideo';
        $page=9999;
        $url='https://search.bilibili.com/video?keyword='.urldecode($keyword).'&page=';
        for ($i=1;$i<=$page;$i++){
            $this->outPut(' 开始采集第'.$i.'页搜索'.PHP_EOL);
            $res=$this->client->http($url.$i);
            $check=$this->searchCheckResult($res);
            if($check!==0)
                return $check;
            $res=Helper::strCut($res,'window.__INITIAL_STATE__=',';(function()');
            $data=json_decode($res,true);
            if(!$data){
                dump($res);
                return -3;
            }
            if(!isset($data['videoData'])){
                dump($res);
                dump($data);
                return -4;
            }
            if($page==9999){
                $page=$data['pageInfo']['totalPages'];
            }
            foreach ($data['videoData'] as $item){
                if(!$this->saveContent([
                    'from_id'=>$item['id'],
                    'tag'=>$item['tag'],
                    'thumb'=>$item['pic'],
                    'title'=>strip_tags($item['title']),
                    'content'=>$this->filter($item['description'],$app_keywords),
                    'username'=>$item['author'],
                    'create_time'=>$item['pubdate'],
                    'caiji_name'=>$app_name,
                ]))
                    return -6;
            }
            msleep(400,20);
        }
        return 0;
    }

    protected function saveContent($data){
        $table='caiji_bilibili';
        if($this->model->from($table)->eq('from_id',$data['from_id'])->find(null,true)){
            $this->outPut('  from_id=>'.$data['from_id'].'已经入库过了'.PHP_EOL);
            return true;
        }
        if($this->model->from($table)->insert($data)){
            $this->outPut('  from_id=>'.$data['from_id'].',成功入库'.PHP_EOL);
            return true;
        }else{
            $this->outPut('  from_id=>'.$data['from_id'].',入库失败'.PHP_EOL);
            return false;
        }

    }

    protected function searchCheckResult(&$res){
        if($res===false)
            return -1;
        if($this->client->getHttpCode()!==200){
            echo 'http_code:'.$this->client->getHttpCode().PHP_EOL;
            return -2;
        }
        return 0;
    }

    /** ------------------------------------------------------------------
     * 评论和视频cid的采集
     *---------------------------------------------------------------------*/
    public function get(){
        $config=Conf::all($this->appName,false,'config/bilibili/');
        if(!$config)
            exit('配置文件不存在'.PHP_EOL);
        $table='caiji_bilibili';
        $where=[['iscaiji','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->init([],'http_init');
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table,$config){
            echo '开始处理：id=>'.$item['id'].',from_id=>'.$item['from_id'].'---------------'.PHP_EOL;
            $code=$this->spiderVideo($item['from_id'],$result);
            $update=[];
            if($code<0){
                exit('运行 '.$this->currentAction.' 时发生错误，信息：'.$this->error[$code].PHP_EOL);
            }elseif($code==1){
                $update['isfabu']=1;
            }else{
                $code=$this->spiderComment($item['from_id'],$config['app_keywords'],$config['app_name']);
                if($code!==0){
                    exit('运行 '.$this->currentAction.' 时发生错误，信息：'.$this->error[$code].PHP_EOL);
                }
                $update['cid']=$result['cid'];
                $update['videos']=$result['videos'];
                unset($result);
            }
            $update['iscaiji']=1;
            $update['comment_counts']=$this->model->count([
                'from'=>'caiji_bilibili_comment',
                'where'=>[['fid','eq',$item['from_id']]]
            ]);
            if( $comments=$this->model->from($table.'_comment')->eq('fid',$item['from_id'])->order('length desc')->limit(30)->findAll(true)) {
                $text = trim(self::filterTags(preg_replace('/\[[^\]]*\]/', '', strip_tags($comments[array_rand($comments)]['content']))));
                unset($comments);
            }else
                $text=$config['title_text'][array_rand($config['title_text'])];
            $update['seo_title']=self::auoTitle($item['title'],$item['username'],$item['tag'],$text,$config['title_keywords']);
            if($this->model->from($table)->eq('id',$item['id'])->update($update))
                echo '  成功：更新'.PHP_EOL;
            else
                echo '  失败：更新'.PHP_EOL;
            msleep(1200,300);
            //exit();
        });
    }

    //视频cid采集
    protected function spiderVideo($fid,&$result){
        $this->currentAction='spiderVideo';
        $url='https://api.bilibili.com/x/web-interface/view?aid='.$fid;
        $this->outPut(' 开始采集 aid=>'.$fid.' 的cid'.PHP_EOL);
        $result=[];
        $res=$this->client->http($url);
        if($res===false){
            return -1;
        }
        if($this->client->getHttpCode()!==200){
            echo 'http_code:'.$this->client->getHttpCode().PHP_EOL;
            return -2;
        }
        $res=json_decode($res,true);
        if(!$res){
            return -3;
        }
        if(!isset($res['code'])){
            return -4;
        }
        if($res['code']!=0){
            echo '  code不为0，提示：'.$res['message'].PHP_EOL;
            return 1;
        }else{
            $result['cid']=$res['data']['cid'];
            if($res['data']['videos']>1){
                $result['videos']=$this->getVideos($res['data']['pages']);
                if(strlen($result['videos'])>65535)
                    return -5;
            }else{
                $result['videos']='{"type":"bilibili","data":[{"page":1,"cid":'.$result['cid'].',"title":""}]';
            }
        }
        unset($res);
        return 0;
    }
    //采集评论
    protected function spiderComment($fid,$app_keywords,$app_name){
        $this->currentAction='spiderComment';
        $url='https://api.bilibili.com/x/v2/reply?type=1&oid='.$fid.'&sort=0&pn=';
        $max_page=999999;
        for ($i=1;$i<=$max_page;$i++){
            $this->outPut(' 开始采集第'.$i.'页评论'.PHP_EOL);
            $res=$this->client->http($url.$i);
            if($res===false){
                return -1;
            }
            if($this->client->getHttpCode()!==200){
                echo 'http_code:'.$this->client->getHttpCode().PHP_EOL;
                return -2;
            }
            $res=json_decode($res,true);
            if(!$res){
                return -3;
            }
            if(!isset($res['code'])){
                return -4;
            }
            if($res['code']!=0 && $res['message']=='禁止评论'){
                return 0;
            }
            if($res['data']['page']['count']==0)
                return 0;
            if($max_page==999999)
                $max_page=(int)ceil($res['data']['page']['count']/20);
            if($res['data']['replies']){
                foreach ($res['data']['replies'] as $reply){
                    $commentData=[
                        'create_time'=>$reply['ctime'],
                        'content'=>$reply['content']['message'],
                        'from_id'=>$reply['rpid'],
                        'username'=>$reply['member']['uname'],
                        'fid'=>$fid,
                        'caiji_name'=>$app_name
                    ];
                    if(isset($reply['replies']) && $reply['replies']){
                        $commentData['children']=count($reply['replies']);
                        $arr=[];
                        foreach ($reply['replies'] as $item){
                            $arr[]=$item['ctime'].'{%||%}'.$item['member']['uname'].'{%||%}'.$item['content']['message'];
                        }
                        $commentData['more']=implode('{%|||%}',$arr);
                    }
                    //评论入库
                    $this->saveComment($commentData,$app_keywords);
                }
            }
        }
        return 0;
    }

    /** ------------------------------------------------------------------
     * 评论入库
     * @param array $data
     * @return bool
     *---------------------------------------------------------------------*/
    protected function saveComment($data,$app_keywords){
        $table='caiji_bilibili_comment';
        if($this->model->from($table)->eq('from_id',$data['from_id'])->find(null,true)){
            $this->outPut('  评论from_id=>'.$data['from_id'].',已经入库过了'.PHP_EOL);
            return true;
        }
        $data['length']=mb_strlen($data['content']);
        //垃圾评论过滤
        if($this->is_rubbish($data['content'],($data['children']?? 0),$data['length'])){
            $this->outPut('  评论from_id=>'.$data['from_id'].',是垃圾评论'.PHP_EOL);
            return true;
        }
        $data['content']=$this->filter($data['content'],$app_keywords);
        //UPDATE `zcm_caiji_bilibili_comment` SET is_content=1 WHERE (children>1 AND length>30) OR length>120
        if((isset($data['children'])&& $data['children'] >1 && $data['length']>30) || $data['length']>120)
            $data['is_content']=1;
        if($this->model->from($table)->insert($data)){
            $this->outPut('  评论from_id=>'.$data['from_id'].',成功入库'.PHP_EOL);
            return true;
        }else{
            $this->outPut('  评论from_id=>'.$data['from_id'].',入库失败'.PHP_EOL,true);
            return false;
        }
    }

    /** ------------------------------------------------------------------
     * 检测评论内容是否是垃圾评论
     * @param string $content
     * @param $reply_counts
     * @return bool
     *---------------------------------------------------------------------*/
    protected function is_rubbish($content,$reply_counts,$length){
        return ($reply_counts==0 && $length<30 && preg_match('/[\x{4e00}-\x{9fa5}]+/u',$content)==0)? true:false;
    }

    protected function filter($str,$app_keywords){
        if(is_array($app_keywords)){
            $app_keyword=$app_keywords[array_rand($app_keywords)];
        }else
            $app_keyword=$app_keywords;
        unset($app_keywords);
        return '<p>' . preg_replace([
                '%https?://[-A-Za-z0-9+&@#/\%?=~_|!:,.;]+[-A-Za-z0-9+&@#/\%=~_|]%',
                '%[0-9a-zA-Z.]+\.(com|net|cn|org|cc|us|vip|club|xyz|me|io|wang|win)%',
                '/bili/i',
                '/bilibili/i',
                '/[bB]\s*站/',
                '/\n+/',
                '/\s{2,}/',
                '/up主/i',
                '\[.*?\]'
            ],[
                '',
                '',
                $app_keyword,
                $app_keyword,
                $app_keyword,
                '<br>',
                ' ',
                '楼主',
                ''
            ],$str). '</p>';
    }

    protected function http_init(){
        $this->newClient(['opt'=>[
            CURLOPT_TIMEOUT=>8,//下载时应该按目标文件大小设置大一点
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
            CURLOPT_REFERER=>'https://www.bilibili.com/',
            //CURLOPT_HTTPPROXYTUNNEL=>false,
            //CURLOPT_PROXYTYPE=>CURLPROXY_HTTP,
            //CURLOPT_PROXY=>'http://http-dyn.abuyun.com:9020', //动态
            //CURLOPT_PROXY=>'http://http-cla.abuyun.com:9030', //经典
            //CURLOPT_PROXY=>'http://http-pro.abuyun.com:9010', //专业
            //CURLOPT_PROXYAUTH=>CURLAUTH_BASIC,
            //CURLOPT_PROXYUSERPWD=>'H05K700VIP07918D:1AFA971C73727EFF',
        ]]);
        $this->client->httpSetting([
            'proxy'=>[], //代理ip 端口 种类 格式 ['ip'=>'8.8.8.5','port'=>80,'type'=>'http']
            //'checkProxyPlugin'=>'\shell\caiji\Douban::check_proxy',//检测代理ip的插件
            //'getProxyPlugin'=>'\shell\caiji\Douban::get_proxy', //获取代理ip的插件
            //'checkResultPlugin'=>'\shell\caiji\Zhihu::check_result',//检测结果是否正常
            //'isProxy'=>false, //是否使用代理ip访问
            //'ipExpirationTime'=>280, //ip过期时间 单位秒
            'isOpenCurlTimeInterval'=>true,//是否开启curl访问时间间隔控制
            'curlTimeInterval'=>1200, //curl每次访问的最小时间间隔 单位毫秒
            //'isRandomUserAgent'=>true, //是否使用随机ua
            //'isAutoReferer'=>true, //是否需要自动获取来路
            //'waitNoProxy'=>20, //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
            //'waitIpLock'=>10000, //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
            //'waitCurlFalse'=>4000,//当curl获取结果为false时 等待多少时间才重新发起下次请求(单位毫秒)
            'tryTimes'=>6,
            'encoding'=>'',
            'stopFile'=>$this->stopFile,
        ]);
    }
//标题处理=====================================================
    public static function auoTitle($title,$username,$tags,$text,$keywords){
        $title=self::titleFilter($title);
        if($tags){
            $tags=explode(',',$tags);
            foreach ($tags as $k=>$tag){
                $safeKeyword=preg_quote($tag,'#');
                if(preg_match('#'.$safeKeyword.'#',$title))
                    unset($tags[$k]);
            }
        }
        if($tags){
            $title=$tags[array_rand($tags)].$title;
        }
        if($keywords){
            foreach ($keywords as $k=>$keyword){
                if(preg_match('#'.$keyword.'#',$title)){
                    unset($keywords[$k]);
                }
            }
        }
        if($keywords){
            $new=$keywords[array_rand($keywords)];
            if($new=='xx0oo')
                $title=mb_substr($username,0,4).':'.$title;
            else
                $title=$new.':'.$title;
        }else
            $title=mb_substr($username,0,4).':'.$title;
        if($length=mb_strlen($title) <28){
            $title.=$text;
        }
        if($length=mb_strlen($title) >28){
            $title=mb_substr($title,0,28);
        }
        return $title;
    }

    //过滤标题
    static protected function titleFilter($title){
        $title=str_replace("\n",' ',$title);
        $length=mb_strlen($title);
        $title=preg_replace_callback_array(
            [
                '#【(.*?)】#' => function ($match) use($length){
                    if(mb_strlen($match[0])>=($length-4))
                        return $match[1];
                    else
                        return '';
                },
                '#\[(.*?)\]#' => function ($match) use($length){
                    if(mb_strlen($match[0])>=($length-4))
                        return $match[1];
                    else
                        return '';
                }
            ],
            $title
        );
        return self::filterTags($title);
    }

    //过滤标点
    public static function filterTags($str){
        return preg_replace([
            '/(！|。|，|？|：|；|“|”|《|》|（|）|—|、)+/',
            '/[,.\\,}#@%&*!()_\-~`|$<>\/]+/'
        ],'',$str);
    }

    public function fabu(){
        $config=Conf::all($this->appName,false,'config/bilibili/');
        if(!$config)
            exit('请输入项目名，格式: -n app_name '.PHP_EOL);
        echo '开始发布内容……'.PHP_EOL;
        $this->fabu_content($config['app_name'],$config['fields_filter']['content']);
        echo '开始发布评论……'.PHP_EOL;
        $this->fabu_comment($config['app_name'],$config['fields_filter']['comment']);
    }

    public function fabu_content($app_name,$fields_filter=''){
        $table='caiji_bilibili';
        $where=[['isfabu','eq',0],['iscaiji','eq',1],['caiji_name','eq',$app_name]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        if(!$this->client)
            $this->newClient();
        $this->setFields($fields_filter);
        $fields=$this->getFields($table);
        $this->doLoop($total,function ($perPage)use ($table,$where,$fields){
            return $this->model->select($fields)->from($table)->_where($where)->order('id')->limit($perPage)->findAll(true);
        },function ($item)use ($table,$app_name){
            echo '正在发布：id=>'.$item['id'].'---------------'.PHP_EOL;
            $id=$item['id'];
            unset($item['id']);
            $item['table']='bilibili';
            $item['type']='content';
            $item['comment']=$this->getCommentFabu($item['from_id'],$in);
            unset($item['caiji_name']);
            //dump($item);exit();
            $ret=$this->client->http('http://www.'.$app_name.'/portal/fabu/table?pwd=Djidksl$$EER4ds58cmO','post',$item);
            if(!$ret){
                exit('接口连接失败'.PHP_EOL);
            }
            if($ret==='发布成功'){
                if($in && !$this->model->from($table.'_comment')->in('id',$in)->update(['isfabu'=>1])){
                    dump($in);
                    exit('无法更新bilibili_comment数据库'.PHP_EOL);
                }
                if(!$this->model->from($table)->eq('id',$id)->update(['isfabu'=>1])){
                    exit('无法更新bilibili数据库'.PHP_EOL);
                }
                echo '发布成功'.PHP_EOL;
            }else
                exit('发布失败：'.$ret.PHP_EOL);
            //exit();
            //if($this->checkStop())
                //exit();
        });
    }
    //评论发布
    public function fabu_comment($app_name,$fields_filter=''){
        $table='caiji_bilibili_comment';
        $where=[['isfabu','eq',0],['caiji_name','eq',$app_name],['length','gt',7]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        if(!$this->client)
            $this->newClient();
        $this->setFields($fields_filter);
        $fields=$this->getFields($table);
        $this->doLoop($total,function ($perPage)use ($table,$where,$fields){
            return $this->model->select($fields)->from($table)->_where($where)->order('id')->limit($perPage)->findAll(true);
        },function ($item)use ($table,$app_name){
            echo '正在发布：id=>'.$item['id'].'---------------'.PHP_EOL;
            $id=$item['id'];
            unset($item['id']);
            $item['table']='bilibili';
            $item['type']='comment';
            if($item['more']===null)
                unset($item['more']);
            //dump($item);exit();
            $ret=$this->client->http('http://www.'.$app_name.'/portal/fabu/table?pwd=Djidksl$$EER4ds58cmO','post',$item);
            if(!$ret){
                exit('接口连接失败'.PHP_EOL);
            }
            if($ret==='发布成功'){
                $this->model->from($table)->eq('id',$id)->update(['isfabu'=>1]);
                echo '发布成功';
            }else
                exit('发布失败：'.$ret);
            //exit();
            //msleep(1000,100);
        });
    }

    /** ------------------------------------------------------------------
     * 1、首先要标记出哪些评论是用作独立文章:UPDATE `zcm_caiji_bilibili_comment` SET is_content=1 WHERE (children>1 AND length>30) OR length>120
     * 2、除去作独立文章的评论后的评论 $comment_counts,$rand=mt_rand(5~9)
     *      a.如果$comment_counts<=$rand 取全部评论
     *      b.如果$comment_counts>$rand =>先取(排序回复数 desc 评论长度 desc)的评论$rand篇
     * 3、剩下的评论到评论处发布
     * getCommentFabu
     * @param int $fid
     * @return string
     *---------------------------------------------------------------------*/
    protected function getCommentFabu($fid,&$in){
        $data=$this->model->from('caiji_bilibili_comment')->_where([['is_content','eq',0],['fid','eq',$fid],['isfabu','eq',0]])->order('children desc,length desc')->limit(mt_rand(5,9))->findAll(true);
        $in=[];
        if($data){
            $str_arr=[];
            foreach ($data as $item){
                $str_arr[]=$item['create_time'].'{%@@%}'.$item['username'].'{%@@%}'.$item['content'].($item['more'] ? '{%##%}'.$item['more']:'');
                $in[]=$item['id'];
            }
            //$this->model->from($table)->in('id',$in)->update(['isfabu'=>1]);
            return implode('{%@@@%}',$str_arr);
        }
        return '';
    }

    protected function getVideos(&$data){
        $count=count($data);
        $str='[';
        for ($i=0;$i<$count;$i++){
            $str.='{"page":'.$data[$i]['page'].',"cid":'.$data[$i]['cid'].',"title":"'.$data[$i]['part'].'"}'.',';
        }
        return '{"type":"bilibili","data":'.rtrim($str,',').']}';
    }

//后期处理===================================================

    public function dodo(){
        $table='caiji_bilibili';
        $where=[['isdone','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);

        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            echo '开始处理：id=>'.$item['id'].',from_id=>'.$item['from_id'].'---------------'.PHP_EOL;
            //$length=mb_strlen(str_replace("\n",'',$item['content']));
            $content='<p>'.preg_replace('/\n+/','<br>',$item['content']).'</p>';
            if($this->model->from($table)->eq('id',$item['id'])->update([
                'isdone'=>1,
                'content'=>$content,
                //'length'=>$length
            ]))
                echo '  成功：更新'.PHP_EOL;
            else
                echo '  失败：更新'.PHP_EOL;
            //msleep(4200,2000);
        });
    }

}
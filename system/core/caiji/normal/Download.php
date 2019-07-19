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


namespace core\caiji\normal;


use extend\Helper;

class Download extends Base
{
    /**
     * @var array 设置
     */
    protected $option=[
        'save_path'=>'public/uploads/images/wzb',
        'replace_path'=>'{%Y%}/{%m%}/{%d%}/{%u%}',
        'save_type'=>'from_cid',//format,from_cid,from_fid
        'plugin_after'=>'',
        'plugin_before'=>'',
        'date_from'=>'',//时间取自哪个标签 格式 tag{%|||%}int|string
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
    ];

    /**
     * Fabu constructor:构造函数，载入公共初始化项的同时，进行任务初始化
     * @param $option
     */
    protected function __construct($option)
    {
        parent::__construct($option);
        $this->taskInit();
    }
    /** ------------------------------------------------------------------
     * 任务初始化
     *---------------------------------------------------------------------*/
    protected function taskInit(){
        $this->model=app('\core\Model');
        $this->model->table=$this->option['downloadTable'];
        //设置最大运行数
        if(isset($this->option['run_max']) && $this->option['run_max'])
            $this->runMax=$this->option['run_max'];
        //日期来源
        if($this->option['date_from'] && is_string($this->option['date_from']) ){
            $this->option['date_from']=explode('{%|||%}',$this->option['date_from']);
        }else
            $this->option['date_from']=[];
        //curl初始化
        $this->curlInit();
    }
    /** ------------------------------------------------------------------
     * 实例化当前类，会对参数进行合法性检测，如果参数不合法会实例化失败
     * @param $option
     * @return string | \core\caiji\normal\Content : 参数合法性检测不通过 返回错误信息,否则返回实例化的当前类
     *--------------------------------------------------------------------*/
    static public function create($option=[]){
        if(!$option)
            return '参数不能为空';
        if(is_string($option))
            $option=json_decode($option,true);
        if(!is_array($option))
            return '参数格式不正确，必须是数组或能转为数组的json格式字符串';
        if(!isset($option['name']) || !$option['name'])
            return '参数没有设置采集名';
        if(!isset($option['downloadTable']) || !$option['downloadTable'])
            return '参数没有设置downloadTable';
        if(!isset($option['table']) || !$option['table'])
            return '参数没有设置table';
        $class=__CLASS__;
        return new $class($option);
    }

    /**------------------------------------------------------------------
     * 运行
     *--------------------------------------------------------------------*/
    public function run()
    {
        $this->doLoop([
            'sql'=>'select * from '.$this->prefix.$this->model->table.' where status=0 and times<4',
            'params'=>[]
        ],function($v){
            //采集
            $contentData=$this->model->from($this->option['table'])->eq('id',$v['cid'])->find(null,true);
            if(!$contentData){
                $this->model->eq('cid',$v['cid'])->delete();
                return 0;
            }
            if($this->option['plugin_before'])
                $v=$this->callback($this->option['plugin_before'],[$v]);
            if($v['true_url']){
                $time=$this->getDate($contentData);
                $ret=$this->caiji($v,$code,$time);
            }else{
                $ret=false;
                $v['times']=9999;
                $this->errorCode[9999]='true_url为空--------';
                $code=9999;
            }
            if($ret===false){
                if($v['times'] >= $this->option['tryTimes']-1){//超过最大次数
                    //修改状态
                    $this->model->eq('id',$v['id'])->update(['status'=>1]);
                    //修改占位符
                    $tag_tmp=explode(':',$v['type']);
                    $update_data[$tag_tmp[0]]=str_replace('{%@'.$v['type'].'@%}',$v['true_url'],$contentData[$tag_tmp[0]]);
                    if($this->getNoDownloadCount($v['cid'])===0)
                        $update_data['isdownload']=1;
                    $this->model->from($this->option['table'])->eq('id',$v['cid'])->update($update_data);
                } else
                    $this->model->setField('times',1,['id'=>$v['id']]);//增加一次
                return $code;
            }else {
                $plugin_after=$this->option['plugin_after'] ?? '';
                if($plugin_after)
                    $this->callback($plugin_after,[$v]);
                $this->save($v,$contentData);
                return 0;
            }
        },[
            'from'=>$this->model->table,
            'where'=>[['status','eq',0],['times','lt',4]],
            'do'=>function($notDoCount){
                $data=[
                    'method_param'=>'',
                    'status'=>0,
                    'type'=>1,//0每天执行，1只执行一次
                    'run_time'=>time()
                ];
                //本次任务有采集内容下载完成时，就要把发布添加到队列
                if($this->total['end']>0){
                    $data['callback']='\core\caiji\normal\Fabu';
                    //固定发布，所有任务一样
                    $data['class_param']=$this->option['name'];
                    $this->addQueue($data,true);
                }
                //没有未完成的下载采集，就要把下载采集从队列中去除
                if($notDoCount<=0){
                    $data['callback']='\core\caiji\normal\Download';
                    $data['class_param']=$this->option['name'];
                    $this->model->_exec('update `'.$this->prefix.'caiji_queue` set status=1 where status=0 and name_md5=?',[md5($data['callback'].$data['class_param'].$data['method_param'])],false);
                }
            }
        ]);
    }

    /** ------------------------------------------------------------------
     * caiji_download
     * @param array $v:数据中的单条记录
     * @param int $code:
     * @param int $time
     * @return bool|int
     *---------------------------------------------------------------------*/
    protected function caiji(&$v,&$code,$time=-1){
        $v=$this->getPath($v,$time);
        $code=0;
        //curl 部分
        $this->option['curl']['options']=$this->option['curl']['options']??[];
        $this->option['curl']['options']['saveFile']=ROOT.'/'.$v['save_path'];
        //dump($v);
        $ret=$this->curl->add($v['true_url'],[],$this->option['curl']['options']);
        //$ret=$this->curl->download($v['true_url'],ROOT.'/'.$v['save_path'],'get');
        if($ret===false) {
            $this->errorCode[1]='caiji bu dao message<<<'.$this->curl->errorMsg.'>>>';
            $code=1;
            return false;
        }
        if(isset($v['__suffix__']) ){
            if($v['__suffix__']==false){
                //获取后缀
                $suffix=$this->getSuffix($v);
                if($suffix !==''){
                    rename(ROOT.'/'.$v['save_path'], ROOT.'/'.$v['save_path'].$suffix);
                    $v['replace_path'].=$suffix;
                    $v['save_path'].=$suffix;
                }
            }
            unset($v['__suffix__']);
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 路径获取
     * @param $data
     * @param int $time
     * @return mixed
     *---------------------------------------------------------------------*/
    protected function getPath($data,$time=-1){
        $data['source_url']=$data['source_url'] ?? '';
        $num=strrchr($data['type'],':');
        if($num!==false){
            //$data['type']=str_replace($num,'',$data['type']);
            $num=ltrim($num,':');
        }else{
            $num='';
        }
        //后缀
        $parUrl=parse_url(rtrim(trim($data['true_url']),'.'));
        $suffix=strrchr(basename($parUrl['path']),'.');
        if($suffix===false)
            $suffix='';
        switch ($this->option['save_type']){
            case 'format':
                if($data['replace_path'])
                    $data['replace_path']=$this->format($data['replace_path'],$data['cid'],$num,$time);
                else
                    $data['replace_path']=$this->format($this->option['replace_path'],$data['cid'],$num,$time);
                break;
            default:
                $data['replace_path']=get_path_from_id($data['cid']).'/'.Helper::uuid();
        }
        $data['replace_path']='/'.$this->option['save_path'].'/'.$data['replace_path'];
        $data['save_path']='public'.$data['replace_path'];
        if($suffix){
            $data['replace_path'].=$suffix;
            $data['save_path'].=$suffix;
        }else{
            $data['__suffix__']=false;
        }
        if(isset($this->option['pre_url']) && $this->option['pre_url']){
            $data['replace_path']=$this->option['pre_url'].$data['replace_path'];
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * 获取附件的后缀
     * @param $data
     * @return string
     *--------------------------------------------------------------------*/
    protected function getSuffix($data){
        $suffix=\extend\ImageResize::getImagExtendName(ROOT.'/'.$data['save_path']);
        return $suffix;
    }

    protected function save($data,$contentData){
        //替换下载文件和图片为本地链接
        $contentData=$this->replaceTag($data,$contentData);
        if($this->getNoDownloadCount($data['cid']) <=1){
            $contentData['isdownload']=1;
            $this->total['end']++;
        }
        $this->debug([$contentData],'......上面为content表最终更新数据',false);

        $data['status']=1;
        $this->debug([$data],'......上面为download表最终更新数据',true);
        //更新content表
        $this->model->from($this->option['table'])->eq('id',$data['cid'])->update($contentData);
        //download表更新
        echo '  更新数据：cid=>'.$data['cid'].',replace_path=>'.$data['replace_path'].' ……'.PHP_EOL;
        $this->model->eq('id',$data['id'])->update([
            'source_url'=>$data['source_url'],
            'true_url'=>$data['true_url'],
            'save_path'=>$data['save_path'],
            'replace_path'=>$data['replace_path'],
            'status'=>$data['status']
        ]);
        msleep(200,100);
    }

    /** ------------------------------------------------------------------
     * 替换内容中对应的标签点位符: ['imag']['search'][]='{%img'.$keyr.'img%}'
     * @param array $data
     * @param array $contentData
     * @return array
     *---------------------------------------------------------------------*/
    protected function replaceTag($data,$contentData){
        if($data['source_url']){
            $res=explode('{%|||%}',$data['source_url']);
        }
        $res[]=$data['type'];
        $ret=[];
        foreach ($res as $item){
            list($tag,$num)=explode(':',$item);
            $ret[$tag]=str_replace('{%@'.$item.'@%}',$data['replace_path'],$contentData[$tag],$count);
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 获取时间戳
     * @param  array $data
     * @return int
     *--------------------------------------------------------------------*/
    protected function getDate($data){
        if($this->option['date_from']){
            if(isset($data[$this->option['date_from'][0]]) && isset($this->option['date_from'][1])){
                $str=$data[$this->option['date_from'][0]];
                if($this->option['date_from'][1]==='int'){
                    $time=(int) $str;
                    return $time>0 ? $time : -1;
                }else if($this->option['date_from'][1]==='string'){
                    $time=strtotime($str);
                    if($time===false)
                        $time=-1;
                    return $time;
                }
            }
        }
        return -1;
    }

    /** ------------------------------------------------------------------
     * 获取一个任务下未完成的所有下载的个数
     * @param int $cid
     * @return int
     *--------------------------------------------------------------------*/
    protected function getNoDownloadCount($cid){
        return $this->model->count(['where'=>[['cid','eq',$cid],['status','eq',0]]]);
    }

}
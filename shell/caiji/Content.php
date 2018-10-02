<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 命令格式：php cmd.php caiji/content id ，其中id为采集任务id为
 *      可选参数如下
 *          -e: 设置输出种类为直接输出，默认输出为重要信息保存日志
 *          -o: 设置单次运行，默认是循环运行
 *          -d: 开启调试模式，默认关闭
 * ======================================*/


namespace shell\caiji;
use extend\Selector;
class Content extends Base
{
    /**
     * @var array 列表模块的配置
     */
    protected $pageOption;
    /**
     * @var array 下载模块的配置
     */
    protected $downloadOption;
    /**
     * @var array 存放下载文件和图片的链接
     */
    protected $downloadData;
    /**
     * @var bool 是否有下载项,给最后入库时判断是否有未完成的下载项 参考的
     */
    protected $isHaveDownload=false;
    public function __construct(array $param)
    {
        parent::__construct($param);
        if(!$this->checkParam()){
            $this->dieEcho();
            $this->isStop=true;
            return;
        }
        $this->_init();
        $this->taskInit('content');
        //其他模块配置初始化
        $this->modleInit();
    }
    protected function checkParam()
    {
        return $this->checkParamCommon();
    }

    /** ------------------------------------------------------------------
     *入口方法
     * @return bool
     *--------------------------------------------------------------------*/
    protected function run(){
        $this->doLoop([
            'sql'=>'select * from '.$this->prefix.$this->option->table.' where caiji_iscaiji=0 and caiji_id='.$this->param[0],
            'params'=>[]
        ],function($v){
            if(!$v['source']){
                $this->errorCode[1]='source is empty.';
                $this->del($v['id']);
                return 1;
            }
            //采集
            $this->downloadData=[];
            $tmp=$this->caiji($v);
            if(is_int($tmp)){
                //删除不能为空却空了的项
                if($tmp==100)
                    $this->del($v['id']);
                return $tmp;
            }
            $down=$this->addDownload($v['id']);
            if($down || $this->isHaveDownload){
                //$tmp=$down;
                $tmp['caiji_isdown']=0;
            } else{
                $tmp['caiji_isdown']=1;
                $this->total['notdown']++;
            }
            //unset($down);
            $tmp['caiji_iscaiji']=1;
            $tmp['id']=$v['id'];
            $this->debug([$tmp],'.....上面为：最终入库数据',true);
            //保存时是否使用指定插件
            if(isset($this->option->plugin_save) && $this->option->plugin_save){
                //使用指定插件
                $this->usePlugin($this->option->plugin_save,[$tmp]);
            }else{
                //使用默认插件
                $this->callback([ '\shell\caiji\plugin\Save','task_'.$this->setting['id']],[$tmp]);
            }
            return 0;
        },[
            'from'=>$this->option->table,
            'where'=>[['caiji_iscaiji','eq',0],['caiji_id','eq',$this->param[0]]],
            'do'=>function($notDoCount){
                $data=[
                    'method_param'=>'',
                    'status'=>0,
                    'type'=>1,//0每天执行，1只执行一次
                    'run_time'=>time()
                ];
                //本次任务有已经采集 而且不用下载的项时，就要添加发布到队列
                if($this->total['notdown']>0){
                    $data['callable']='\shell\caiji\Fabu@start';
                    //固定发布，所有任务一样
                    $data['class_param']=$this->option->table.' -n 10';
                    $this->addQueue($data,true);
                }
                //没有未完成的内容采集，就去除内容采集的队列
                if($notDoCount<=0){
                    $data['callable']='\shell\caiji\Content@start';
                    $data['class_param']=$this->param[0];
                    $this->model->_exec('update `'.$this->prefix.'crontab` set status=1 where status=0 and name_md5=?',[md5($data['callable'].$data['class_param'].$data['method_param'])],false);
                }
                //本次任务有下载项时，就要添加下载采集到队列
                if($this->total['down']>0){
                    $data['callable']='\shell\caiji\Download@start';
                    $data['class_param']=$this->param[0];
                    $this->addQueue($data,true);
                }
            }
        ]);
        return 0;
    }

    /** ------------------------------------------------------------------
     * caiji
     * @param array $v:数据中的单条记录
     * @return bool|array 需要update的项
     *---------------------------------------------------------------------*/
    protected function caiji($v){
        //$plugin=$this->option->plugin_single;
        if(isset($this->option->plugin_all_before) && $this->option->plugin_all_before)
            $v=$this->usePlugin($this->option->plugin_all_before,[$v]);
        //curl 部分
        $this->option->curl['options']=$this->option->curl['options']??[];
        $html=$this->curl->add($v['source'],[],$this->option->curl['options']);
        $html=$this->curl->encoding($html);
        if($html===false){
            $this->errorCode[2]='caiji bu dao:message<<<'.$this->curl->errorMsg.'>>>';
            return 2;
        }
        if($html===''){
            $this->errorCode[4]='caiji bu dao:is empty';
            return 4;
        }
        $v=$this->query($html,$v['source'],$v);
        if(! is_int($v)){
            if(isset($this->option->plugin_all_after) && $this->option->plugin_all_after)
                $v=$this->usePlugin($this->option->plugin_all_after,[$v]);
        }
        return $v;
    }

    protected function query($html,$url,$data)
    {
        $ret = [];
        //循环获取每个标签的值
        foreach ($this->option->caiji as $k => $item) {
            $item->match->from=$item->match->from ?? 'html';
            switch ( $item->match->from) {
                case 'html':
                    //正则匹配
                    if($item->is_loop){
                        $ret[ $k ]=Selector::findAll($html,$item->match->reg,'',$item->match->cut);
                    }else{
                        if(isset($item->match->cut) && $item->match->cut){
                            $ret[ $k ]=Selector::find(Selector::find($html,$item->match->cut),$item->match->reg);
                        }else
                            $ret[ $k ]=Selector::find($html,$item->match->reg);
                    }
                    if($ret[ $k ]===false){
                        $this->errorCode[3]= 'selector error:'.Selector::getError();
                        return 3;
                    }
                    if( is_array($ret[$k])){
                        $ret[ $k ]=implode('{%|||%}',$ret[$k]);
                    }
                    //$this->debug([$ret],'......上面为：过滤前 results',false);
                    //不为空检测
                    if ($ret[ $k ] == ''){
                        if(isset($item->not_empty) && $item->not_empty){
                            $this->errorCode[100]='Selector "'.$k.'" is empty! ';
                            return 100;
                        }else
                            continue;
                    }

                    //过滤
                    if (isset($item->filter)) {
                        $ret[$k]=$this->filter($item->filter,$ret[$k],$url);
                        //不为空检测（再检测一次）
                        if ($ret[ $k ] == ''){
                            if(isset($item->not_empty) && $item->not_empty){
                                $this->errorCode[100]='After filter ,"'.$k.'" is empty';
                                return 100;
                            }else
                                continue;
                        }
                    }

                    //文件下载
                    if(isset($item->files->type)){
                        if($item->files->type == 2){ //文件
                            $this->downloadData[$k]=$ret[$k];
                        }elseif ($item->files->type == 1){ //图片
                            $ret[$k]=$this->getImgDownload($k,$ret[$k]);
                        }
                    }
                    break;
                case 'page':
                    $data[$k]=$data[$k] ?? '';
                    $ret[$k]=$data[$k];
                    if(isset($this->pageOption["rules"]) && ($rule=end($this->pageOption["rules"]))){
                        if(isset($rule["file"][$k]['type'] ) && ($rule["file"][$k]['type']==1 || $rule["file"][$k]['type'] ==2) ){
                            $this->isHaveDownload=true;
                            $this->total['down']++;
                        }
                    }
                    break;
                case 'url':
                    break;
                case 'tags':
                    break;
                case 'fixed':
                    $ret[$k]=$item->match->remak;
                    break;
                case 'function':
                    break;
            }
        }
        $this->debug([$ret],'......上面为：各标签采集后并经过过滤的结果集',false);
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 删除某个winxinqun
     * @param $id
     *---------------------------------------------------------------------*/
    protected function del($id){
        $this->model->reset()->eq('id',$id)->delete();
        $this->model->_exec('delete FROM `'.$this->prefix.'tag_relation` WHERE tid = ? and type= ?',[$id,'weixinqun'],false);
    }
    //添加数据到 caiji_download
    protected function addDownload($id){
        if(empty($this->downloadData))
            return false;
        $this->debug([$this->downloadData],'.....上面为：downloadData',false);
        //1、下载项分离重复的
        $data_download=\extend\Helper::array_delet_repeat($this->downloadData);
        $this->downloadData=[];
        //2、合并
        $data_download=$this->down_merge($data_download);
        //2、不相同项保存到下载表
        $this->model->table=$this->downloadOption['table'];
        foreach ($data_download['unique'] as $k => $v){
            $num=strrchr($k,':');
            if($num!==false){
                $tag=str_replace($num,'',$k);
                $num=ltrim($num,':');
            }else{
                $tag=$k;
                $num='';
            }
            $replace_path=$this->option->caiji ->{$tag}->files->replace_path ?? '';
            //不是测试时才入库
            if(! $this->debug){
                if(is_array($v)){
                    $true_url=$v[0];
                    $source_url=$v[1];//'aaa:0{%|||%}aaa:1{%|||%}bbb'
                }else{
                    $true_url=$v;
                    $source_url='';
                }
                if($this->model->insert([
                    'source_url'=>$source_url,
                    'true_url'=>$true_url,
                    'replace_path'=>$replace_path,
                    //'save_path'=>trim($this->downloadOption['save_path'],'/').'/'.$replace_path,
                    'type'=>$tag.($num ? ':'.$num : ''),
                    'cid'=>$id
                ]) >0 ){
                    //下载项计数+1
                    $this->total['down']++;
                }

            }
            //if (isset($this->option->caiji ->{$tag}->files->pre_url))
                //$data_download['unique'][$k]=$this->option->caiji ->{$tag}->files->pre_url.$replace_path;
        }
        $this->model->reset()->table=$this->option->table;
        return true;
        //整合下载项
        //$arr=$this->down_merge($data_download);
        //$this->debug([$arr],'.....上面为：整合下载项',false);
        //采集项有下载内容的替换为本地下载的链接
        //return $this->down_replace($arr,$data);
    }

    /** ------------------------------------------------------------------
     * 整合下载项
     * @param array $down
     * @return array
     *--------------------------------------------------------------------*/
    protected function down_merge($down){
        foreach ($down['unique'] as $k1 =>$v1){
            $tag=[];
            foreach ($down['change'] as  $k2=>$v2){
                if($k1===$v2){
                    $tag[]=$k2;
                }
            }
            if($tag){
                $down['unique'][$k1]=[$v1,implode('{%|||%}',$tag)];
	        }
        }
        return $down;
    }

    /** ------------------------------------------------------------------
     * 替换文件为本地文件路径，替换图片占位符为本地路径
     * @param array $down
     * @param array $data
     * @return array 返回替换后的结果集
     *--------------------------------------------------------------------*/
    protected function down_replace($down,$data){
        foreach ($data as $k=>$v){
            if(isset($down[$k])){
                if(! is_array($down[$k])){
                    $data[$k]=$down[$k];
                }else{
                    $search=[];
                    $replace=[];
                    foreach ($down[$k] as $key =>$item ){
                        $search[]='{%img'.$key.'img%}';
                        $replace[]=$item;
                    }
                    $data[$k]=str_replace($search,$replace,$v);
                }
            }
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * 建立下载文件的数据表
     *---------------------------------------------------------------------*/
    protected function modleInit(){
        $this->downloadOption=json_decode($this->setting['download'],true);
        $this->pageOption=json_decode($this->setting['page'],true);
        if(!isset($this->downloadOption['table']) || !$this->downloadOption['table'])
            $this->downloadOption['table']='caiji_download_'.$this->setting['id'];
        $sql='CREATE TABLE IF NOT EXISTS `'.$this->prefix.$this->downloadOption['table'].'` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`source_url` TEXT NULL COMMENT \'原始文件地址\',
	`true_url` TEXT NULL COMMENT \'真实文件地址\',
	`save_path` TEXT NULL COMMENT \'完整保存路径\',
	`replace_path` TEXT NULL COMMENT \'写入内容表的路径\',
	`status` TINYINT(1) NOT NULL DEFAULT \'0\',
	`upload` TINYINT(1) NOT NULL DEFAULT \'0\',
	`type` VARCHAR(50) NOT NULL DEFAULT \'\',
	`cid` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
	`times` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
	PRIMARY KEY (`id`),
	INDEX `cid` (`cid`)
)COLLATE=\'utf8mb4_general_ci\' ENGINE=InnoDB;';
        $this->model->_exec($sql,[],false);
    }

    /** ------------------------------------------------------------------
     * 添加要下载的图片到 $this->downloadData中，同时把标签内容中的图片换成特殊的占位符
     * @param string $tag
     * @param string $content
     * @return string
     *--------------------------------------------------------------------*/
    public function getImgDownload($tag,$content){
        $reg='#<img ([^<>]*?)src=([\'"]?)([^\s<>"\']*)\2([^>]*)>#i';
        $i=0;
        $content= preg_replace_callback($reg,function($match)use($tag,&$i){
            $ret='';
            if($match[3]){
                $this->downloadData[$tag.':'.$i]=$match[3];
                $i++;
                $ret='{%img'.$tag.':'.$i.'img%}';
            }
            return '<img '.$match[1].'src="'.$ret.'"'.$match[4].'>';
        },$content);
        return $content;
    }

    public function doTest($url,$rule=''){
        $this->debug=true;
        //$this->outType=1;
        if($rule)
            $this->option->caiji=json_decode(json_encode($rule));
        $this->caiji(['source'=>$url]);
    }

}
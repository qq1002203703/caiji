<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 命令格式：php cmd.php caiji/download id ，其中id为采集任务id为
 *      可选参数如下
 *          -e: 设置输出种类为直接输出，默认输出为重要信息保存日志
 *          -o: 设置单次运行，默认是循环运行
 *          -d: 开启调试模式，默认关闭
 * ======================================*/


namespace shell\caiji;

class Download extends Base
{
    protected $contentOption;
    public function __construct(array $param)
    {
        parent::__construct($param);
        if(!$this->checkParam()){
            $this->dieEcho();
            $this->isStop=true;
            return;
        }
        $this->_init();
        $this->taskInit('download');
    }
    protected function checkParam(){
       return $this->checkParamCommon();
    }

    protected function run(){
        $this->doLoop([
            'sql'=>'select * from '.$this->prefix.$this->model->table.' where status=0 and times<4',
            'params'=>[]
        ],function($v){

            //采集
            $ret=$this->caiji($v,$code);
            if($ret===false){
                $this->model->_exec('UPDATE `'.$this->prefix.$this->model->table.'` set `times` = `times`+1  where id=? ',[$v['id']],false);
                return $code;
            }else {
               $this->save($v);
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
                if($this->total['notdown']>0){
                    $data['callable']='\shell\caiji\Fabu@start';
                    //固定发布，所有任务一样
                    $data['class_param']=$this->getContentTable().' -n 10';
                    $this->addQueue($data,true);
                }
                //没有未完成的下载采集，就要把下载采集从队列中去除
                if($notDoCount<=0){
                    $data['callable']='\shell\caiji\Download@start';
                    $data['class_param']=$this->param[0];
                    $this->model->_exec('update `'.$this->prefix.'crontab` set status=1 where status=0 and name_md5=?',[md5($data['callable'].$data['class_param'].$data['method_param'])],false);
                }
            }
        ]);
        return 0;
    }


    /** ------------------------------------------------------------------
     * caiji_download
     * @param array $v:数据中的单条记录
     * @param int $code:
     * @return bool|int
     *---------------------------------------------------------------------*/
    protected function caiji(&$v,&$code){
        $v=$this->getPath($v);
        $code=0;
        $plugin_before=$this->option->plugin_before ?? '';
        if($plugin_before)
            $v=$this->usePlugin($plugin_before,[$v]);
        //curl 部分
        $this->option->curl['options']=$this->option->curl['options']??[];
        $this->option->curl['options']['saveFile']=ROOT.'/'.$v['save_path'];
        //dump($v);
        $ret=$this->curl->add($v['true_url'],[],$this->option->curl['options']);
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
        $plugin_after=$this->option->plugin_after ?? '';
        if($plugin_after)
            $this->usePlugin($plugin_after,[$v]);
        return $ret;
    }

    protected function getPath($data){
        $data['source_url']=$data['source_url'] ?? '';
        $num=strrchr($data['type'],':');
        if($num!==false){
            //$data['type']=str_replace($num,'',$data['type']);
            $num=ltrim($num,':');
        }else{
            $num='';
        }
        //后缀
        $parUrl=parse_url($data['true_url']);
        $suffix=strrchr(basename($parUrl['path']),'.');
        if($suffix===false)
            $suffix='';
        if($data['replace_path']){
            $data['replace_path']=$this->format($data['replace_path'],$data['cid'],$num,$data['true_url']);
        }else{
            $data['replace_path']=$this->format($this->option->replace_path,$data['cid'],$num,$data['true_url']);
        }
        $data['save_path']=$this->option->save_path.'/'.$data['replace_path'];
        if($suffix){
            $data['replace_path'].=$suffix;
            $data['save_path'].=$suffix;
        }else{
            $data['__suffix__']=false;
        }
        if(isset($this->option->pre_url) && $this->option->pre_url){
            $data['replace_path']=$this->option->pre_url.$data['replace_path'];
        }
        return $data;
    }

    protected function getSuffix($data){
        $suffix=\extend\ImageResize::getImagExtendName(ROOT.'/'.$data['save_path']);
        return $suffix;
    }

    protected function save($data){
        //替换下载文件和图片为本地链接
        $this->model->reset()->table=$this->getContentTable();
        $contentData=$this->model->eq('id',$data['cid'])->find(null,true);
        $contentData=$this->replaceTag($data,$contentData,$num);
        $count=$this->model->count(['where'=>[['cid','eq',$data['cid']],['status','eq',0],['times','lt',4]]]);
        if($count==0){
            $contentData['caiji_isdown']=1;
            $this->total['notdown']++;
        }
        $this->debug([$contentData],'......上面为content表被替换后的数据',false);
        //更新content表
        $this->model->eq('id',$data['cid'])->update($contentData);
        $this->model->reset()->table=$this->option->table;

        //download表更新
        if($num!==false){
            $data['type']=str_replace($num,'',$data['type']);
        }
        $data['status']=1;
        $this->debug([$contentData],'......上面为download表最终入库数据',false);
        $this->model->eq('id',$data['id'])->update($data);
    }

    //['imag']['search'][]='{%img'.$keyr.'img%}'
    protected function replaceTag($data,$contentData,&$num){
        if($data['source_url']){
            $res=explode('{%|||%}',$data['source_url']);
        }
        $res[]=$data['type'];
        foreach ($res as $item){
            if(strpos($item,':')!==false){
                list($tag,$num)=explode(':',$item);
                str_replace('{%img'.$item.'img%}',$data['replace_path'],$contentData[$tag]);
                //$ret[$tag]['search'][]='{%img'.$item.'img%}';
                //$ret[$tag]['replace'][]=$replace_path;
            }else{
                $contentData[$item]=$data['replace_path'];
                //$ret[$item]=$replace_path;
            }
        }
        return $contentData;
    }

    public function doTest(){

    }
}
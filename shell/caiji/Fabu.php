<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 命令格式：php cmd caiji/fabu tableName ，其中tableName为数据采集时保存在的表名称
 *      可选参数如下
 *          -e: 设置输出种类为直接输出，默认输出为重要信息保存日志
 *          -o: 设置单次运行，默认是循环运行
 *          -d: 开启调试模式，默认关闭
 *          -n \d+: 设置最大运行次数为-n后面的数字，默认为10（无限）
 * ======================================*/

namespace shell\caiji;

class Fabu extends Base
{
    /**
     * @param array $param
     */
    public function __construct(array $param){
        parent::__construct($param);
        if(!$this->checkParam()){
            $this->dieEcho();
            $this->isStop=true;
            return;
        }
        $this->model->table=$this->param[0];
        $this->_init();
        //添加命令参数 -n \d+
        if(($i=array_search('-n',$this->param,true)) ){
            $i=(int) $this->param[$i+1];
            if($i >0 )
                $this->runMax=$i;
        }else{
            $this->runMax=10;
        }
    }
    protected function checkParam()
    {
        if(empty($this->param) || !isset($this->param[0]) || !$this->param[0]){
            $this->error='命令缺少必要的参数';
            return false;
        }
        if(!$this->model->_sql('show tables like \''.$this->prefix.$this->param[0].'\'',[],false)){
            $this->error='数据库中不存在表 "'.$this->param[0].'"';
            return false;
        }
        return true;
    }

    protected function run(){
        $this->doLoop([
            'sql'=>'select * from '.$this->prefix.$this->model->table.' where caiji_isfabu=0 and caiji_iscaiji=1 and caiji_isdown=1',
            'params'=>[]
        ],function($data){
            if($data['thumb'] || $data['qrcode'] || $data['qun_qrcode'])
                $have_img=1;
            else
                $have_img=0;
            $ret=$this->model->eq('id',$data['id'])->update(['caiji_isfabu'=>1,'status'=>1,'have_img'=>$have_img]);
            if($ret>0)
                return 0;
            else{
                $this->errorCode[1]='更新失败';
                return 1;
            }
        },[
            'from'=>$this->model->table,
            'where'=>[['caiji_isfabu','eq',0],['caiji_iscaiji','eq',1],['caiji_isdown','eq',1]],
            'do'=>function($notDoCount){
                //没有未完成的发布任务，就要把发布任务从队列中去除,否则会保留
                if($notDoCount<=0){
                    $name_md5=md5('\shell\caiji\Fabu@start'.$this->model->table);
                    $this->model->_exec('update `'.$this->prefix.'crontab` set status=1 where name_md5=?',[$name_md5],false);
                }
            },
        ]);
    }


}
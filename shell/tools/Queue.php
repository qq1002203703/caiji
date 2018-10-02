<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 任务队列类
 * php cmd tools/queue run|create
 *      可选项：
 *                  -e: 设置输出种类为直接输出
 * ======================================*/
namespace shell\tools;

use shell\BaseShell;
use \core\Conf;
use \core\lib\cache\File;
use \extend\Helper;
class Queue extends BaseShell
{
    protected $param;
    protected $model;
    protected $prefix;
    protected $path=ROOT.'/cache/caiji/queque';
    protected $outType=2;//1为直接输出，2为重要信息保存为日志，可以传第二个参数'-e'来改变此值
    protected $debug=false;
    protected $runOnce=0;
    protected $total=0;
    public function __construct($param=[]){
        if(! $param ){
            $this->param=['help'];
            return;
        }
        $this->param = $param;
        $this->_setCommandOptions(['-e'=>['outType',1],'-d'=>['debug',true],'-o'=>['runOnce',true]],$this->param);
        parent::__construct();
        $this->model=app('\core\Model');
        $this->model->table='caiji_queue';
        $this->prefix=Conf::get('prefix','database');
        $this->outPut('Date:'.date('Y-m-d H:i:s').','.$this->param[0].' running--------------------------------'.PHP_EOL,true);
    }

    /** ------------------------------------------------------------------
     * cli模式下入口
     *--------------------------------------------------------------------*/
    public function start()
    {
        switch ($this->param[0]){
            case  'run':
                $this->run();
                break;
            case 'create':
                $this->create();
                break;
            case 'page':
                $this->page();
                break;
            case 'caiji':
                $this->caiji();
                break;
            case 'test':
                $this->test();
                break;
            case 'help':
            default:
            echo '  1)create queue useg: php cmd tools/queue create'.PHP_EOL;
            echo '  2)run queue useg: php cmd tools/queue run'.PHP_EOL;
            echo '      可选参数:   -e:设置输出种类为直接输出'.PHP_EOL;
        }
        $this->goodbye();
    }

    /**------------------------------------------------------------------
     * 执行队列任务（每天运行多次，可以设置crontab每隔十多分钟执行一次）
     * @return int
     *-------------------------------------------------------------------*/
    public function run(){
        //把队列数据表中状态为0，时间已达到的任务查出
        $res=$this->model->eq('status',0)->lt('run_time',TIME)->limit(100)->findAll(true);
        if($res){
            foreach ($res as  $re){
                if(strpos($re['callback'],'@')>0){
                    $callback=explode('@',$re['callback']);
                }else{
                    $callback=$re['callback'];
                }
                if(is_callable($callback)){
                    if(is_array($callback)){
                        call_user_func_array([new $callback[0](explode(' ',$re['class_param'])),$callback[1]],explode(' ',$re['method_param']));
                    }else{
                        call_user_func_array($callback,explode(' ',$re['method_param']));
                    }
                    $this->total++;
                    if($re['del_type']==1){
                        $this->model->table='caiji_queue';
                        $this->model->eq('id',$re['id'])->update(['status'=>1]);
                    }
                }else{
                    $this->model->table='caiji_queue';
                    $this->model->reset()->eq('id',$re['id'])->delete();
                    $this->outPut(' The function can not callback : '.$re['callback'].PHP_EOL,true);
                }
            }
        }
        return 0;
    }
    /**------------------------------------------------------------------
     * 创建队列任务 （每天运行一次，设置crontab每天00：01执行一次）
     * @return int
     *-------------------------------------------------------------------*/
    public function create(){
        //删除已经执行过的一次性任务
        $this->model->eq('type',1)->eq('status',1)->delete();
        //把队列任务中种类为每天执行的，恢复为未执行状态,并把执行时间改为当天的时间
        $data=$this->model->eq('type',0)->eq('status',1)->findAll(true);
        //dump($data);
        if(!$data)
            return 0;
        foreach ($data as $item){
            $time=strtotime(date('Y-m-d'))+$item['run_time']-strtotime(date('Y-m-d',$item['run_time']));
            $this->model->eq('id',$item['id'])->update([
                'run_time'=>$time,
                'status'=>0
            ]);
            $this->total++;
        }
        return 0;
    }

    public function caiji(){
        $res=$this->model->from('caiji_queue')->eq('status',0)->lt('run_time',time())->limit(100)->findAll(true);
        if(!$res)
            return 0;
        foreach ($res as  $re){
            $caijiRule=$this->getCaijiRules($re['class_param'],basename(str_replace('\\','/', $re['callback'])));
            if($caijiRule===false){
                $this->update('caiji_queue',$re['id'],['status'=>1]);
                $this->outPut(' 无法获取到规则,id:'.$re['id'].PHP_EOL,true);
                continue;
            }
            $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
            if(is_object($callback)) {
                $callback->start();
                $this->total++;
                if($re['del_type']==1){
                    $this->update('caiji_queue',$re['id'],['status'=>1]);
                }
            }else{
                $this->update('caiji_queue',$re['id'],['status'=>1]);
                $this->outPut(' '.$callback.',id:'.$re['id'].PHP_EOL,true);
            }
        }
        return 0;
    }
    /** ------------------------------------------------------------------
     * 列表页队列
     * @return int
     *---------------------------------------------------------------------*/
    public function page(){
        $this->model->table='caiji_page';
        $time=time()-3600*24;
        $data=$this->model->_sql('select * from '.$this->prefix.$this->model->table.' where status=1 and (type=0 or update_time< ? ) order by update_time limit 100',[$time],false);
        if(!$data)
            return 0;
        foreach ($data as $item){
            //$rule=$this->model->_sql('select * from '.$this->prefix.'caiji where id=?',[$item['rule_id']],false);
            $caijiRule=$this->getCaijiRules($item['name'],'page',$item['options']);
            if($caijiRule===false){
                $this->update('caiji_page',$item['id'],['update_time'=>time()]);
                $this->outPut(' 无法获取到规则,pageId:'.$item['id'].PHP_EOL,true);
                continue;
            }
            $caijiRule['url']=$item['url'];
            $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
            if(is_object($callback)) {
                $callback->start();
            }else{
                $this->outPut(' '.$callback.',pageId:'.$item['id'].PHP_EOL,true);
            }
            $this->update('caiji_page',$item['id'],['update_time'=>time()]);
        }
        return 0;
    }
    /** ------------------------------------------------------------------
     * 输出: this->outType==2时会把重要信息写到日志里; this->outType==1时，直接输出
     * @param string $msg 信息
     * @param bool $important 是否是重要信息
     *--------------------------------------------------------------------*/
    protected function outPut($msg,$important=false){
        if($this->outType ==2 && $important){
            File::write($this->path.'.log',$msg,true);
        }elseif($this->outType ==1){
            echo $msg;
        }
    }

    public function goodbye(){
        $this->outPut('Date:'.date('Y-m-d H:i:s').','.$this->param[0].' end!-----------------------------------'.PHP_EOL.PHP_EOL.PHP_EOL,true);
        parent::goodbye();
    }

    protected function test(){
        $caijiRule=$this->getCaijiRules('zuanke8.com','content');
        if(!$caijiRule){
            echo '规则名不正确';
            return;
        }
        $caiji=\core\caiji\normal\Content::create($caijiRule);
        if(is_object($caiji)){
            $caiji->doTest('http://www.zuanke8.com/thread-5297706-1-1.html');
        }else{
            dump($caiji);
        }
    }

    /** ------------------------------------------------------------------
     * 读取采集规则
     * @param string $name 采集规则名
     * @param string $type  种类：page/content/download/fabu
     * @param null|string $customOpt 自定义项 json格式字符串
     * @return array|bool
     *--------------------------------------------------------------------*/
    protected function getCaijiRules($name,$type,$customOpt=null){
        $type=strtolower($type);
        $caijiRule=Conf::get($type,$name,null,'config/caiji/');
        if(!$caijiRule){
            return false;
        }
        if($customOpt){
            $item_options=json_decode($customOpt,true);
            if(isset($item_options[$type])){
                $caijiRule=array_merge($caijiRule,$item_options[$type]);
            }
            unset($item_options);
        }
        $options=Conf::get('options',$name,null,'config/caiji/');
        if($options){
            $caijiRule=array_merge($caijiRule,$options);
        }
        unset($options);
        $caijiRule['name']=$name;
        //$caijiRule['url']=$item['url'];
        $caijiRule['callback'].=ucfirst($type);
        $caijiRule['outType']=$this->outType;
        $caijiRule['runOnce']=$this->runOnce;
        $caijiRule['debug']=$this->debug;
        return $caijiRule;
    }

    protected function update($table,$id,$data){
        $this->model->table=$table;
        $this->model->eq('id',$id)->update($data);
    }
}
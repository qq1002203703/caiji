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
    protected $startId=0;
    protected $maxId=0;
    protected $total=0;
    public function __construct($param=[]){
        if(! $param ){
            $this->param=['help'];
            return;
        }
        $this->param = $param;
        $this->_setCommandOptions(['-e'=>['outType',1],'-d'=>['debug',true],'-o'=>['runOnce',true],'-s'=>['startId'],'-m'=>['maxId']],$this->param);
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
            case 'getlist':
                $this->getlist();
                break;
            case 'test':
                $this->test();
                break;
            /*case 'do':
                $this->do();
                break;*/
            case 'help':
            default:
                echo '  注：下面说到的name为任务对应的名称，不是一个固定值，是一个变量'.PHP_EOL;
            echo '  1.可选参数:'.PHP_EOL.
                    '       -e : 设置输出种类为直接输出'.PHP_EOL.
                    '       -d : 设置模式为测试模式'.PHP_EOL.
                    '       -o : 只运行一次'.PHP_EOL.
                    '       -s n : 设置最小的id,n为数字'.PHP_EOL.
                    '       -m n : 设置最大的id,n为数字'.PHP_EOL;
                    echo '      注：下面所有命令都可以使用上面的参数'.PHP_EOL;
            echo '  2.全部命令列表:'.PHP_EOL;
            echo '      2.1 采集测试:'.PHP_EOL.
                '       命令格式: php cmd tools/queue test name page|content|getlist|fabu'.PHP_EOL.
                '       注：page|content|getlist|fabu为采集种类，必须指定其中一种'.PHP_EOL;
            echo '      2.2 列表页采集:'.PHP_EOL.
                            '       2.2.1 全部任务的列表页采集: php cmd tools/queue page'.PHP_EOL.
                            '       2.2.2 指定任务名的列表页采集: php cmd tools/queue page name'.PHP_EOL;
            echo '      2.2 run queue useg: php cmd tools/queue run'.PHP_EOL;
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
                        $this->model->from('caiji_queue')->eq('id',$re['id'])->update(['status'=>1]);
                    }
                }else{
                    $this->model->from('caiji_queue')->eq('id',$re['id'])->delete();
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
        if(isset($this->param[1]) && $this->param[1]){
            $res=$this->model->from('caiji_queue')->eq('class_param',$this->param[1])->eq('status',0)->lt('run_time',time())->limit(100)->findAll(true);
        }else{
            $res=$this->model->from('caiji_queue')->eq('status',0)->lt('run_time',time())->limit(100)->findAll(true);
        }
        //dump($res);
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
        //dump($this->param);
        if(isset($this->param[1]) && $this->param[1]){
            $data=$this->model->_sql('select * from '.$this->prefix.$this->model->table.' where name=? and status=1 and (type=0 or update_time< ? ) order by update_time limit 100',[$this->param[1],$time],false);
        }else{
            $data=$this->model->_sql('select * from '.$this->prefix.$this->model->table.' where status=1 and (type=0 or update_time< ? ) order by update_time limit 100',[$time],false);
        }
        if(!$data){
            $this->outPut(' 队列不存在需要进行的采集任务'.PHP_EOL,false);
            return 0;
        }

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

    public function getlist(){
        //dump($this->param);
        $name=$this->param[1] ?? '';
        if(!$name){
            $this->outPut(' 请输入第二个参数用来指定规则名'.PHP_EOL);
            return 1;
        }
        $caijiRule=$this->getCaijiRules($name,'getlist');
        if($caijiRule===false){
            $this->outPut(' 无法获取到规则'.PHP_EOL,true);
            return 2;
        }
        $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
        if(is_object($callback)) {
            $callback->start();
        }else{
            $this->outPut(' '.$callback.PHP_EOL,true);
            return 3;
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
        //需要提供，$name、$type和doTest的参数
        //dump($this->param);return;
        if(!isset($this->param[1]) || !isset($this->param[2])){
            echo  ' 参数不完整,第一个是名称，第二个参数是种类包括 page|content|fabu|getlist'.PHP_EOL;
            echo  ' 如：php cmd tools/queue test zuanke8 page'.PHP_EOL;
            return;
        }

        $name=$this->param[1];
        $type=$this->param[2];
        $caijiRule=$this->getCaijiRules($name,$type);
        if(!$caijiRule){
            echo '规则名不正确';
            return;
        }
        $method='test_'.$type;
        $this->$method($caijiRule);
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
            unset($options);
        }
        $caijiRule['name']=$name;
        //$caijiRule['url']=$item['url'];
        $caijiRule['callback'].=ucfirst($type);
        $caijiRule['outType']=$this->outType;
        $caijiRule['runOnce']=$this->runOnce;
        $caijiRule['debug']=$this->debug;
        $caijiRule['startId']=$this->startId;
        $caijiRule['maxId']=$this->maxId;
        return $caijiRule;
    }

    protected function update($table,$id,$data){
        //$this->model->table=$table;
        $this->model->from($table)->eq('id',$id)->update($data);
    }

    public function test_page($caijiRule){
        $caijiRule['url']='https://www.laoliboke.com/page_53.html';
        $caiji='\core\caiji\normal\Page';
        $caiji=call_user_func($caiji.'::create',$caijiRule);
        if(is_object($caiji)){
            $caiji->doTest();
        }else{
            dump($caiji);
        }
    }

    public function test_content($caijiRule){
        $caiji='\core\caiji\normal\Content';
        $caiji=call_user_func($caiji.'::create',$caijiRule);
        if(is_object($caiji)){
            $caiji->doTest('https://www.laoliboke.com/post/3.html');
        }else{
            //echo 'bbb';
            dump($caiji);
        }
    }

    public function test_fabu($caijiRule){
        $caiji='\core\caiji\normal\Fabu';
        $caiji=call_user_func($caiji.'::create',$caijiRule);
        if(is_object($caiji)){
            $caiji->doTest('http://www.wezhubo.my/portal/fabu/start?pwd=Djidksl$$EER4ds58cmO',[
                'from_id'=>12334,
                'title'=>'标题测试',
                'content'=>'<p> <img src="http://www.zhuboimg.com/zb_users/upload/043/201809021335201535866520552911/1 (1).jpg"/> </p> <p> <img src="http://www.zhuboimg.com/zb_users/upload/043/201809021335201535866520552911/1 (2).jpg"/> </p> <p> <img src="http://www.zhuboimg.com/zb_users/upload/043/201809021335201535866520552911/1 (3).jpg"/> </p>',
                'id'=>1,
                'thumb'=>'http://www.zhuboimg.com/zb_users/upload/043/201809021335201535866520552911/zhutu.jpg',
                'category'=>'夏娃'
            ]);
        }else{
            dump($caiji);
        }
    }
    //临时测试用

}
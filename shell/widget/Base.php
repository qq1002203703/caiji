<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/

namespace shell\widget;

class  Base
{
    protected $table;
    protected $path=ROOT.'/shell/widget';
    protected $test=false;
    protected $total=0;
    protected $stopFile;
    /**
     * @var array 命令行传过来的参数
     */
    protected $param;
    /**
     * @var array 全局设置
     */
    protected $option_all;
    protected $option;
    //页数是否递进
    protected $pageAdd=false;
    /**
     * @var \app\admin\model\Caiji|object
     */
    protected $model;
    protected $outType=1;//1为直接输出，2为保存为日志文件
    /**
     * @var string 存放错误信息
     */
    protected $msg;
    /**
     * @var string 表前缀
     */
    protected $prefix;

    public function __construct($param=[])
    {
        $this->param = $param;
        $this->_init();
    }
    /** ------------------------------------------------------------------
     * 任务初始化
     *---------------------------------------------------------------------*/
    protected function _init(){
        /*if(!isset($this->param[0]) || !$this->param[0]){
            $this->param[0]='base';
        }*/
        //检测加琐文件在不在，在的话把它去掉琐，不在的话生成不加琐的stop文件
        $this->stopFile=$this->path.'/stop/'.$this->param[0];
        if(is_file($this->stopFile.'.lock')){
            rename($this->stopFile.'.lock',$this->stopFile);
        }else{
            if(!is_file($this->stopFile))
                \core\lib\cache\File::write($this->stopFile,'');
        }
        //是否是test测试
        if(isset($this->param[1])){
            $this->test=($this->param[1]=='test');
        }
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        if (intval(ini_get("memory_limit")) < 1024)
        {
            ini_set('memory_limit', '1024M');
        }
    }
    /** ------------------------------------------------------------------
     * 循环操作
     * @param array $query:绑定查询
     *          string $query['sql']:绑定查询的sql语句
     *          array $query['params']:绑定查询的参数
     * @param \Closure|string $doFunc:回调函数接收两个参数（每条记录的$k和$v），可以是闭包，也可以是本类中一个方法名
     * @param string $msg
     * @param array $endEchoExp：
     *---------------------------------------------------------------------*/
    protected function doLoop($query,$doFunc,$endEchoExp,$msg=''){
        $start=0;
        $perPage=20;
        while (1){
            if ($this->checkStop()) {
                $this->endEcho('   you must delete the sop file,'.$msg,$endEchoExp);
                break;
            }
            $data=$this->model->_sql($query['sql']." limit {$start},{$perPage}",$query['params'],false);
            if($this->pageAdd){
                $start+=$perPage;
            }
            if($data){
                foreach ($data as $k =>$v){
                    if ($this->checkStop()) {
                        $this->endEcho('   stop,'.$msg,$endEchoExp);
                        break 2;
                    }
                    echo '*******************************************start:id=' . $v['id'] . PHP_EOL;
                    $ret=false;
                    if($doFunc instanceof \Closure){
                        $ret=call_user_func($doFunc,$v);
                    }elseif(is_string($doFunc)){
                        $ret=call_user_func([$this,$doFunc],$v);
                    }
                    if($ret===0){
                        $this->total++;
                    }elseif ($ret===false) {
                        echo '  回调函数出错';
                        break 2;
                    }elseif($ret >=100){
                        $this->msg .= '  error code:'.$ret.PHP_EOL;
                    } else{
                        echo '  错误码:'.$ret.PHP_EOL;
                        if($this->outType==1){
                            echo $this->msg;
                        }
                        continue;
                    }
                    echo '*******************************************end:id=' . $v['id'] . PHP_EOL;
                }
            }else{
                $this->endEcho('   complete!'.$msg,$endEchoExp);
                break;
            }
        }
    }

    /**
     * 检测参数是否正确
     * @return bool
     */
    protected function checkParam(){
    }
    /**
     * 参数不正确时，输出提示内容
     */
    protected function dieEcho(){
        echo '命令格式错误，格式如下'.PHP_EOL;
        echo PHP_EOL;
        echo 'php tools {test} [{参数}]'.PHP_EOL;
        echo PHP_EOL;
    }
    /** ------------------------------------------------------------------
     * 任务结束输出
     * @param string $msg
     * @param array $exp
     *---------------------------------------------------------------------*/
    protected function endEcho($msg,$exp){
        $cout=$this->model->count($exp);
        echo $msg.'本次总共处理:'.$this->total.' ,剩余:'.$cout.PHP_EOL;
    }
    /**
     * 检测是否是测试，并检测加琐的stop文件是否存在
     * @return bool
     */
    protected function checkStop(){
        if($this->test && $this->total > 0)
            return true;
        return file_exists($this->stopFile.'.lock');
    }
    /**
     * 使用插件
     * @param string $plugin
     * @param array $params
     * @return mixed
     */
    protected function use_plugin($plugin,$params){
        $plugin=explode('@',$plugin);
        if(count($plugin)==1){
            return call_user_func_array($plugin[0],$params);
        }else{
            return call_user_func_array([new $plugin[0],$plugin[1]],$params);
        }
    }

    public function run(){
        $this->dieEcho();
    }


}

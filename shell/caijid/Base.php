<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *开启浏览器驱动进行采集的Base类---基础类，其他采集类继承它
 * ======================================*/
namespace shell\caiji;
use shell\CaijiCommon;
use core\lib\cache\File;
use extend\Helper;
use extend\Curl;
use app\admin\model\Caiji;

abstract class  Base extends CaijiCommon
{
    protected $path=ROOT.'/shell/caiji';
    /**
     * @var bool 是否是单次运行
     */
    protected $runOnce=false;
    //连续出错,就停止运行
    protected $errorTimes=10;
    //日志保存目录
    protected $logPath=ROOT.'/cache/shell/caiji';
    protected $total=[
        'all'=>0, //本次运行，成功入库的个数
        'down'=>0, //本次运行，需要下载的个数
        'notdown'=>0, //本次运行，成功入库且不用下载的个数
    ];
    protected $stopFile;
    //页数是否递进
    protected $pageAdd=false;
    /**
     * @var array 命令行传过来的参数
     */
    protected $param;
    /**
     * @var array 全局设置
     */
    protected $option_all;
    /**
     * @var object 当前任务配置
     */
    protected $option;
    //当前任务设置，数据库中的设置会被查询放到这里
    protected $setting;
    /**
     * @var \app\admin\model\Caiji|object
     */
    protected $model;
    /**
     * @var int : 1时直接输出信息，2 时重要错误信息保存为日志文件，其他什么都不做
     */
    protected $outType=2;
    /**
     * @var string 信息提示
     */
    protected $errorCode=[];
    protected $error='';
    /**
     * @var string 表前缀
     */
    protected $prefix;
    protected $fileName;
    //缓存当前连续出错的次数
    protected $errorTimesTmp=0;
    /**
     * @var \extend\Curl;
     */
    protected $curl;
    //本次任务最大执行次数,0为不限制
    protected $runMax=0;
    //是否停止
    protected $isStop=false;
    public function __construct($param=[])
    {
        parent::__construct();
        $this->param = $param;
        $this->model=app('\app\admin\model\Caiji');
        $this->prefix=Caiji::$prefix;
    }
    /**
     * 检测参数是否正确
     * @return bool
     */
    abstract protected function checkParam();

    protected function checkParamCommon()
    {
        if(empty($this->param) || !isset($this->param[0]) || !$this->param[0]){
            $this->error='命令缺少必要的参数 id';
            return false;
        }
        $this->setting=$this->model->eq('id',(int)$this->param[0])->find(null,true);
        if(! $this->setting){
            $this->error='不存在id为 "'.$this->param[0].'" 的采集任务';
            return false;
        }
        return true;
    }
    /** ------------------------------------------------------------------
     * 通用初始化
     *---------------------------------------------------------------------*/
    protected function _init(){
        //注册命令可选项
        $this->_setCommandOptions([
            '-e'=>['outType',1],//设置输出种类为直接输出，默认为重要信息保存日志
            '-o'=>['runOnce',true],//设置单次运行，默认是循环运行
            '-d'=>['debug',true] //开启调试模式，默认关闭
        ], $this->param);
        //脚本无限执行
        ignore_user_abort(true);
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        //脚本最大运行内存设置
        if (intval(ini_get("memory_limit")) < 1024) {
            ini_set('memory_limit', '1024M');
        }
    }
    /** ------------------------------------------------------------------
     * 具体任务初始化
     * @param string $type
     * @param bool $isMyTable 是否取自身设置的数据表
     *---------------------------------------------------------------------*/
    protected function taskInit($type,$isMyTable=true){
        //生成不加琐的stop文件
        $this->fileName=str_pad($this->setting['id'],6,'0',STR_PAD_LEFT).'_'.$this->setting['name'].'_'.basename(str_replace('\\','/',get_class($this))).'_'.$this->param[0];
        $this->stopFile=$this->path.'/stop/'.$this->fileName;
        if(is_file($this->stopFile.'.lock')){
            rename($this->stopFile.'.lock',$this->stopFile);
        }else{
            if(!is_file($this->stopFile))
                File::write($this->stopFile,'');
        }
        $this->option=json_decode($this->setting[$type]);
        if($isMyTable)
            $this->option->table=$this->option->table ??'caiji_'.$type.'_'.$this->setting['id'];
        else
            $this->option->table=$this->getContentTable();
        $this->model->table=$this->option->table;
        //设置最大运行数
        if(isset($this->option->run_max) && $this->option->run_max)
            $this->runMax=$this->option->run_max;
        //curl初始化
        $this->curlInit();
    }

    /** ------------------------------------------------------------------
     * cli模式入口函数
     * @return int
     *--------------------------------------------------------------------*/
    public function start(){
        if($this->isStop) return -1;
        $this->run();
        return 0;
    }
    abstract protected function run();
    /** ------------------------------------------------------------------
     * 循环操作
     * @param array $query:绑定查询
     *          string $query['sql']:绑定查询的sql语句
     *          array $query['params']:绑定查询的参数
     * @param \Closure|string|array $doFunc:回调函数（接收两个参数:每条记录的$k和$v）
     * @param string $msg 额外提示信息
     * @param array $endEchoExp：结束时输出用到，包含下面三项
     *          string $endEchoExp['from']:数据库中的一个表名
     *          array $endEchoExp['where']:where条件查询表达式
     *          array|string|\Closure $endEchoExp['do']:回调函数（接收1个参数：本任务有待完成的项目个数$count），本项可以不填或为空，不填或为空时不会进行回调
     * @return int
     *---------------------------------------------------------------------*/
    protected function doLoop($query,$doFunc,$endEchoExp,$msg=''){
        if($this->runOnce)
            $perPage=1;
        else
            $perPage=20;
        $start=0;
        do{
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
                    $this->outPut('*******************************************start:id=' . $v['id'] . PHP_EOL);
                    $ret=$this->callback($doFunc,[$v]);
                    if($ret=== -1){
                        $this->outPut('   致命错误:'.$this->error.PHP_EOL,true);
                        $this->endEcho('   stop -1,'.$msg,$endEchoExp);
                        break 2;
                    }elseif($ret===0){
                        $this->countTimes(false,$this->errorTimesTmp);
                        $this->total['all']++;
                    }elseif ($ret===false) {
                        $this->countTimes(true,$this->errorTimesTmp);
                        $this->endEcho('   callback is wrong,' . $msg, $endEchoExp);
                        break 2;
                    }elseif($ret >= 100){
                        $this->outPut('  error code:'.$ret.',msg:'.$this->errorCode[$ret].PHP_EOL);
                    } else{
                        $this->outPut('  id:'.$v['id'].' ;error code:'.$ret.', msg:'.$this->errorCode[$ret].PHP_EOL,true);
                        $this->countTimes(true,$this->errorTimesTmp);
                    }
                    $this->outPut('*******************************************end:id=' . $v['id'] . PHP_EOL);
                }
            }else{
                $this->endEcho('   complete!',$endEchoExp);
                break;
            }
        }while($this->runOnce == false);
        return $this->isStop ? -1 : 0;
    }


    /**
     * 参数不正确时，输出提示内容
     */
    protected function dieEcho(){
        $this->outType=1;
        $this->outPut('采集系统初始化失败，错误信息：'.$this->error.'当前类名：'.get_class($this).PHP_EOL);
        $msg= '正确命令格式如下:'.PHP_EOL;
        $msg.= '  caiji/page id   运行列表采集模块，id为采集任务id'.PHP_EOL;
        $msg.= '  caiji/content id   运行内容采集模块，id同上'.PHP_EOL;
        $msg.= '  caiji/download id   运行文件/图片下载模块，id同上'.PHP_EOL;
        $msg.= '  caiji/fabu tableName   运行发布模块，tableName是采集时数据保存的数据表名'.PHP_EOL;
        $msg.= '    可选参数：'.PHP_EOL;
        $msg.= '        -e: 设置输出种类为直接输出，默认输出为重要信息保存日志'.PHP_EOL;
        $msg.= '        -o: 设置单次运行，默认是循环运行'.PHP_EOL;
        $msg.= '        -d: 开启调试模式，默认关闭'.PHP_EOL;
        $msg.= '        -n 100: 运行发布模块时独有的参数，设置最大运行次数为-n后面的数字，默认为0（无限）'.PHP_EOL;
        $this->outPut($msg);
    }
    /** ------------------------------------------------------------------
     * 任务结束输出
     * @param string $msg
     * @param array $exp
     *---------------------------------------------------------------------*/
    protected function endEcho($msg,$exp){
        $cout=$this->model->count($exp);
        if(isset($exp['do']) && $exp['do']){
            $this->callback($exp['do'],[$cout]);
        }
        $this->outPut($msg.'This time had handled:'.$this->total['all'].'. Remaining:'.$cout.' ; '.date('Y-m-d H:i:s').PHP_EOL,true);
    }

    /**
     * 检测是否需要停止
     * @return bool
     */
    protected function checkStop(){
        if($this->isStop){
            $this->outPut('   致命错误! msg : '.$this->error.PHP_EOL,true);
            return true;
        }
        //连续错误次数大于设置的错误最大值时就停止
        if ($this->errorTimesTmp > $this->errorTimes){
            $this->outPut('   error times more than Max:'.$this->errorTimes.PHP_EOL,true);
            return true;
        }
        //检测最大执行次数
        if($this->runMax !==0 &&  $this->total['all'] >=$this->runMax ){
            $this->outPut('   Run times more than runMax:'.$this->total['all'].PHP_EOL);
            return true;
        }
        //检测加琐的stop文件是否存在
        if(file_exists($this->stopFile.'.lock')){
            $this->outPut('   lock file exists, end !'.PHP_EOL);
            return true;
        }
        return false;
    }
    //全局配置读取
    protected function globalSetting(){
        //$this->option_all=json_decode($this->setting['options']);
    }
    /** ------------------------------------------------------------------
     * 输出: this->outType==2时会把重要信息写到日志里; this->outType==1时，直接输出
     * @param string $msg 信息
     * @param bool $important 是否是重要信息
     * @return  bool
     *---------------------------------------------------------------------*/
    protected function outPut($msg,$important=false){
        if($this->outType ==2 && $important){
            return File::write($this->logPath.'/'.$this->fileName.'.log',$msg,true);
        }elseif($this->outType ==1){
            echo $msg;
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 连续进行计数器
     * @param bool $isDo
     * @param int $count
     *--------------------------------------------------------------------*/
    protected function  countTimes($isDo,&$count){
        if($isDo){
            $count ++;
        }else{
            if($count > 0)
                $count=0;
        }
    }








    /** ------------------------------------------------------------------
     * curl初始化
     *---------------------------------------------------------------------*/
    protected function curlInit(){
        if(isset($this->option->curl->setting)){
            $this->option->curl=Helper::object2array($this->option->curl);
        }else{
            $this->option->curl=[];
        }
        $this->option->curl['setting']=$this->option->curl['setting'] ?? [];
        /*$this->option->curl['cookieFile']=$this->option->curl['cookieFile'] ?? '';
        $this->option->curl['opt']=$this->option->curl['opt'] ?? [];
        $this->option->curl['proxy']=$this->option->curl['proxy'] ?? [];
        $this->option->curl['method']=$this->option->curl['method'] ?? 'get';
        $this->option->curl['header']=$this->option->curl['header'] ?? [];*/
        $this->curl=New Curl($this->option->curl['setting']);
    }



    /**------------------------------------------------------------------
     * 添加队列
     * @param array $data 数据
     * @param bool $check 是否需要检测是否已经存在相同名字的队列
     * @return int|bool 成功插入返回插入id，否则返回0
     *--------------------------------------------------------------------*/
    protected function addQueue($data,$check=true){
        $table= $this->model->table;
        $this->model->reset()->table='crontab';
        $data['name_md5']=md5($data['callable'].preg_replace('/\s\-[a-z](?:\s\d+)?/','',$data['class_param']).$data['method_param']);
        $res=false;
        if($check){
            $res=$this->model->eq('name_md5',$data['name_md5'])->eq('status',0)->find(null,true);
        }
        if(!$res){
            $ret=$this->model->reset()-> insert($data);
        }
        $this->model->reset()->table=$table;
        return $ret ?? 0;
    }

    /** ------------------------------------------------------------------
     * 获取采集内容时设置的数据表
     * @return string
     *--------------------------------------------------------------------*/
    protected function getContentTable(){
        $contentOption=json_decode($this->setting['content'],true);
        return $contentOption['table'];
    }

}

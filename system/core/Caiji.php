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


namespace core;
use extend\Helper;
use core\lib\cache\File;
use extend\Selector;
abstract  class  Caiji
{
    /**
     * @var bool 是否是调试模式
     */
    public $debug=false;
    public $outType=2;
    public $runOnce=false;
    public $path=ROOT.'/cache/caiji';
    protected $fileName;
    protected $option;
    protected $prefix;
    protected $errorTimesTmp=0;
    public $errorTimes=10;
    public $maxId=0;
    public $startId=0;
    /**
     * @var \core\Model
     */
    protected $model;
    protected $total=[
        'all'=>0, //本次运行，成功入库的个数
        'down'=>0, //本次运行，需要下载的个数
        'notdown'=>0, //本次运行，成功入库且不用下载的个数
        'end'=>0,
    ];
    protected $error='';
    //本次任务最大执行次数,0为不限制
    public $runMax=0;
    //是否停止
    protected $isStop=false;
    protected $stopFile;

    protected function __construct($option){
        $this->option=array_merge($this->option,$option);
        $this->_init();
    }
    protected function _init(){
        if(isset($this->option['outType']) && $this->option['outType'])
            $this->outType=(int)$this->option['outType'];
        if(isset($this->option['debug']) && $this->option['debug'])
            $this->debug=(bool)$this->option['debug'];
        if(isset($this->option['runOnce']) && $this->option['runOnce'])
            $this->runOnce=(bool) $this->option['runOnce'];
        if(isset($this->option['startId']) && $this->option['startId'])
            $this->startId=(int) $this->option['startId'];
        if(isset($this->option['maxId']) && $this->option['maxId'])
            $this->maxId=(int) $this->option['maxId'];
        if(isset($this->option['curlTimeInterval']) && $this->option['curlTimeInterval'])
            $this->curlTimeInterval=(int) $this->option['curlTimeInterval'];
        $this->fileName=$this->option['name'].'_'.basename(str_replace('\\','/',get_class($this)));
        $this->prefix=Conf::get('prefix','database');

        //生成不加琐的stop文件
        $this->stopFile=$this->path.'/stop/'.$this->fileName;
        if(is_file($this->stopFile.'.lock')){
            rename($this->stopFile.'.lock',$this->stopFile);
        }else{
            if(!is_file($this->stopFile))
                File::write($this->stopFile,'');
        }
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
     * 使用回调函数
     * @param string|\Closure|array $func 回调函数
     * @param array $params 回调函数的参数
     * @return mixed
     *---------------------------------------------------------------------*/
    protected function callback($func,$params){
        return Helper::callback($func,$params);
    }
    /** ------------------------------------------------------------------
     * 格式化字符串，用于文件和图片本地化时的路径与文件名的生成
     * @param string $str
     * @param int|string $id
     * @param string $url
     * @param int|string $num
     * @param int $time
     * @return string
     *--------------------------------------------------------------------*/
    static public function format($str,$id=0,$num='',$time=-1){
        if($time<0)
            $time=time();
        return str_replace([
            '{%Y%}',
            '{%m%}',
            '{%d%}',
            '{%H%}',
            '{%i%}',
            '{%s%}',
            '{%r%}',
            '{%u%}',
            '{%id%}',
            '{%num%}',
        ],[
            date('Y',$time),
            date('m',$time),
            date('d',$time),
            date('H',$time),
            date('i',$time),
            date('s',$time),
            self::randomKeys(8),
            Helper::uuid(),
            (string)$id,
            (string)$num,
        ],$str);
    }
    /** ------------------------------------------------------------------
     * 生成随机字符串
     * @param int $length
     * @return string
     *---------------------------------------------------------------------*/
    protected static function randomKeys($length){
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $key='';
        for($i=0;$i<$length;$i++) {
            $key .= $pattern{mt_rand(0,35)};
        }
        return $key;
    }

    /** ------------------------------------------------------------------
     * 找出字符串中的图片网址和链接网址，将其中的相对网址转换为绝对网址
     * @param string $str 原字符串
     * @param string $url 当前页面网址
     * @param bool $mao 是否保留锚点,默认不保留
     * @return string
     *---------------------------------------------------------------------*/
    public function toTrueUrl($str,$url,$mao=false){
        return preg_replace_callback('/<(img|a) ([^>]*?)(src|href)=([\'"]?)([^>\s\'"]*)\4([^>]*)>/i',function($match)use($url,$mao){
            //$match[6]=$match[6] ?? '';
            if($match[1]==='a' or $match[1]==='A'){
                return '<a '.$match[2].'href="'.$this->getTrueUrl($match[5],$url,$mao).'"'.$match[6].'>';
            }else{
                if($match[5])
                    return '<img '.$match[2].'src="'.$this->getTrueUrl($match[5],$url,$mao).'"'.$match[6].'>';
                else
                    return '';
            }
        },$str);
    }

    /** ------------------------------------------------------------------
     * 相对网址转换为绝对网址
     * @param string $srcurl 原网址
     * @param string $baseurl 当前页面网址
     * @param bool $mao:是否保留锚点,,默认不保留
     * @return string 转换为绝对网址后的网址
     *---------------------------------------------------------------------*/
    public function getTrueUrl($srcurl, $baseurl,$mao=false) {
        $srcinfo = parse_url($srcurl);
        if(isset($srcinfo['scheme'])) {
            return $srcurl;
        }
        //$srcinfo['fragment']=$srcinfo['fragment'] ?? '';
        $srcinfo['fragment']=isset($srcinfo['fragment']) ? '#'.$srcinfo['fragment'] : '';
        if(!isset($srcinfo['path'])){
            if($mao)
                return $baseurl .$srcinfo['fragment'] ;
            else
                return $baseurl;
        }
        //$srcinfo['query']=$srcinfo['query'] ?? '';
        $srcinfo['query']=isset($srcinfo['query']) ? '?'.$srcinfo['query'] : '';
        $baseinfo = parse_url($baseurl);
        $baseinfo['user']=isset($baseinfo['user']) ? $baseinfo['user'].':' : '';
        $baseinfo['pass']= isset($baseinfo['pass']) ? $baseinfo['pass'].'@' : '';
        $baseinfo['port']=isset($baseinfo['port']) ? ':'.$baseinfo['port']:'';
        $url = $baseinfo['scheme'].'://'.$baseinfo['user'].$baseinfo['pass'].$baseinfo['host'].$baseinfo['port'];
        if(!isset($baseinfo['path']) or substr($srcinfo['path'], 0, 1) == '/') {
            $path = $srcinfo['path'];
        }else{
            $filename=  basename($baseinfo['path']);
            if(strrpos($filename,'.') >0){
                //文件网址
                $path = dirname($baseinfo['path']).'/'.$srcinfo['path'];
            }else{
                //目录网址
                $path = ltrim($baseinfo['path'],'/').'/'.$srcinfo['path'];
            }
        }
        $rst = [];
        $path_array = explode('/', str_replace('\\', '/', $path));
        foreach ($path_array as $key => $dir) {
            if ($dir == '..') {
                array_pop($rst);
            }elseif($dir && $dir != '.') {
                $rst[] = $dir;
            }
        }
        $url .='/'. implode('/', $rst);
        if(end($path_array)=='')
            $url.='/';
        return $mao ? $url.$srcinfo['query'] . $srcinfo['fragment'] :$url.$srcinfo['query'];
    }

    /** ------------------------------------------------------------------
     * 过滤器
     * @param array|object $option:过滤规则集合
     * @param string $content
     * @param string $baseurl 当前页面url
     * @return string
     *---------------------------------------------------------------------*/
    protected function filter($option,$content,$baseurl){
        if($content=='')
            return $content;
        if(! is_array($option) && !($option instanceof \Traversable))
            return $content;
        foreach ($option as $v){
            $v=explode('{%|||%}',$v);
            if(!isset($v[1]))
                continue;
            switch (strtolower($v[0])){
                case 'replace'://替换
                    if(!isset($v[2]))
                        continue 2;
                    $content=str_replace($v[1],$v[2],$content);
                    break;
                case 'html'://去除html标签
                    $content=strip_tags($content,$v[1]);
                    break;
                case 'reg'://正则
                    if(!isset($v[2]))
                        continue 2;
                    $content=preg_replace($v[1],$v[2],$content);
                    break;
                case 'union'://组合
                    $content=str_replace('{%xxoo%}',$content,$v[1]);
                    break;
                case 'has'://必须包含
                    if(strpos($content,$v[1])===false){
                        $content='';
                    }
                    break;
                case 'nhas'://不能包含
                    if(strpos($content,$v[1])!==false){
                        $content='';
                    }
                    break;
                case 'trueurl'://转换为绝对网址
                    $v[1]=(int)$v[1];
                    $v[2]=(bool)($v[2] ?? false);
                    $v[3]=$v[3]??$baseurl;
                    if($v[1]==0){
                        $content=$this->getTrueUrl($content,$v[3],$v[2]);
                    }else{
                        $content=$this->toTrueUrl($content,$v[3],$v[2]);
                    }
                    break;
                case 'trim':
                    $content=trim($content);
                    break;
                case 'length':
                    $length=mb_strlen(strip_tags($content));
                    if($length<(int)$v[1])
                        $content='';
                    break;
            }
        }
        return $content;
    }

    /** ------------------------------------------------------------------
     * 调试模式的时候 打印变量
     * @param array $vars
     * @param string $msg
     * @param bool $exit 是否退出
     *--------------------------------------------------------------------*/
    protected function debug($vars,$msg='',$exit=true){
        if($this->debug){
            foreach ($vars as $var){
                //echo PHP_EOL;
                dump($var);
                //echo PHP_EOL;
            }
            if($exit){
                exit($msg);
            }else{
                echo $msg;
            }
        }
    }

    /** ------------------------------------------------------------------
     * 输出: this->outType==2时会把重要信息写到日志里; this->outType==1时，直接输出
     * @param string $msg 信息
     * @param bool $important 是否是重要信息
     *---------------------------------------------------------------------*/
    protected function outPut($msg,$important=false){
        if($this->outType ==2 && $important){
            File::write($this->path.'/'.$this->fileName.'.log',$msg,true);
        }elseif($this->outType ==1){
            echo $msg;
        }
    }

    protected function checkTimes($var1,$var2){
        if($var2 !=0 && $var1 >= $var2){
            return true;
        }
        return false;
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
    protected function startEcho($msg){
        $this->outPut('Date：'.date('Y-m-d H:i:s').','.$msg.PHP_EOL,true);
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

    /**------------------------------------------------------------------
     * 添加队列
     * @param array $data 数据
     * @param bool $check 是否需要检测是否已经存在相同名字的队列
     * @return int|bool 成功插入返回插入id，否则返回0
     *--------------------------------------------------------------------*/
    protected function addQueue($data,$check=true){
        $table= $this->model->table;
        $this->model->reset()->table='caiji_queue';
        $data['name_md5']=md5($data['callback'].preg_replace('/\s\-[a-z](?:\s\d+)?/','',$data['class_param']).$data['method_param']);
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
     * thowError
     * @param \Throwable $e
     *--------------------------------------------------------------------*/
    protected function thowError($e){
        $str='      Message:'.$e->getMessage().PHP_EOL;
        $str.='     File:'.$e->getFile().PHP_EOL;
        $str.='     Code:'.$e->getCode().PHP_EOL;
        //$this->outPut($str,true);
        $this->endEcho($str,[]);
    }

    /** ------------------------------------------------------------------
     * 建立数据表
     * @param  string $type 表名种类
     *--------------------------------------------------------------------*/
    protected function createTable($type){
        $type=ucfirst($type);
        if(isset($this->option['create'.$type.'Table']) && $this->option['create'.$type.'Table']){
            $sql='';
            switch ($type){
                case 'Content':
                    $sql.='CREATE TABLE IF NOT EXISTS `'.$this->prefix.$this->option['table'].'` (
                        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `caiji_name` VARCHAR(50) NOT NULL,
                        `iscaiji` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `isfabu` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `isdownload` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `isshenhe` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `islaji` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `isend` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `isdone` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',
                        `update_time` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
                        `times` SMALLINT(5) UNSIGNED NOT NULL DEFAULT \'0\',
                        `create_time` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
                        `url` VARCHAR(500) NOT NULL DEFAULT \'\',
                        `from_id` VARCHAR(50) NOT NULL DEFAULT \'\',
                        PRIMARY KEY (`id`),
                        INDEX `from_id` (`from_id`),
                        INDEX `caiji_name` (`caiji_name`),
                        INDEX `iscaiji` (`iscaiji`,`isfabu`,`isdownload`),
	                    INDEX `isend` (`isend`, `islaji`, `isshenhe`)
                    )COLLATE=\'utf8mb4_general_ci\' ENGINE=InnoDB;';
                    break;
                case 'Download':
                    $sql.='CREATE TABLE IF NOT EXISTS `'.$this->prefix.$this->option['downloadTable'].'` (
                        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `source_url` TEXT NULL DEFAULT NULL COMMENT \'原始文件地址\',
                        `true_url` TEXT NULL DEFAULT NULL COMMENT \'真实文件地址\',
                        `save_path` TEXT NULL DEFAULT NULL COMMENT \'完整保存路径\',
                        `replace_path` TEXT NULL DEFAULT NULL COMMENT \'写入内容表的路径\',
                        `status` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `upload` TINYINT(1) NOT NULL DEFAULT \'0\',
                        `type` VARCHAR(50) NOT NULL DEFAULT \'\',
                        `cid` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
                        `times` INT(10) UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'下载次数\',
                        PRIMARY KEY (`id`),
                        INDEX `cid` (`cid`)
                    )COLLATE=\'utf8mb4_general_ci\' ENGINE=InnoDB;';
                    break;
            }
            $this->model->_exec($sql,[],false);
            //$option=Conf::all($this->option['name'],null,'config/caiji/');
            $file=ROOT.'/config/caiji/'.$this->option['name'].'.php';
            $option=@file_get_contents($file);
            if($option===false)
                return;
            $option=preg_replace('% *([\'"])create'.$type.'Table\1 *=> *true *,?.*\r?\n%','',$option,1);
            File::write($file,$option);
        }
    }

    /** ------------------------------------------------------------------
     * 选择器
     * @param string $html
     * @param array $rule
     * @return bool|string|array
     *--------------------------------------------------------------------*/
    protected function selector(&$html,$rule){
        $rule['cut']=$rule['cut'] ?? '';
        return Selector::find($html,$rule['type'],$rule['selector'],$rule['tags'],$rule['cut']);
    }

}
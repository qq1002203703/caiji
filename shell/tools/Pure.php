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


namespace shell\tools;

use shell\BaseShell;

class Pure extends BaseShell
{
	protected $param=[];
	protected $debug=false;
    protected $runOnce=false;
    protected $startId=0;
    protected $maxId=0;

	public function __construct($param=[]){
	 if(!$param ){
            $this->param=['help'];
            return;
        }
        $this->param = $param;
        $this->_setCommandOptions(['-e'=>['outType',1],'-d'=>['debug',true],'-o'=>['runOnce',true],'-s'=>['startId'],'-m'=>['maxId']],$this->param);
        parent::__construct();
	}

    public function start(){
		if(!$this->param || !isset($this->param[0])){
            $this->tipEcho();
            $this->goodbye();
            return;
        }
		$method=$this->param[0];
        if(is_callable([$this,$method])){
            call_user_func([$this,$method]);
        }else{
            $this->tipEcho();
        }
        $this->goodbye();
    }
    protected function outPut($msg, $important=false)
    {
       echo $msg;
    }

	protected function tipEcho()
    {
       echo '出错了'.PHP_EOL;
    }

    public function add_stop_file(){
        $time=strtotime('2019-06-23 20:03:00')-30;
        $time=$time-time();
        echo '等待 '.$time.' 秒-----'.PHP_EOL;
        sleep($time);
        $file=ROOT.'/cache/caiji/stop/douban_Content';
        rename($file,$file.'.lock');
    }

	public function change_file(){
		$name='/15';
       $path='F:/caiji/av'.$name;
       if(!is_dir($path.$name)){
           mkdir($path.$name,0755,true);
       }
	   $res=scandir($path.'/Processed');
       if(!$res)
           return;
       foreach ($res as $key =>$item){
           if($item=='.'||$item=='..')
               continue;
           echo '处理：'.$item.PHP_EOL;
           if(is_file($path.'/'.$item)){
               if(rename($path.'/'.$item,$path.$name.'/'.$item)){
                   echo '   成功：转移'.PHP_EOL;
               }else
                   echo '   失败：转移'.PHP_EOL;
           }else
               echo '   不存在文件'.PHP_EOL;
           //exit();
       }
	}

}
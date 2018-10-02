<?php
namespace shell;

abstract class BaseShell
{
    private $startTime;
    private $endTime;

    public function __construct()
    {
        $this->setStartTime();
        $this->welcome();
    }

    public function setStartTime()
    {
        $this->startTime = microtime(true);
    }

    public function setEndTime()
    {
        $this->endTime = microtime(true);
    }

    //获取脚本运行时间
    public function getUseTime()
    {
        return round($this->endTime - $this->startTime, 4);
    }

    /**
     * 输出欢迎logo
     */
    public function welcome()
    {
        $str = '*====================================================*'  . PHP_EOL
            . '*       PHP版本:' . PHP_VERSION . '   框架版本:' . SYSTEM_VERSION  . '     *'.PHP_EOL
            . '*====================================================*' . PHP_EOL;
        $this->outPut($str,false);
    }

    /**
     * 友好的结束语句
     */
    public function goodbye()
    {
        $this->setEndTime();
        $this->outPut('--------------------总用时 ' . $this->getUseTime() . '----------------------'.PHP_EOL,false);
    }

	public function showCommand()
	{
	    $this->outPut('Usage : '.PHP_EOL.'  folderName/className [arguments] [options]'.PHP_EOL.PHP_EOL,false);
	}

    /** ------------------------------------------------------------------
     * 输出控制
     * @param string $msg 信息
     * @param bool $important 是否是重要信息
     *--------------------------------------------------------------------*/
	abstract protected function outPut($msg,$important);

    /** ------------------------------------------------------------------
     * 设置命令行可选参数
     * @param array $options ['-e'=>['runOnce',true],'-d'=>['debug',true]]
     * @param array $argv
     *--------------------------------------------------------------------*/
    protected function _setCommandOptions($options,&$argv){
        foreach ($options as $key =>$option){
            if(isset( $this->{$option[0]})){
                if(($i=array_search($key,$argv,true)) !==false){
                    $this->{$option[0]}=$option[1];
                    unset($argv[$i]);
                }
            }
        }
        $argv=array_values($argv);
    }
}
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

namespace shell\caiji;
use shell\BaseShell;
use \core\Conf;
use \core\lib\cache\File;
class Run extends BaseShell
{
    protected $param;
    protected $model;
    protected $prefix;
    protected $path=ROOT.'/shell/tools/queque';
    protected $outType=2;//1为直接输出，2为重要信息保存为日志，可以传第二个参数来改变此值
    protected $total=0;
    public function __construct($param=[])
    {
        parent::__construct();
        if(! $param ){
            $this->param=['help'];
            return;
        }
        $this->param = $param;
        $this->model=app('\core\Model');
        $this->model->table='caiji_queue';
        $this->prefix=Conf::get('prefix','database');
        $this->_setCommandOptions(['-e'=>['outType',1]],$this->param);
    }

    public function page(){

    }

    /** ------------------------------------------------------------------
     * 输出: this->outType==2时会把重要信息写到日志里; this->outType==1时，直接输出
     * @param string $msg 信息
     * @param bool $important 是否是重要信息
     * @return  bool
     *--------------------------------------------------------------------*/
    protected function outPut($msg,$important=false)
    {
        if($this->outType ==2 && $important){
            return File::write($this->path.'/queue.log',$msg,true);
        }elseif($this->outType ==1){
            echo $msg;return true;
        }
        return false;
    }
}
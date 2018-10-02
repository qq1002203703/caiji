<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 常规采集基类
 * ======================================*/

namespace core\caiji\normal;
use core\Caiji;
use extend\Curl;
abstract class Base extends Caiji
{
    /**
     * @var \extend\Curl
     */
    protected $curl;
    protected  $repeatTimeTmp;
    //页数是否递进
    protected $pageAdd=false;
    protected $errorCode=[];

    //入口
    public function start()
    {
        $this->startEcho('Rule name:' . $this->option['name'] . ',callback:' . $this->option['callback']);
        try {
            $this->run();
        }catch(\ErrorException $e){
            $this->thowError($e);
        }catch (\Exception $e){
            $this->thowError($e);
        }catch (\Error $e){
            $this->thowError($e);
        }
    }
    abstract protected function run();
    /** ------------------------------------------------------------------
     * curl初始化
     *---------------------------------------------------------------------*/
    protected function curlInit(){
        $this->option['curl']= $this->option['curl'] ?? [];
        $this->option['curl']['setting']= $this->option['curl']['setting'] ?? [];
        $this->option['curl']['options']= $this->option['curl']['options'] ?? [];
        $this->curl=New Curl($this->option['curl']['setting']);
    }

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
}
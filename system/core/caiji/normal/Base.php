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
use extend\HttpClient;

abstract class Base extends Caiji
{
    /**
     * @var \extend\Curl
     */
    protected $curl;
    /**
     * @var \extend\HttpClient
     */
    protected $httpClient;
    protected  $repeatTimeTmp;
    //页数是否递进
    protected $pageAdd=true;
    protected $errorCode=[];
    /**
     * @var array 其他需要保存到数据库的健值对：必须是关联数组，而且是数据库存在的字段作健名，否则保存时会出错
     */
    public $saveValue=[];
    //入口
    public function start($saveValue=[])
    {
        if($saveValue){
            $this->saveValue=$saveValue;
        }
        $this->startEcho('Rule name:' . $this->option['name'] . ',callback:' . $this->option['callback']);
        try {
            $this->run();
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

    /** ------------------------------------------------------------------
     * httpClient初始化
     *---------------------------------------------------------------------*/
    protected function httpClientInit(){
        $this->option['http']= $this->option['http'] ?? [];
        $this->option['http']['setting']= $this->option['http']['setting'] ?? [];
        $this->option['http']['options']= $this->option['http']['options'] ?? [];
        $this->httpClient=New HttpClient($this->option['http']['setting']);
        $this->httpClient->httpSetting($this->option['http']['options']);
        $this->httpClient->stopFile=$this->stopFile;
    }

    protected function doLoop($query,$doFunc,$endEchoExp,$msg=''){
        if($this->runOnce)
            $perPage=1;
        else
            $perPage=20;
        do{
            $sql=$query['sql'];
            if(isset($this->startId) && $this->startId>0)
                $sql.=' and id >'.$this->startId;
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $sql.=' and id <= '.$this->maxId;
            $data=$this->model->_sql($sql." order by id limit {$perPage}",$query['params'],false);
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

    /** ------------------------------------------------------------------
     * 添加额外的健值对到数据中
     * @param mixed $oldData 原来的数据
     * @param string $keyName 当$oldData不是数组时的健名
     *---------------------------------------------------------------------*/
    protected function addSaveValue(&$oldData,$keyName='url'){
        if(!$this->saveValue)
            return;
        if(!is_array($oldData))
            $oldData[$keyName]=$oldData;
        foreach ($this->saveValue as $k => $item){
            $oldData[$k]=$item;
        }
    }

}
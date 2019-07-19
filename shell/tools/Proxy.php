<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *  代理ip池自动维护脚本
 * ======================================*/

namespace shell\tools;

use extend\Helper;
use shell\BaseCommon;
use MultiHttp\MultiRequest;
use MultiHttp\Response;
class Proxy extends BaseCommon
{
    //每次发起从网站读取代理ip的最小时间间隔(单位：秒)
    public $timeSpan=15;
    //最少可用的代理ip数
    public $ipActiveMin=8;
    //最后一次发起从网站读取代理ip的时间戳
    protected $utime=0;
    //脚本是否停止
    protected $isStop=false;

    //初始化
    protected function _init(){
        //脚本停止文件
        $this->path.='/shell/tools/proxy';
        if(is_file($this->path.'.lock')){
            rename($this->path.'.lock',$this->path);
        }
        //默认数据库表名
        $this->model->table='proxy';
    }

    //代理入库
    public function run(){
        do{
            $countActiveIp=$this->countActiveIp();
            $timeSpan=time()-$this->utime;
            if($countActiveIp<$this->ipActiveMin && $timeSpan >=$this->timeSpan){
                $res=$this->getIpFromApi();
                if($res)
                    $this->checkIp($res);
            }elseif ($countActiveIp<$this->ipActiveMin && $timeSpan <$this->timeSpan){
                msleep(($this->timeSpan-$timeSpan)*1000);
            }else{
                msleep($this->timeSpan/3*1000);
            }
            $this->checkStop();
        }while(!$this->isStop);
    }

    //从代理网站读取ip
    protected function getIpFromApi(){
        echo '  从网站获取代理ip:';
        return $this->getFromAxun();
    }
    //阿布云经典模式自动更换ip
    public function getFromAbuyun(){
        $url='http://proxy.abuyun.com/switch-ip';
        $option=[
            CURLOPT_HTTPPROXYTUNNEL=>false,
            CURLOPT_PROXYTYPE=>CURLPROXY_HTTP,
            //CURLOPT_PROXY=>'http://http-dyn.abuyun.com:9020',
            CURLOPT_PROXY=>'http://http-cla.abuyun.com:9030', //经典
            CURLOPT_PROXYAUTH=>CURLAUTH_BASIC,
            CURLOPT_PROXYUSERPWD=>'H52E23WF8U60619C:B7AD4EEC38365C24',
        ];
        do{
            for ($i=1 ;$i<100;$i++){
                $res=trim(Helper::curl_request($url,$status,$option));
                echo '第'.$i.'次取得ip:'.$res.PHP_EOL;
                $html=Helper::curl_request('https://movie.douban.com/subject/1304560/',$status,$option);
                if($status===false)
                    continue;
                $pos=strpos($html,'检测到有异常请求从你的 IP 发出');
                 if($pos===false)
                     break;
            }
            echo '休眠120秒'.PHP_EOL;
            sleep(120);
        }while(true);
    }

    protected function getFromAxun(){
        $url='https://axun.honghekeji.cn/api/link/5d0d9672e132d1480586af90';
        $res=Helper::curl_request($url,$status,[
            CURLOPT_TIMEOUT=>10,
            CURLOPT_CONNECTTIMEOUT=>3
        ]);
        $this->utime=time();
        if($status && $res){
            $arr=json_decode($res,true);
            if($arr && isset($arr['data']) && $arr['data']){
                foreach ($arr['data'] as $key => $item){
                    $arr['data'][$key]=[
                        'ip'=>$item['ip'],
                        'port'=>$item['port'],
                        'type'=>'socks5',
                    ];
                }
                echo '获取成功'.PHP_EOL;
                return $arr['data'];
                //return ['ip'=>$arr[0]['domain'],'port'=>(int)$arr[0]['ip_port'],'type'=>'socks5','expire'=>strtotime($arr[0]['expire_time'])];
            }
        }
        dump($res);
        echo '获取失败'.PHP_EOL;
        return false;
    }

    //检测代理ip是否有效
    protected function checkIp($data=[]){
        if($data){
            $this->checkProxy($data);
        } else{
            $where=[['status','eq',0]];
            $total=$this->model->count(['where'=>$where]);
            $perPage=30;
            $page=(int)ceil($total/$perPage);
            for ($i=0;$i<$page;$i++){
                $data=$this->model->_where($where)->limit($perPage)->findAll(true);
                if($data){
                    $this->checkProxy($data);
                }else{
                    break;
                }
            }
        }
    }

    //计算可用代理ip的数量
    protected function countActiveIp(){
        return $this->model->count([
            'where'=>[['status','gt',0]]
        ]);
    }

    //检测脚本停止文件是否存在
    protected function checkStop(){
        if(is_file($this->path.'.lock'))
            $this->isStop=true;
    }

    protected  function checkProxy($data,$do='insert'){
        $mr  = MultiRequest::create();
        $type=['http'=>CURLPROXY_HTTP,'socks5'=>CURLPROXY_SOCKS5];
        foreach ($data as $proxy){
            $mr->add('GET', 'http://www.uuhuihui.com/uploads/index.html',array(), array(
                'timeout' => 6,
                'connect_timeout'=>3,
                'retry_times' => 3,
                'CURLOPT_HEADER'=>true,
                'CURLOPT_NOBODY'=>true,
                'CURLOPT_PROXY'=>$proxy['ip'],
                'CURLOPT_PROXYPORT'=>$proxy['port'],
                'CURLOPT_PROXYTYPE'=>$type[$proxy['type']],
                //'ip' => $proxy['ip'].':'.$proxy['port'],
                'callback' => function (Response $response)use($proxy,$do) {
                    echo '  正在检测 --> ';
                    if($response->code == 200){
                        echo $proxy['ip']. ':'.$proxy['port'].' --> 有效 -------    '.PHP_EOL;
                        if($do==='insert'){
                            $proxy['status']=1;
                            $this->model->insert($proxy);
                        } else
                            $this->model->eq('id',$proxy['id'])->update(['status'=>1]);
                    }else{
                        echo $proxy['ip']. ':'.$proxy['port'].'-'.$proxy['type'].' --> 无效 --------    '.PHP_EOL;
                        if($do!=='insert')
                            $this->model->eq('id',$proxy['id'])->delete();
                    }
                }
            ));
        }
        $mr->sendAll();
        return 0;
    }
}
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


namespace shell\caiji\plugin;

use MultiHttp\MultiRequest;
use MultiHttp\Response;
use extend\Helper;

class Proxy
{
    static public function douban_get_proxy(){
        $model=app('\core\Model');
        $data=false;
        while (!$data){
            do{
                sleep(2);
                $data=$model->from('proxy')->select('id,ip,port,type') -> eq('status',1)->order('id')->find(null,true);
                $ret=false;
                if($data)
                    $ret=$model->from('proxy')->eq('id',$data['id'])->update(['status'=>2]);
            }while($data && $ret==false);
            if($data && $ret){
                return $data;
            }
            //die('数据库里没有了');
            $data=self::getProxyFrom2808proxy();
            if($data){
                if($model->_exec('replace into '.($model::$prefix).'proxy(ip, port,type, expire,status) value (? , ? , ? , ?, 2)',[$data['ip'],$data['port'],$data['type'],$data['expire']],false)){
                    $data['old']=0;
                    $data['id']=$model::$db->lastInsertId();
                }

            }else{
                $model->from('proxy')->eq('is_lock',0)->update(['is_lock'=>0]);
            }
        }
        return $data;
    }



    static  public function douban_check_result_page($html){
        $pos=strpos($html,'检测到有异常请求从你的 IP 发出');
        return ($pos===false);

    }

    static public function douban_check_result($html){
        $pos=strpos($html,'{"rating": {"max":');
        //1、没有数据 2、检测msg消息
        if($pos===false){
            $json=json_decode($html,true);
            if($json===false)
                return false;
            if(isset($json['msg'])){
                $json['msg']=(string) $json['msg'];
                switch ($json['msg']){
                    case 'invalid_apikey':
                    case 'movie_not_found':
                        return true;
                }
            }
            return false;
        } else
            return true;
    }

    static public function douban_check_proxy($proxy){
        if(!isset($proxy['ip']) || !isset($proxy['port']) || !isset($proxy['type']))
            return false;
        $type=['http'=>CURLPROXY_HTTP,'socks5'=>CURLPROXY_SOCKS5];
        Helper::curl_request('http://www.uuhuihui.com/uploads/index.html',$status,[
            CURLOPT_TIMEOUT=>7,
            CURLOPT_CONNECTTIMEOUT=>3,
            CURLOPT_HEADER=>true,
            CURLOPT_NOBODY=>true,
            CURLOPT_PROXY=>$proxy['ip'],
            CURLOPT_PROXYPORT=>$proxy['port'],
            CURLOPT_PROXYTYPE=>$type[$proxy['type']],
        ]);
        return $status;
    }

    static public  function douban_check_proxy2($data){
        $mr  = MultiRequest::create();
        $model=app('\core\Model');
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
                'callback' => function (Response $response)use($model,$proxy) {
                    echo '  正在检测 --> ';
                    $res = ($response->code == 200);
                    $update=[];
                    $update['isdo']=1;
                    if($res){
                        $update['status']=1;
                        echo $proxy['ip']. ':'.$proxy['port'].' --> 有效     '.PHP_EOL;
                    }else
                        echo $proxy['ip'].':'. $proxy['port'].' --> 无效     '.PHP_EOL;
                    $model->from('proxy')->eq('id',$proxy['id'])->update($update);
                }
            ));
        }
        $mr->sendAll();
        return 0;
    }

    static protected function getProxyFromUeuz(){
        //https://api.2808proxy.com/proxy/unify/get?token=R0XTC739C7JPM1TQNRXCG535T8LVLDWL&amount=1&proxy_type=socks5&format=json&splitter=n&expire=200
        $url='https://too.ueuz.com/frontapi/public/http/get_ip/index?type=2155&iptimelong=1&ipcount=1&protocol=1&areatype=1&area=&resulttype=json&duplicate=1&separator=4&other=&show_city=0&show_carrier=0&show_expire=true&isp=3&auth_key=553959b4aa59da391a7a29325b445392&timestamp=1554122731&sign=16F58CEB40BB3B2805D232D9C8232A35';
        $res=Helper::curl_request($url,$status,[
            CURLOPT_TIMEOUT=>10,
            CURLOPT_CONNECTTIMEOUT=>3
        ]);
        if($status && $res){
            $arr=json_decode($res,true);
            if($arr && isset($arr[0])){
                return ['ip'=>$arr[0]['domain'],'port'=>(int)$arr[0]['ip_port'],'type'=>'socks5','expire'=>strtotime($arr[0]['expire_time'])];
            }
        }
        return false;
    }
    static protected function getProxyFrom2808proxy(){
        $url='https://api.2808proxy.com/proxy/unify/get?token=R0XTC739C7JPM1TQNRXCG535T8LVLDWL&amount=1&proxy_type=socks5&format=json&splitter=n&expire=200';
        $res=Helper::curl_request($url,$status,[
            CURLOPT_TIMEOUT=>10,
            CURLOPT_CONNECTTIMEOUT=>3
        ]);
        if($status && $res){
            $arr=json_decode($res,true);
            if($arr['status'] ===0 &&$arr['data']){
                return ['ip'=>$arr['data'][0]['ip'],'port'=>(int)$arr['data'][0]['s5_port'],'type'=>'socks5','expire'=>(time()+190)];
            }
        }
        return false;
    }

}
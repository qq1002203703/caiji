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

use shell\Spider;

class Weibo extends Spider
{
    protected $fileBodyName='weibo';
    protected $error=[
        1=>'本条搜索已采完',
        -1=>'采集结果false',
        -2=>'采集结果http_code不是200',
        -3=>'采集结果json转数组出错',
        -4=>'采集结果格式不正确',
        -5=>'文本过长超过了字段最大值',
        -6=>'保存到数据库失败'
    ];
    public function search(){
        $keywords=['我是一个','我这个人','我这样的人','自己是一个','自我介绍','介绍一下自己','关于我'];
        foreach ($keywords as $keyword){
            echo '开始采集关键词：'.$keyword.PHP_EOL;
            $code=$this->searchOne($keyword);
            if($code<0){
                exit($this->error[$code].PHP_EOL);
            }elseif($code>0){
                echo $this->error[$code].PHP_EOL;
            }
        }
    }

    protected function searchOne($keyword){
        //$url='https://s.weibo.com/weibo?q=%22%E6%88%91%E6%98%AF%E4%B8%80%E4%B8%AA%22&wvr=6&b=1&Refer=SWeibo_box';
        //echo urldecode('q%3D%22%E6%88%91%E6%98%AF%E4%B8%80%E4%B8%AA%22').PHP_EOL;return;
        //我是一个,我这个人,我这样的人,自己是一个,自我介绍,介绍一下自己,关于我自己，'我很想'
        //$keywods=['我是一个','我这个人','我这样的人','自己是一个','自我介绍','介绍一下自己','关于我','我想哭'];
        //$keyword='我想哭';
        $url='https://m.weibo.cn/api/container/getIndex?containerid=100103type%3D1%26'.urlencode('q="'.$keyword.'"').'&sudaref=login.sina.com.cn&display=0&retcode=6102&page_type=searchall&page=';
        for ($i=2;$i<150;$i++){
            echo '开始采集第'.$i.'页-------------'.PHP_EOL;
            //'%25E8%2587%25AA%25E6%2588%2591%25E4%25BB%258B%25E7%25BB%258D'
            $this->http_init();
            $res=$this->client->http($url.$i);
            echo $url.$i.PHP_EOL;
            if($res===false){
                //exit($this->error[-1].PHP_EOL);
                return -1;
            }
            if($this->client->getHttpCode()!==200){
                echo 'http_code:'.$this->client->getHttpCode().PHP_EOL;
                return -2;
                //exit($this->error[-2].PHP_EOL);
            }
            $res=json_decode($res,true);
            if(!$res){
                return -3;
                //exit($this->error[-3].PHP_EOL);
            }

            if(!isset($res['data']['cards'][0]['card_group'])|| !$res['data']['cards'][0]['card_group']){
                if(isset($res['msg']) && $res['msg']=='这里还没有内容'){
                    return 1;
                }
                dump($res);
                return -4;
                //exit($this->error[-4].PHP_EOL);
            }
            $res=$res['data']['cards'][0]['card_group'];
//        dump($res);
//        $this->dodo($res);
//        exit();
            $isFalse=false;
            foreach ($res as $item){
                if($item['mblog']['isLongText']){
                    $item['mblog']['text']=$this->filterLongText($item['mblog']['longText']['longTextContent']);
                    echo '      长微博'.PHP_EOL;
                }else
                    $item['mblog']['text']=$this->filter($item['mblog']['text']);
                if($this->checkIsRubbish($item['mblog']['text'])){
                    echo '  垃圾内容'.PHP_EOL;
                    continue;
                }
                $checkResult=$this->check($item['mblog']['text']);
                switch ($checkResult){
                    case 1:
                        if(!$this->saveText($item['mblog']['text']))
                            $isFalse=true;
                        break;
                    case 2:
                        if(!$this->saveSignature($item['mblog']['text']))
                            $isFalse=true;
                        break;
                    default:
                        echo '  字数不符合要求'.PHP_EOL;
                }
            }
            if($isFalse)
                return -6;
                //exit('  更新出错'.PHP_EOL);
            msleep(2800,600);
        }
        return 0;
    }

    protected function filter($text){
        if(!$text)
            return $text;
        return trim(strip_tags(preg_replace([
            '%<a [^>]+>[\s\S]*?</a>%i',
            '%http://t\.cn/[a-z0-9]+%i',
            '%\[.*?\]%',
            '%微博%',
            '%[\r\n\t]+%',
            '% {2,}%',
        ],[
            '',
            '',
            '',
            '社区',
            ' ',
            ' ',
        ],$text)));
    }

    protected function filterLongText($text){
        if(!$text)
            return $text;
        return trim(strip_tags(preg_replace([
            '%#.*?#%i',
            '%\[.*?\]%',
            '%[\t\r]+%',
            '%(\n *){2,}%',
            '%\n%',
            '%http://t\.cn/[a-z0-9]+%i',
            '%@.+? +%',
            '%微博%',
            '%(<br>\s*){2,}%',
            '%[\r\n\t]%'
        ],[
            '',
            '',
            '',
            "\n",
            '<br>',
            '',
            '',
            '社区',
            '',
            ''
        ],$text),'<br>'));
    }
    protected function checkIsRubbish($str){
        return strpos($str,'嘉宾')!==false;
    }
    protected function check($text){
        $length=mb_strlen(strip_tags($text));
        if($length>=54 && $length<=500)
            return 1;
        elseif ($length>24 && $length<=300)
            return 2;
        else
            return 0;
    }
    protected function saveText($text){
        $md5=md5($text);
        $table='caiji_renren_name';
        if($this->model->from($table)->eq('md5',$md5)->find(null,true)){
            echo '  重复Text：----------- '.PHP_EOL;
            return true;
        }else{
            $tmp=$this->model->select('id')->from($table)->eq('md5','')->order('id')->find(null,true);
            if(!$tmp)
                exit('  Text没有需要更新的了'.PHP_EOL);
            if($this->model->from($table)->eq('id',$tmp['id'])->update(['md5'=>$md5,'text'=>$text])){
                echo '  更新Text：'.$tmp['id'].'---'.$text.'----------- '.PHP_EOL;
                return true;
            }
            echo '  Text更新出错';
            dump($this->model->getSql());
            dump(['md5'=>$md5,'text'=>$text]);
            return false;
        }
    }

    protected function saveSignature($text){
        $data['sign_md5']=md5($text);
        $data['signature']=$text;
        $table='caiji_renren_name';
        //echo 'sign_md5=>'.$data['sign_md5'];
        if($this->model->from($table)->eq('sign_md5',$data['sign_md5'])->find(null,true)){
            echo '  重复Signature：----------- '.PHP_EOL;
            return true;
        }else{
            $tmp=$this->model->select('id')->from($table)->eq('sign_md5','')->find(null,true);
            if(!$tmp)
                exit('  Signature没有需要更新的了'.PHP_EOL);
            if($this->model->from($table)->eq('id',$tmp['id'])->update($data)){
                echo '  更新Signature：'.$tmp['id'].'---'.$data['signature'].'----------- '.PHP_EOL;
                return true;
            }
            echo '  Signature更新出错';
            dump($this->model->getSql());
            dump($data);
            return false;
        }
    }

    public function dodo2(){
        $str='';
        $str=$this->filter($str);
        dump($str);
    }
    public function dodo($var){
        ob_start();
        var_dump($var);
        file_put_contents(ROOT.'/cache/1.php',ob_get_clean());
    }
    protected function http_init(){
        $this->newClient(['opt'=>[
            CURLOPT_TIMEOUT=>8,//下载时应该按目标文件大小设置大一点
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
            CURLOPT_REFERER=>'http://weibo.com/',
            //CURLOPT_HTTPPROXYTUNNEL=>false,
            //CURLOPT_PROXYTYPE=>CURLPROXY_HTTP,
            //CURLOPT_PROXY=>'http://http-dyn.abuyun.com:9020', //动态
            //CURLOPT_PROXY=>'http://http-cla.abuyun.com:9030', //经典
            //CURLOPT_PROXY=>'http://http-pro.abuyun.com:9010', //专业
            //CURLOPT_PROXYAUTH=>CURLAUTH_BASIC,
            //CURLOPT_PROXYUSERPWD=>'H05K700VIP07918D:1AFA971C73727EFF',
        ]]);
        $this->client->httpSetting([
            'proxy'=>[], //代理ip 端口 种类 格式 ['ip'=>'8.8.8.5','port'=>80,'type'=>'http']
            //'checkProxyPlugin'=>'\shell\caiji\Douban::check_proxy',//检测代理ip的插件
            //'getProxyPlugin'=>'\shell\caiji\Douban::get_proxy', //获取代理ip的插件
            //'checkResultPlugin'=>'\shell\caiji\Zhihu::check_result',//检测结果是否正常
            //'isProxy'=>false, //是否使用代理ip访问
            //'ipExpirationTime'=>280, //ip过期时间 单位秒
            'isOpenCurlTimeInterval'=>true,//是否开启curl访问时间间隔控制
            'curlTimeInterval'=>1200, //curl每次访问的最小时间间隔 单位毫秒
            //'isRandomUserAgent'=>true, //是否使用随机ua
            //'isAutoReferer'=>true, //是否需要自动获取来路
            //'waitNoProxy'=>20, //当无法获得有效的代理ip时，程序进行休眠的时间(单位秒)
            //'waitIpLock'=>10000, //当所有ip被封琐时，程序进行休眠的时间(单位毫秒)
            //'waitCurlFalse'=>4000,//当curl获取结果为false时 等待多少时间才重新发起下次请求(单位毫秒)
            'tryTimes'=>6,
            'encoding'=>'',
            'stopFile'=>$this->stopFile,
        ]);
    }
}
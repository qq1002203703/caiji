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

use extend\Helper;
use extend\Selector;
use shell\Spider;

class Renren extends Spider
{
    protected $fileBodyName='renren';
    protected $error=[
        1=>'视频已失效',
        -1=>'采集结果false',
        -2=>'采集结果http_code不是200',
        -3=>'采集结果json转数组出错',
        -4=>'采集结果格式不正确',
        -5=>'文本过长超过了字段最大值',
        -6=>'保存到数据库失败'
    ];

    /** ------------------------------------------------------------------
     * 真实人名采集
     *---------------------------------------------------------------------*/
    public function spider_name(){
        $url='http://name.renren.com/getSurnameList?t='.time();
        $this->http_init();
        while (true){
            $res=$this->client->http($url);
            if($res===false){
                exit($this->error[-1].PHP_EOL);
            }
            if($this->client->getHttpCode()!==200){
                echo 'http_code:'.$this->client->getHttpCode().PHP_EOL;
                exit($this->error[-2].PHP_EOL);
            }
            $res=json_decode($res,true);
            if(!$res){
                exit($this->error[-3].PHP_EOL);
            }
            if(!isset($res['data'])|| !$res['data']){
                dump($res);
                exit($this->error[-4].PHP_EOL);
            }
            foreach ($res['data'] as $item){
                if(!$item)
                    continue;
                $count=count($item);
                for ($i=0;$i<$count;$i++){
                    if(!$item[$i])
                        continue;
                    if(!$this->saveName([
                        'name'=>$item[$i]['name']
                    ]))
                        exit($this->error[-6].PHP_EOL);
                }
            }
            //msleep(1500);
        }
    }

    public function spider_name2(){
        $url='http://name.renren.com/tongMing';
        $maxPage=365534+10000;
        $this->http_init();
        //366534
        for ($i=367345;$i<$maxPage;$i++){
            echo '开始采集第'.$i.'页-----------'.PHP_EOL;
            $res=$this->client->http($url.$i);
            if($res===false){
                exit($this->error[-1].PHP_EOL);
            }
            if($this->client->getHttpCode()!==200){
                echo '  http_code:'.$this->client->getHttpCode().PHP_EOL;
                exit($this->error[-2].PHP_EOL);
            }
            $res=Selector::find($res,'regex,multi','%<li><a[^>]+>(?P<name>.*?)</a>%','name','<ul class="index_more_list">{%|||%}</ul>');
            if(!$res){
                exit($this->error[-4].PHP_EOL);
            }
            $isStop=false;
            foreach ($res as $item){
                if(!$this->checkName($item)){
                    echo '  '.$item.'=>不符合要求路过----'.PHP_EOL;
                    continue;
                }
                if(!$this->saveName(['name'=>$item])){
                    echo ($this->error[-6].PHP_EOL);
                    $isStop=true;
                    continue;
                }
            }
            if($isStop)
                exit($this->error[-6].PHP_EOL);
        }
    }

    protected function checkName($name){
        return preg_match('/[a-z0-9]+/i',$name) ===0;
    }

    protected function saveName($data){
        if($this->model->from('caiji_renren_name')->eq('name',$data['name'])->find(null,true)){
            echo '  重复：'.$data['name'].'----------- '.PHP_EOL;
            return true;
        }else{
            if($this->model->from('caiji_renren_name')->insert($data)){
                echo '  入库：'.$data['name'].'----------- '.PHP_EOL;
                return true;
            }
            return false;
        }
    }

    protected function http_init(){
        $this->newClient(['opt'=>[
            CURLOPT_TIMEOUT=>8,//下载时应该按目标文件大小设置大一点
            //CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0',
            //CURLOPT_COOKIE=>'bdshare_firstime=1563956041170; Hm_lvt_2d4a3d45dac6e1a64c396d24f801ba10=1563956041; Hm_lpvt_2d4a3d45dac6e1a64c396d24f801ba10=1563956041',
            //CURLOPT_REFERER=>'http://name.renren.com/',
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

    public function username(){
        $where=[['isdone','eq',0]];
        $table='zuanke8';
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            echo '开始处理'.$item['id'].'---------'.PHP_EOL;
            if(!$item['content']){
                $this->model->from($table)->eq('id',$item['id'])->update(['isdone'=>1]);
                return;
            }
            $item['content']=explode('{%|||%}',$item['content']);
            $isFalse=false;
            foreach ($item['content'] as $value){
                $username=trim(Helper::strCut($value,'{%||%}','{%||%}'));
                if(!$username || !$this->checkUsername($username)){
                    echo '  用户名['.$username.']不符合要求，跳过----'.PHP_EOL;
                    continue;
                }
                if(!$this->updateUsername($username,true)){
                    echo '  失败：更新数据表caiji_renren_name'.$item['id'].'---------'.PHP_EOL;
                    $isFalse=true;
                }
            }
            if($isFalse)
                exit('  有错误'.PHP_EOL);
            $this->model->from($table)->eq('id',$item['id'])->update(['isdone'=>1]);
            //msleep(1000);
        });
    }
    protected function checkUsername($username){
        if(mb_strlen($username)>7)
            return false;
        return preg_match('%\.(net|com\cn)%i',$username)===0;
       // return preg_match('%[\x{4e00}-\x{9fa5}]+%u',$username);
    }
    protected function updateUsername($username,$desc=false){
        if($this->model->from('caiji_renren_name')->eq('username',$username)->find(null,true)){
            echo '  重复:用户['.$username.']入库------'.PHP_EOL;
            return true;
        }
        $order=$desc?'id desc':'id';
        $tmp=$this->model->select('id')->from('caiji_renren_name')->eq('username','')->order($order)->find(null,true);
        if(!$tmp){
            exit('没有需要添加username的项了'.PHP_EOL);
        }
        if($this->model->from('caiji_renren_name')->eq('id',$tmp['id'])->update(['username'=>$username])){
            echo '  --成功:用户['.$username.']入库------'.PHP_EOL;
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 签名采集
     *---------------------------------------------------------------------*/
    public function qianming(){
        $this->http_init();
        for ($i=1;$i<160;$i++){
            $url='http://www.qqgexingqianming.com/jingdian/'.$i.'.htm';
            echo '开始采集第'.$i.'页-----------'.PHP_EOL;
            $res=$this->client->http($url);
            if($res===false){
                exit($this->error[-1].PHP_EOL);
            }
            if($this->client->getHttpCode()!==200){
                echo '  http_code:'.$this->client->getHttpCode().PHP_EOL;
                exit($this->error[-2].PHP_EOL);
            }
            $res=Selector::find($res,'regex,multi','%<li>(?P<signature>.*?)</li>%','signature','<ul class="list" id="list1">{%|||%}</ul>');
            if(!$res){
                exit($this->error[-4].PHP_EOL);
            }
            $isStop=false;
            foreach ($res as $item){
                $item=$this->filterSign($item);
                if(!$this->checkSign($item)){
                    echo '  不符合要求，跳过:'.$item.'----'.PHP_EOL;
                    continue;
                }
                if(!$this->saveSign($item))
                    $isStop=true;
            }
            if($isStop)
                exit($this->error[-6].PHP_EOL);
            msleep(200);
        }

    }

    protected function saveSign($signature){
        $md5=md5($signature);
        if($this->model->from('caiji_renren_name')->eq('sign_md5',$md5)->find(null,true)){
            echo '  重复的signature：'.$signature.'----------- '.PHP_EOL;
            return true;
        }
        $tmp=$this->model->select('id')->from('caiji_renren_name')->eq('sign_md5','')->find(null,true);
        if(!$tmp){
            exit('  没有需要添加signature的项了'.PHP_EOL);
        }
        if($this->model->from('caiji_renren_name')->eq('id',$tmp['id'])->update([
            'sign_md5'=>$md5,
            'signature'=>$signature
        ])){
            echo '  成功入库signature：'.$signature.'-------'.PHP_EOL;
            return true;
        }
        return
            false;
    }

    protected function filterSign($text){
        return trim(strip_tags($text));
    }
    protected function checkSign($text){
        return (mb_strlen($text)>20);
    }

    //知呼user
    public function zhihu(){
        $this->http_init();
        for ($i=342;$i<8680;$i++){
            $url='https://www.zhihu.com/people/y-i-x/followers?page='.$i;
            //$url='https://www.zhihu.com/org/zhi-hu-ri-bao-51-41/followers?page='.$i;
            echo '开始采集第'.$i.'页-----------'.PHP_EOL;
            $res=$this->client->http($url);
            if($res===false){
                exit($this->error[-1].PHP_EOL);
            }
            if($this->client->getHttpCode()!==200){
                echo '  http_code:'.$this->client->getHttpCode().PHP_EOL;
                exit($this->error[-2].PHP_EOL);
            }
            // <script id="js-initialData" type="text/json">
            $res=json_decode(Helper::strCut($res,'"entities":{"users":',',"questions":'),true);
            if(!$res){
                exit($this->error[-3].PHP_EOL);
            }
            $isFalse=false;
            $j=1;
            foreach ($res as $item){
                if(!$item['name'] || $j==1){
                    $j++;
                    continue;
                }
                if(!$this->checkUsername($item['name'])){
                    echo '  字数不符合要求跳过-----'.PHP_EOL;
                    continue;
                }
                if(!$this->updateUsername($item['name'])){
                    echo '  失败：更新数据表caiji_renren_name---------'.PHP_EOL;
                    $isFalse=true;
                }//else
                    //echo '  --成功:用户['.$item['name'].']入库------'.PHP_EOL;
            }
            if($isFalse)
                exit($this->error[-6].PHP_EOL);
            msleep(1200,500);
        }
    }

    //人的出生日期，居住地，
    public function more(){
        $table='caiji_renren_name';
        $where=[['isdo','eq',0]];
        if(isset($this->startId) && $this->startId>0)
            $where[]=['id','gt',$this->startId];
        if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
            $where[]=['id','lte',$this->maxId];
        //echo '1976-01-01 00:00:00=>'.strtotime('1976-01-01 00:00:00').PHP_EOL;
        //echo '2004-01-01 00:00:00=>'.strtotime('2004-01-01 00:00:00').PHP_EOL;
        //1976-01-01 00:00:00=>189273600
        //2004-01-01 00:00:00=>1072886400
        $this->doLoop(99999999,function ()use ($table,$where){
            return $this->model->select('id')->from($table)->_where($where)->limit(30)->findAll(true);
        },function ($item)use ($table,&$citys){
            $update=['isdo'=>1];
            $update['birthday']=mt_rand(189273600,1072886400);
            $update['city']=$this->getRandCity();
            if($this->model->from($table)->eq('id',$item['id'])->update($update))
                echo '成功处理id=>'.$item['id'].'-------'.PHP_EOL;
            else
                exit('失败处理id=>'.$item['id'].'-------'.PHP_EOL);
            //exit();
        });
    }
    protected function getCity(){
       $fp=fopen(ROOT.'/data/city.txt','rb');
        while (feof($fp)===false) {
            yield fgets($fp);
        }
        fclose($fp);
    }
    protected function getRandCity(){
        $rand=mt_rand(0,333);
        $city=$this->getCity();
        $i=0;
        foreach ($city as $item){
            if($i==$rand)
                return trim($item);
            $i++;
        }
    }

    public function dodo2(){
        $table='caiji_bilibili_comment';
        $where=[['status','eq',1]];
        if(isset($this->startId) && $this->startId>0)
            $where[]=['id','gt',$this->startId];
        if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
            $where[]=['id','lte',$this->maxId];
        $total=99999999;
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->select('id,from_id,content,more')->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            echo '开始处理：id=>'.$item['id'].'---------------'.PHP_EOL;
            $item['content2']=strip_tags($item['content']);
            $length=mb_strlen($item['content2']);
            $isFalse=false;
            if($length>=30 and $length<60){
                if(!$this->saveSign($item['content2']))
                    $isFalse=true;
            }elseif($length>=60){
                if(!$this->saveToText($item['content']))
                    $isFalse=true;
            }
            if(!$this->commentHandle($item['more']))
                $isFalse=true;
            if($isFalse)
                exit('  入库出错'.PHP_EOL);
            if($this->model->from($table)->eq('id',$item['id'])->delete())
                echo '  成功：删除-----'.PHP_EOL;
            else
                echo '  失败：删除-----'.PHP_EOL;
            //msleep(2000);
        });
    }
    protected function commentHandle($comment){
        if(!$comment)
            return true;
        $arr=explode('{%|||%}',$comment);
        $isFalse=false;
        foreach ($arr as $item){
            list(,,$content)=explode('{%||%}',$item);
            $content=$this->bilibiliFilter($content);
            $content2=strip_tags($content);
            $length=mb_strlen($content2);
            if($length>=30 and $length<60){
                if(!$this->saveSign($content2))
                    $isFalse=true;
            }elseif($length>=60)
                if(!$this->saveToText($comment))
                    $isFalse=true;
        }
        return $isFalse==false;
    }

    protected function saveToText($str){
        $md5=md5($str);
        $table='caiji_renren_name';
        if($this->model->from($table)->eq('md5',$md5)->find(null,true)){
            echo '  重复text：----------- '.PHP_EOL;
            return true;
        }else{
            $tmp=$this->model->select('id')->from($table)->eq('md5','')->find(null,true);
            if(!$tmp)
                exit('  所有人都添加了text了'.PHP_EOL);
            if($this->model->from($table)->eq('id',$tmp['id'])->update([
                'md5'=>$md5,
                'text'=>$str
            ])){
                echo '  更新text：id=>'.$tmp['id'].'----------- '.PHP_EOL;
                return true;
            }
            return false;
        }
    }

    protected function bilibiliFilter($str){
        return '<p>' . preg_replace([
                '%https?://[-A-Za-z0-9+&@#/\%?=~_|!:,.;]+[-A-Za-z0-9+&@#/\%=~_|]%',
                '%[0-9a-zA-Z.]+\.(com|net|cn|org|cc|us|vip|club|xyz|me|io|wang|win)%',
                '/bili/i',
                '/bilibili/i',
                '/[bB]\s*站/',
                '/\n+/',
                '/\s{2,}/',
                '/up主/i',
                '/\[.*?\]/',
                '%回复 @.+?:%'
            ],[
                '',
                '',
                '社区',
                '社区',
                '社区',
                '<br>',
                ' ',
                '楼主',
                '',
                ''
            ],$str). '</p>';
    }
}
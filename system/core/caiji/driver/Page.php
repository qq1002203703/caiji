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


namespace core\caiji\driver;
use core\Caiji;
use extend\Selector;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
class Page extends Caiji
{
    protected $option=[
        'name'=>'',//string,rule_name 采集规则名（独一无二的）
        'url'=>'',//string 起始页url
        'isloop'=>true,//bool 是否循环拖动
        'nextPage'=>'', //string 定位下一页按钮的css，默认为空不用点击分页
        'nextPageInterval'=>1,//string 间隔多少次点一下nextPage,默认1次，注：只有nextPage不为空时，此项才起作用
        'scroll'=>false,  //是否要慢慢滚动到底部，默认false即一下子滚动到底部
        'iscookie'=>false,//是否要读取文件的cookie
        'cookieFile'=>'',//cookie文件名，如果为空会被$this->setCookie()设为cache/caiji/cookie/{$caijiRuleId}.txt
        //chrome浏览器设置项
        'chrome'=>[],
        'outType'=>2,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plugSave'=>'\shell\caiji\plugin\Taobaotoutiao@pageSave',
        'plugBeforSelector'=>'',
        'plugCheckLogin'=>'\shell\caiji\plugin\Taobaotoutiao@checkLogin',
        'reTimes'=>0,
        'table'=>'',//必须
        'downloadTable'=>'',//必须
        'rule'=>[
            'type'=>'reg',//匹配方式：分别为'reg'、'xpath'和'json'
            'cut'=>'',//截取中间内容
            'reg'=>'#<div class="image group">\s*<div class="grid images_3_of_1">\s*<a href="([^">]+)" title="[^">]+" target="_blank"><img src="([^">]+)"[^>]*></a>\s*</div>#i',//标签匹配正则
            'tags'=>['url'],//标签
            'filter'=>[
                //'url'=>['reg{%|||%}/\?.*?$/{%|||%}','replace{%|||%} {%|||%}','html{%|||%}<p>','union{%|||%}http://www.aaa.com/{%xxoo%}']
                'url'=>['trueurl{%|||%}0'],
            ],
            'notEmpty'=>['url'],//不能为空的标签名，tags是数组，此项也是数组，tags为空，此项设置真或假
            /*'file'=>[
                'thumb'=>[
                    'type'=>'2',//种类：1=>为图片，2=>为文件
                    'replace_path'=>'{%Y%}/{%m%}/{%d%}/thumb_{%id%}',
                ]
            ],*/
        ]
    ];
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected $driver;
    protected  $repeatTimeTmp;

    public function __construct($option)
    {
        parent::__construct($option);
        $this->taskInit();
    }
    protected function taskInit(){
        $this->model=app('\core\Model');
        $this->model->table=$this->option['table'];
        //设置最大运行数
        if(isset($this->option['run_max']) && $this->option['run_max'])
            $this->runMax=$this->option['run_max'];
    }

    /** ------------------------------------------------------------------
     * 实例化当前类，会对参数进行合法性检测，如果参数不合法会实例化失败
     * @param $option
     * @return string | \core\Caiji : 参数合法性检测不通过 返回错误信息,否则返回实例化的当前类
     *--------------------------------------------------------------------*/
    static public function create($option){
        if(!$option)
            return '参数不能为空';
        if(is_string($option))
            $option=json_decode($option,true);
        if(!is_array($option))
            return '参数格式不正确，必须是数组或能转为数组的json格式字符串';
        if(!$option['name'])
            return '参数没有设置采集名';
        if(!$option['url'])
            return '参数没有设置起始页url';
        if(!$option['table'])
            return '参数没有设置table';
        $class=__CLASS__;
        return new $class($option);
    }
    //入口
    public function start(){
        $host = 'http://localhost:4444/wd/hub';
        $this->startEcho('Rule name:'.$this->option['name'].',pageUrl:'.$this->option['url']);
        try{
            $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome(), 15000);
            $this->driver->get($this->option['url']);
            $this->setCookie();
            while (1){
                $result=[];
                if(isset($this->option['plugBeforSelector']) && $this->option['plugBeforSelector']){
                    $result=$this->callback( $this->option['plugBeforSelector'],[$this->driver,$this->option]);
                }else{
                    $result['html']=$this->driver->getPageSource();
                    $result['data']=Selector::findAll($result['html'],$this->option['rule']['reg'],$this->option['rule']['tags'],$this->option['rule']['cut'],$this->option['rule']['type']);
                }
                $this->resultFilter($result['data'],$this->option['rule']['filter'],$this->option['rule']['notEmpty'],$this->option['url']);
                $this->option['rule']['file']=$this->option['rule']['file']??[];
                //保存数据
                if(isset($this->option['plugSave']) && $this->option['plugSave']){
                    $ret_save=$this->callback($this->option['plugSave'],[$result['data'],$this->option['rule']['file']]);
                }else{
                    $ret_save=$this->save($result['data'],$this->option['rule']['file']);
                }
                unset($result);
                //保存结果，如果全部重复证明没有新内容，停止运行
                if($ret_save===-1){
                    break;
                }
                if($this->option['isloop']){
                    $this->scroll($this->option['scroll'],true);
                }else{
                    break;
                }
            }
        }catch (\Exception $e){
            //echo  $e->getMessage().PHP_EOL;
            $str='      Message:'.$e->getMessage().PHP_EOL;
            $str.='     File:'.$e->getFile().PHP_EOL;
            $str.='     Code:'.$e->getCode().PHP_EOL;
            $this->outPut($str,true);
        }
    }

    /** ------------------------------------------------------------------
     * 运行js使页面滚动到底部/项部
     * @param bool $isSlowly:是否慢慢滚动
     * @param bool $isToBottom 是否向下滚动
     *---------------------------------------------------------------------*/
    protected function scroll($isSlowly=false,$isToBottom=true){
        $height=$this->driver->executeScript('if(document.compatMode == "BackCompat") return window.document.body.scrollHeight;else return Math.max(window.document.documentElement.scrollHeight,window.document.documentElement.clientHeight);');
        if($isSlowly){
            $y=40;
            if($isToBottom){
                $i=0;
                while ($i*$y <$height){
                    $this->driver->executeScript('window.scrollTo(0,'.$y*($i+1) .')');
                    $i++;
                    usleep(50000);
                }
            }else{
                $i=(int) $height/$y;
                while ($i > 0){
                    $this->driver->executeScript('window.scrollTo(0,'.($i*$y) .')');
                    $i--;
                    usleep(50000);
                }
            }
        }else{
            if($isToBottom)
                $this->driver->executeScript('window.scrollTo(0,'.$height .')');
            else
                $this->driver->executeScript('window.scrollTo(0,0)');
            usleep(50000);
        }
    }

    protected function setCookie(){
        if(isset($this->option['iscookie']) && $this->option['iscookie']){
            if(isset($this->option['cookieFile']) && $this->option['cookieFile']){
                $file=ROOT.'/'.$this->option['cookieFile'];
            }else{
                $file=ROOT.'/cache/caiji/cookie/'.$this->option['name'].'.txt';
            }
            if(!is_file($file))
                return false;
            $cookie= file_get_contents($file);
            if(!$cookie)
                return false;
            $cookie=json_decode($cookie,true);
            if($cookie===false){
                $str=str_replace('; ','&',$cookie);
                parse_str($str,$cookie);
            }
            if(!is_array($cookie))
                return false;
            $this->driver->manage()->addCookie($cookie);
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 结果集过滤:按过滤规则过滤数据，过滤时同时检测是否有设置不能为空的标签，在结果集中去除该标签为空的项
     * @param array $results：结果集（二维数组或一维数组）
     * @param object|string $filter_options：各标签过滤规则集合
     * @param  array|bool $notEmpty:不能为空的标签集合
     * @param string $pageUrl
     * @return array|null 结果集过滤后，不为空返回过滤后的结果集，否则返回NULL
     *--------------------------------------------------------------------*/
    protected function resultFilter($results,$filter_options,$notEmpty,$pageUrl){
        if(!$filter_options)
            return $results;
        $ret=[];
        foreach ($results as $key =>$result){
            //if($this->checkStop()){break;}
            if(is_array($result)){
                foreach ($result as $k =>$v){
                    if(isset($filter_options[$k]) && $filter_options[$k])
                        $ret[$key][$k]=$this->filter($filter_options[$k],$v,$pageUrl);
                    else
                        $ret[$key][$k]=$v;
                    if(in_array($k,$notEmpty) && !$ret[$key][$k] ){
                        unset($ret[$key]);
                        break;
                    }
                }
            }else{
                $ret[$key]=$this->filter($filter_options,$result,$pageUrl);
                if($notEmpty && !$ret[$key] ){
                    unset($ret[$key]);
                }
            }
        }
        return $ret ? array_values($ret) :null;
    }
    //结果集入库
    protected function save($results,$fileRule){
        foreach ($results as $v){
            //检测连续重复次数是否已经达到最大值
            if($this->checkTimes($this->repeatTimeTmp,$this->option['reTimes'])){
                $this->isStop=true;
                $this->error='URL is repeat to the maxnum:'.$this->repeatTimeTmp;
                return -1;
            }
            if(is_array($v)){
                //检测重复
                if($this->model->eq('url',$v['url'])->find(null,true)){
                    $this->outPut(' URL is repeat！url:'.$v['url'].PHP_EOL);
                    $this->countTimes(true,$this->repeatTimeTmp);
                }else{
                    $this->countTimes(false,$this->repeatTimeTmp);
                    //$v['status']=0;
                    $v['caiji_id']=$this->option['name'];
                    if($id=$this->model->insert($v)){
                        //文件
                      /*  if($fileRule){
                            foreach ($fileRule as $k =>$item){
                                $replace_path=$item->replace_path ?? '';
                                if($item->type==2){//文件
                                    $this->model->_exec('insert into `'.$this->prefix.$this->downloadOption['table'].'` ( `true_url`, `replace_path`, `type`, `cid`) VALUES (?,?,?,?)',[$v[$k],$replace_path,$k,$id],false);
                                }
                                //elseif ($item->type==1){//图片
                                //找出所有图片=>去重复=>每个图片循环添加到下载表中=>原字符串中图片替换为占位符，并更新

                                // }
                            }
                        }*/
                        $this->outPut(' Save success！id:'.$id.';url:'.$v['url'].PHP_EOL);
                        $this->total['all']++;
                    }else{
                        $this->outPut(' Save failed！url:'.$v['url'].PHP_EOL);
                    }
                }
            }else{
                //单项时只有url
                if($this->model->eq('url',$v)->find(null,true)){
                    $this->outPut(' URL is repeat！url:'.$v.PHP_EOL);
                    $this->countTimes(true,$this->repeatTimeTmp);
                }else{
                    $this->countTimes(false,$this->repeatTimeTmp);
                    if($id=$this->model->insert(['url'=>$v,'caiji_name'=>$this->option['name']])){
                        //单项不会有文件
                        $this->outPut(' Save success！id:'.$id.';url:'.$v.PHP_EOL);
                        $this->total['all']++;
                    }else{
                        $this->outPut(' Save failed！url:'.$v.PHP_EOL);
                    }
                }
            }
        }
        return 0;
    }

    /** ------------------------------------------------------------------
     * 检测是否已经登陆,需要插件
     * @return  bool
     *---------------------------------------------------------------------*/
    protected function checkLogin(){
        $isLogin=$this->callback($this->option['plugCheckLogin'],[$this->driver->getPageSource(),$this->driver->manage()->getCookies()]);
        return ($isLogin===0) ? true :false;
    }
}
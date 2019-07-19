<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 常规列表页采集类
 * ======================================*/

namespace core\caiji\normal;
use extend\Selector;
use extend\Helper;

class Getlist extends Base
{
    protected  $option=[
        'runMax'=>2,//每次发布的条数，0表示不限制
        'plug'=>'',//插件
    ];
    protected $result=[
        'http://www.lovehzb.com/category-1.html',
        'http://www.lovehzb.com/category-2.html',
        'http://www.lovehzb.com/category-3.html',
        'http://www.lovehzb.com/category-4.html',
        'http://www.lovehzb.com/category-6.html',
        'http://www.lovehzb.com/category-7.html',
        'http://www.lovehzb.com/category-8.html',
        'http://www.lovehzb.com/category-9.html',
        'http://www.lovehzb.com/category-10.html',
        'http://www.lovehzb.com/category-11.html',
        'http://www.lovehzb.com/category-12.html',
        'http://www.lovehzb.com/category-13.html',
        'http://www.lovehzb.com/category-14.html',
        'http://www.lovehzb.com/category-15.html',
        'http://www.lovehzb.com/category-16.html',
        'http://www.lovehzb.com/category-17.html',
        'http://www.lovehzb.com/category-18.html',
        'http://www.lovehzb.com/category-19.html',
        'http://www.lovehzb.com/category-20.html',
        'http://www.lovehzb.com/category-21.html',
        'http://www.lovehzb.com/category-22.html',
        'http://www.lovehzb.com/category-23.html',
        'http://www.lovehzb.com/category-24.html',
        'http://www.lovehzb.com/category-25.html',
        'http://www.lovehzb.com/category-26.html',
        'http://www.lovehzb.com/category-27.html',
        'http://www.lovehzb.com/category-28.html',
        'http://www.lovehzb.com/category-29.html',
        'http://www.lovehzb.com/category-30.html',
        'http://www.lovehzb.com/category-31.html',
        'http://www.lovehzb.com/category-32.html',
        'http://www.lovehzb.com/category-33.html',
        'http://www.lovehzb.com/category-34.html',
        'http://www.lovehzb.com/category-35.html',
        'http://www.lovehzb.com/category-36.html',
        'http://www.lovehzb.com/category-37.html',
        'http://www.lovehzb.com/category-38.html',
        'http://www.lovehzb.com/category-39.html',
        'http://www.lovehzb.com/category-40.html',
        'http://www.lovehzb.com/category-41.html',
        'http://www.lovehzb.com/category-42.html',
        'http://www.lovehzb.com/category-43.html',
        'http://www.lovehzb.com/category-44.html',
        'http://www.lovehzb.com/category-45.html',
        'http://www.lovehzb.com/category-66.html',
        'http://www.lovehzb.com/category-67.html',
        'http://www.lovehzb.com/category-68.html',
        'http://www.lovehzb.com/category-69.html',
        'http://www.lovehzb.com/category-70.html',
        'http://www.lovehzb.com/category-71.html',
        'http://www.lovehzb.com/category-5.html',
        'http://www.lovehzb.com/category-46.html',
        'http://www.lovehzb.com/category-47.html',
        'http://www.lovehzb.com/category-49.html',
        'http://www.lovehzb.com/category-50.html',
        'http://www.lovehzb.com/category-51.html',
        'http://www.lovehzb.com/category-52.html',
        'http://www.lovehzb.com/category-53.html',
        'http://www.lovehzb.com/category-54.html',
        'http://www.lovehzb.com/category-55.html',
        'http://www.lovehzb.com/category-56.html',
        'http://www.lovehzb.com/category-57.html',
        'http://www.lovehzb.com/category-58.html',
        'http://www.lovehzb.com/category-59.html',
        'http://www.lovehzb.com/category-60.html',
        'http://www.lovehzb.com/category-61.html',
        'http://www.lovehzb.com/category-62.html',
        'http://www.lovehzb.com/category-63.html',
        'http://www.lovehzb.com/category-64.html',
        'http://www.lovehzb.com/category-65.html',
        'http://www.lovehzb.com/category-48.html',
    ];
    protected $resultString='';
    /**
     * constructor:构造函数，载入公共初始化项的同时，进行任务初始化
     * @param array $option
     */
    protected function __construct($option)
    {
        parent::__construct($option);
        $this->taskInit();
    }

    /** ------------------------------------------------------------------
     * 任务初始化
     *---------------------------------------------------------------------*/
    protected function taskInit(){
        $this->model=app('\core\Model');
        $this->model->table=$this->option['table'];
        //设置最大运行数
        if(isset($this->option['run_max']) && $this->option['run_max'])
            $this->runMax=$this->option['run_max'];
        //curl初始化
        $this->curlInit();
    }

    /** ------------------------------------------------------------------
     * 实例化当前类，会对参数进行合法性检测，如果参数不合法会实例化失败
     * @param $option
     * @return string | $this : 参数合法性检测不通过 返回错误信息,否则返回实例化的当前类
     *--------------------------------------------------------------------*/
    static public function create($option=[]){
        if(!$option)
            return '参数不能为空';
        if(is_string($option))
            $option=json_decode($option,true);
        if(!is_array($option))
            return '参数格式不正确，必须是数组或能转为数组的json格式字符串';
        if(!isset($option['name']) || !$option['name'])
            return '参数没有设置采集名';
        if(!isset($option['table']) || !$option['table'])
            return '参数没有设置table';
        $class=__CLASS__;
        return new $class($option);
    }

    public function run(){
        /*if(strpos($this->option['url'],'{%')===false){
            //$this->levelHandler($this->option['url'],$this->option['rule_00']);
            $this->getResult($this->option['url'],$this->option['rule_00']);
        }else{
            $this->parseUrl($this->option['url']);
        }*/
        //dump($this->result);
        if($this->result){
            foreach ($this->result as $item){
                echo '  正在分析：'.$item.PHP_EOL;
                $html=$this->caiji2($item);
                if(is_int($html)){
                    if($this->debug || $this->runOnce){
                        break;
                    }
                    continue;
                } else{
                    $tmp=Helper::callback($this->option['plug'],[$html,$this->option['rule_01'],$item]);
                    if($tmp){
                        if($this->resultString)
                            $this->resultString.='{%|||%}'.$tmp;
                        else
                            $this->resultString=$tmp;
                    }
                    if($this->debug || $this->runOnce){
                        break;
                    }
                }
            }
        }
        echo '  最后结果：'.PHP_EOL.'        '.$this->resultString.PHP_EOL;
    }

    /** ------------------------------------------------------------------
     * 解析起始网址
     * @param string $pages http://www.xxx.com/archiver/?fid-15.html&page={%0,1,40236,1,1,0%}
     * @return int
     *---------------------------------------------------------------------*/
    protected function parseUrl($pages){
        $params=explode(',',Helper::strCut($pages,'{%','%}')) ;
        switch ($params[0]){
            case '0': //公差
                //开始项=>$params[1],总页数=>$params[2],公差或公比=>$params[3]
                if(isset($params[4]) && $params[4]){
                    for ($i=$params[1];$i<= $params[2]*$params[3]+$params[1]-$params[3];$i+=$params[3]){//第二个循环
                        if($this->checkStop()){break;}
                        $page=preg_replace('/\{%.+?%\}/',$i,$pages);
                        //$this->levelHandler($page,$this->option['rules']);
                        $this->getResult($page,$this->option['rule_00']);
                    }
                }else{
                    for ($i=$params[2]*$params[3]+$params[1]-$params[3];$i>=$params[1];$i -=$params[3]){//第二个循环
                        if($this->checkStop()){break;}
                        $page=preg_replace('/\{%.+?%\}/',$i,$pages);
                        //$this->levelHandler($page,$this->option['rules']);
                        $this->getResult($page,$this->option['rule_00']);
                    }
                }
                break;
            case '1': //公比
                if($params[3]=='1'){
                    $this->outPut('公比不能为1'.PHP_EOL,true);
                    return -1;
                }
                $max=$params[1]*pow($params[3],$params[2]-1);
                if($max>2147483647){
                    $this->outPut('过大的公比值'.PHP_EOL,true);
                    return -1;
                }
                if(isset($params[4]) && $params[4]){
                    for ($i=$params[1];$i<= $max;$i=$i*$params[3]){//第二个循环
                        if($this->checkStop()){break;}
                        $page=preg_replace('/\{%.+?%\}/',$i,$pages);
                        //$this->levelHandler($page,$this->option['rules']);
                        $this->getResult($page,$this->option['rule_00']);
                    }
                }else{
                    for ($i=$max;$i>=$params[1];$i =$i/$params[3]){//第二个循环
                        if($this->checkStop()){break;}
                        $page=preg_replace('/\{%.+?%\}/',$i,$pages);
                        //$this->levelHandler($page,$this->option['rules']);
                        $this->getResult($page,$this->option['rule_00']);
                    }
                }
                break;
            case '2':
        }
        return 0;
    }

    /** ------------------------------------------------------------------
     * 数据采集和过滤
     * @param string $url 要采集的网址
     * @param array $rule 当前规则
     * @param string $html 把采集网址的原码返回这个变量中
     * @param string $msg 额外信息
     * @return array|int
     *--------------------------------------------------------------------*/
    protected function caiji($url,$rule,&$html,$msg=''){
        if(isset($this->option['plugBeforeCaiji']) && $this->option['plugBeforeCaiji']){
            //插件
            $html=$this->callback($this->option['plugBeforeCaiji'],[$url,$rule,$this->option,&$this->curl]);
        }else{
            $html=$this->curl->add($url,[],$this->option['curl']['options']);
        }
        if($html===false){
            $this->countTimes(true,$this->errorTimesTmp);
            $this->outPut(' '.$msg.'Url access failed! url: "'.$url.'" ;msg:'.$this->curl->errorMsg.PHP_EOL,true);
            return 1;
        }
        $this->countTimes(false,$this->errorTimesTmp);
        $html=$this->curl->encoding($html);
        //插件
        if(isset($this->option['plugBeforeSelector']) && $this->option['plugBeforeSelector']){
            $html=$this->callback($this->option['plugBeforeSelector'],[$html,$rule,$this->option,$this->curl]);
        }
        $rule['cut'] =$rule['cut'] ?? '';
        $result=$this->selector($html,$rule);
        if($result===false){//正则出错
            $this->error='Selector error!reg :'.$rule['reg'].';msg:'.Selector::getError();
            $this->isStop=true;
            return 2;
        }elseif(!$result){//正则无法匹配到结果
            $this->outPut(' '.$msg.'Selector find null!reg :'.$rule['selector'].';msg:'.Selector::getError().PHP_EOL,true);
            return 3;
        }
        $this->debug([$result],$msg.'.....上面为：正则匹配结果'.PHP_EOL,false);
        //过滤
        $result=$this->resultFilter($result,$rule['filter'],$rule['notEmpty'],$url);
        if(!$result){
            $this->outPut(' '.$msg.'After result filter ,result is empty!'.PHP_EOL);
            return 4;
        }
        $this->debug([$result],$msg.'.....上面为：最终入库数据'.PHP_EOL,true);
        return $result;
    }

    protected function caiji2($url){
        $html=$this->curl->add($url,[],$this->option['curl']['options']);
        if($html===false){
            $this->outPut(' Url access failed! url: "'.$url.'" ;msg:'.$this->curl->errorMsg.PHP_EOL,true);
            return 1;
        }
        $html=$this->curl->encoding($html);
        return $html;
    }

    /** ------------------------------------------------------------------
     * 获取采集结果
     * @param string $page url
     * @param array $rule 规则集
     * @param bool $multiLevel 是否不是最后一层网址,为true时，不入库且返回值为结果集，为false时，入库且返回值为0
     * @param bool $multiPage 是否是多分页
     * @return int|array:出错时返回非0数字，不出错时有两种情况，$multiLevel=true||$multiPage=true返回结果集（一维数组或二维数组）,否则返回0
     *--------------------------------------------------------------------*/
    protected function getResult($page,$rule){
        //$this->outPut('Page:'.$page.PHP_EOL);
        $result=$this->caiji($page,$rule,$html,'');
        if(is_int($result)) return $result;
        unset($html);
        if(! $this->debug){
            $this->save($result);
        }
        return  0;
    }
    protected function save($result){
        $this->result=array_merge($this->result,$result);
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
        if(!$filter_options && !$notEmpty){
            return $results;
        }
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
                if(isset($filter_options) && $filter_options)
                    $ret[$key]=$this->filter($filter_options,$result,$pageUrl);
                else
                    $ret[$key]=$result;
                if($notEmpty && !$ret[$key] ){
                    unset($ret[$key]);
                }
            }
        }
        return $ret ? array_values($ret) :null;
    }

    /** ------------------------------------------------------------------
     * 测试
     * @param string $target
     * @param array $data
     *---------------------------------------------------------------------*/
    public function doTest($target='',$data=[]){
        $this->debug=true;
        $fields=explode(',',$this->getFields());
        if($target)
            $this->option['target']=$target;
        if(!$data){
            foreach ($fields as $field){
                $data[$field]=$field.'测试：'.$this->randomKeys(5);
            }
        }
        echo 'post的数据：'.dump($data,false);
        if(isset($this->option['plug'])&&$this->option['plug'] ){//使用插件
            $ret=$this->callback($this->option['plug'],[$data,$this->option,$this->curl]);
        }else {//不使用
            $ret=$this->post($data);
        }
        echo '  结果：';
        if($ret>0){
            echo '  code=>'.$ret.';'.$this->errorCode[$ret].PHP_EOL;
        }else
            echo '  成功发布'.PHP_EOL;
    }
}
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
class Page extends Base
{
    protected $option=[
        'name'=>'',//string,rule_name 采集规则名（独一无二的）
        'url'=>'', //string 起始页url
        'type'=>1, //种类
        'retimes'=>0,//重复多少次停止
        'outType'=>2,//输出方式（1或2），默认为2时重要信息保存日志，为1直接输出
        'plugSave'=>'',
        'plugBeforCaiji'=>'',
        'plugBeforSelector'=>'',
        'plugCheckLogin'=>'',
        'table'=>'',//必须
        'downloadTable'=>'',//必须
        'rules'=>[
            [
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
                /*'multi_page'=>[ //采集分页，如果不采集分页可以删除此项
                    'isdo'=>false,//是否要采集分页,必须声明此项为真才会采集分页
                    'max'=>0,//最大页数，0为不限制
                    'type'=>'reg',
                    'cut'=>'',
                    'reg'=>'',
                    'filter'=>['trueurl{%|||%}0'],
                    'tags'=>'',
                    'notEmpty'=>true,
                ],*/
            ],
        ],
        'curl'=>[],
    ];
    //采集分页时保存的分页地址
    protected $pagination;

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
        $this->curlInit();
        $this->createTable('content');
        $this->createTable('download');
    }

    /** ------------------------------------------------------------------
     * 实例化当前类，会对参数进行合法性检测，如果参数不合法会实例化失败
     * @param $option
     * @return string | \core\Caiji : 参数合法性检测不通过 返回错误信息,否则返回实例化的当前类
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
        if(!isset($option['url']) || !$option['url'])
            return '参数没有设置起始页url';
        if(!isset($option['table']) || !$option['table'])
            return '参数没有设置table';
        $class=__CLASS__;
        return new $class($option);
    }

    protected function run(){

        switch ($this->option['type']){
            case 1://普通单层
            case 2://多层
                $pageArr=explode('{%|||%}',$this->option['url']);
                foreach ($pageArr as $page){//第一个循环
                    if($this->checkStop()){
                        $this->endEcho($this->error);
                        break 2;
                    }
                    if(strpos($page,'{%')===false){
                        $this->levelHandler($page,$this->option['rule']);
                    }else{
                        $this->parseUrl($page);
                    }
                }
                break;
            case 3://单页循环
                $html='';
                $i=0;
                $page=$this->option['url'];
                do{//第一个循环
                    if($this->checkStop()) break;
                    if($i==0){
                        $html=$this->curl->add($page,[],$this->option['curl']['options']);
                        $i=1;
                    } else{
                        $html=$this->callback($this->option['plug_single'],[$html,$this->option['rules'][0],$this->option,$this->curl]);
                    }
                }while($html);
                break;
        }
        if($this->checkStop()){
            $this->endEcho($this->error);
        }else
            $this->endEcho('Run end!');
    }
    /** ------------------------------------------------------------------
     * 获取采集结果
     * @param string $page url
     * @param object $rule 规则集
     * @param bool $multiLevel 是否不是最后一层网址,为true时，不入库且返回值为结果集，为false时，入库且返回值为0
     * @param bool $multiPage 是否是多分页
     * @return int|array:出错时返回非0数字，不出错时有两种情况，$multiLevel=true||$multiPage=true返回结果集（一维数组或二维数组）,否则返回0
     *--------------------------------------------------------------------*/
    protected function getResult($page,$rule,$multiLevel=false,$multiPage=false){
        $this->outPut('Page:'.$page.PHP_EOL);
        $result=$this->caiji($page,$rule,$html,'');
        if(is_int($result)) return $result;
        //多分页采集
        if($multiPage){
            $links=$this->selector($html,$rule['multi_page']);
            if($links){
                $this->pagination = array_merge(array_combine($links, array_fill(0, count( $links), false)), $this->pagination);
            }
        }
        unset($html);
        if(! $this->debug && ! $multiLevel){
            $this->save($result,($rule['file'] ?? ''));
        }
        return ($multiLevel || $multiPage) ? $result : 0;
    }

    /** ------------------------------------------------------------------
     * 分页处理器：判断是否有多分页，有多分页就去多分页循环，没有就直接获取结果
     * @param $page
     * @param $rule
     * @param bool $is_multi
     * @return array|int
     *--------------------------------------------------------------------*/
    protected function pageHandler($page,$rule,$is_multi=false){
        if(isset($rule['multi_page']) && isset($rule['multi_page']['isdo']) &&$rule['multi_page']['isdo']){
            $rule['multi_page']['tags']='';
            $rule['multi_page']['notEmpty']=true;
            $result=$this->caiji($page,$rule['multi_page'],$html,'MultiPage\'s ');
            unset($html);
            if(is_int($result)) return $result;
            foreach ($result as $link){
                $this->pagination[$link]=false;
            }
            return $this->multiPage($rule,$is_multi);
        }else{
            return $this->getResult($page,$rule,$is_multi);
        }
    }

    /** ------------------------------------------------------------------
     * 多分页自动识别(没指定最大分页数时，只有访问完所有分页才会跳出循环)
     * @param object $rule 分页规则
     * @param bool $multiLevel 是否是多层
     * @return array|int
     *---------------------------------------------------------------------*/
    protected function  multiPage($rule,$multiLevel=false){
        $ret=[];
        $max=(int)$rule['multi_page']['max'];
        $i=0;
        while ($max==0 || $i<$max){//第五个循环 没指定最大页数
            if($this->checkStop()) break;
            $link = array_search(false, $this->pagination, true);
            if ($link === false) {//只有所有分页链接访问完才会break
                break;
            }
            $this->pagination[$link] = true;
            $result = $this->getResult($link,$rule,$multiLevel,true);
            $i++;
            if (!is_array($result)) {
                continue;
            }else{
                if($multiLevel){
                    $ret=array_merge($ret,$result);
                }
            }
        }
        return $multiLevel ? $ret : 0;
    }

    /** ------------------------------------------------------------------
     * 解析起始网址
     * @param string $pages
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
                        $this->levelHandler($page,$this->option['rules']);
                    }
                }else{
                    for ($i=$params[2]*$params[3]+$params[1]-$params[3];$i>=$params[1];$i -=$params[3]){//第二个循环
                        if($this->checkStop()){break;}
                        $page=preg_replace('/\{%.+?%\}/',$i,$pages);
                        $this->levelHandler($page,$this->option['rules']);
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
                        $this->levelHandler($page,$this->option['rules']);
                    }
                }else{
                    for ($i=$max;$i>=$params[1];$i =$i/$params[3]){//第二个循环
                        if($this->checkStop()){break;}
                        $page=preg_replace('/\{%.+?%\}/',$i,$pages);
                        $this->levelHandler($page,$this->option['rules']);
                    }
                }
                break;
            case '2':
        }
        return 0;
    }

    //结果集入库
    protected function save($results,$fileRule){
        foreach ($results as $v){
            //检测连续重复次数是否已经达到最大值
            if($this->checkTimes($this->repeatTimeTmp,$this->option['retimes'])){
                $this->isStop=true;
                $this->error='URL is repeat to the maxnum:'.$this->repeatTimeTmp;
                return -1;
            }
            if(is_array($v)){
                //检测重复
                if($this->model->eq('from_id',$v['from_id'])->find(null,true)){
                    $this->outPut(' URL is repeat！url:'.$v['url'].PHP_EOL);
                    $this->countTimes(true,$this->repeatTimeTmp);
                }else{
                    $this->countTimes(false,$this->repeatTimeTmp);
                    $v['caiji_name']=$this->option['name'];
                    if($id=$this->model->insert($v)){
                        //文件
                        if($fileRule){
                            foreach ($fileRule as $k =>$item){
                                $replace_path=$item['replace_path'] ?? '';
                                if($item['type']==2){//文件
                                    $this->model->_exec('insert into `'.$this->prefix.$this->option['downloadTable'].'` ( `true_url`, `replace_path`, `type`, `cid`) VALUES (?,?,?,?)',[$v[$k],$replace_path,$k,$id],false);
                                }
                                //elseif ($item['type']==1){//图片
                                //找出所有图片=>去重复=>每个图片循环添加到下载表中=>原字符串中图片替换为占位符，并更新

                                // }
                            }

                        }
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
            if($this->checkStop()){break;}
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
     * 网址处理器
     * @param $page
     * @param $rules
     * @return  int
     *--------------------------------------------------------------------*/
    protected function levelHandler($page,$rules){
        $res=[];
        $count=count($rules);
        if($count==1){
            $this->pageHandler($page,$rules[0],false);
        }else{
            for($i=0;$i<$count;$i++){//第三个循环 遍历所有层
                if($this->checkStop()) break;
                if($i ==0) {//开始层
                    $res=$this->pageHandler($page,$rules[$i],true);
                }elseif ($i ==$count-1){//最终层
                    $this->multiLevel($res,$rules[$i],false);
                }else{//其它层
                    $res=$this->multiLevel($res,$rules[$i],true);
                }
            }
        }
        return 0;
    }

    /** ------------------------------------------------------------------
     * 多层级网址处理：由上一层提交过来的数据，按规则继续访问下一层
     * @param array $results 上一层获得的结果集
     * @param object $rule 规则集
     * @param bool $is_last 是否是最后一层
     * @return array|int
     *--------------------------------------------------------------------*/
    protected function multiLevel($results,$rule,$is_last){
        $ret=[];
        foreach ($results as $result){//第四个循环
            if($this->checkStop()) break;
            if(is_array($result)){
                $ret=array_merge($ret,$this->pageHandler($result['url'],$rule,$is_last));
            }else{
                $ret=array_merge($ret,$this->pageHandler($result,$rule,$is_last));
            }
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * endEcho
     * @param string $msg
     * @param array $exp
     *--------------------------------------------------------------------*/
    protected function endEcho($msg, $exp=[]){
        if($this->total['all'] > 0 || $this->model->eq('caiji_iscaiji',0)->eq('caiji_name',$this->option['name'])->find(null,true)){
            //添加内容采集队列
            $this->addQueue([
                'method_param'=>'',
                'status'=>0,
                'type'=>1,
                'run_time'=>time(),
                'del_type'=>0,
                'callback'=>'\core\caiji\normal\Content',
                'class_param'=>$this->option['name']
            ],true);
        }
        $this->outPut($msg.' ,Total:'.$this->total['all'].' ;'.date('Y-m-d H:i:s').PHP_EOL,true);
    }


    /** ------------------------------------------------------------------
     * 数据采集和过滤
     * @param string $url 要采集的网址
     * @param object $rule 当前规则
     * @param string $html 把采集网址的原码返回这个变量中
     * @param string $msg 额外信息
     * @return array|int
     *---------------------------------------------------------------------
     */
    protected function caiji($url,$rule,&$html,$msg=''){
        if(isset($this->option['plugBeforCaiji']) && $this->option['plugBeforCaiji']){
            //插件
            $html=$this->callback($this->option['plugBeforCaiji'],[$url,$rule,$this->option,&$this->curl]);
        }else{
            $html=$this->curl->add($url,[],$this->option['curl']['options']);
        }
        if($html===false){
            $this->countTimes(true,$this->errorTimesTmp);
            $this->outPut(' '.$msg.'Url access failed! url: "'.$url.'" ;msg:'.$this->curl->errorMsg.PHP_EOL,true);
            return 1;
        }
        $this->countTimes(false,$this->errorTimesTmp);
        //插件
        if(isset($this->option['plugBeforSelector']) && $this->option['plugBeforSelector']){
            $html=$this->callback($this->option['plugBeforSelector'],[$html,$rule,$this->option,&$this->curl]);
        }
        $rule['cut'] =$rule['cut'] ?? '';
        $html=$this->curl->encoding($html);
        $result=$this->selector($html,$rule);
        if($result===false){//正则出错
            $this->error='Selector error!reg :'.$rule['reg'].';msg:'.Selector::getError();
            $this->isStop=true;
            return 2;
        }elseif(!$result){//正则无法匹配到结果
            $this->outPut(' '.$msg.'Selector find null!reg :'.$rule['reg'].';msg:'.Selector::getError().PHP_EOL,true);
            return 3;
        }
        $this->debug([$result],$msg.'.....上面为：正则匹配结果'.PHP_EOL,false);
        //过滤
        $result=$this->resultFilter($result,$rule['filter'],$rule['notEmpty'],$url);
        if(!$result){
            $this->outPut(' '.$msg.'After result filter ,result is empty!'.PHP_EOL);
            return 4;
        }
        //文件
        if(isset($rule['file']) && $rule['file']){
            $result=$this->resultFile($result,$rule);
        }
        $this->debug([$result],$msg.'.....上面为：最终入库数据'.PHP_EOL,true);
        return $result;
    }

    protected function resultFile($result,$rule){
        if(!$result || !is_array($result))
            return $result;
        foreach ($rule['file'] as $key=> $item) {
            if ($item['type'] == 2) {
                $replace_path = $rule['replace_path'] ?? '';
            }
            foreach ($result as $value) {
                if (is_array($value)) {

                } else {

                }
            }
        }
        return $result;
    }

    public function doTest($option=''){
        $this->debug=true;
        if($option){
            $this->option=array_merge($this->option,$option);
            $this->curlInit();
        }
        $this->run();
    }
}
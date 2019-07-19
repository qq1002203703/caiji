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
namespace core\caiji\normal;
use extend\Selector;
use extend\Helper;
class Content extends Base
{
    /**
     * @var array 存放下载文件和图片的链接
     */
    protected $downloadData;
    /**
     * @var bool 是否有下载项,给最后入库时判断是否有未完成的下载项 参考的
     */
    protected $isHaveDownload=false;
    protected  $option=[
        //格式：'类名@方法名'、'类名::静态方法名'、'函数名'，在未开始采集前，传递参数：array,各项原始数据记录; 返回值：array
        'pluginBefore'=>'',
        //格式：'类名@方法'，各项都采集完后、入库前，传递参数：array,各项数据; 返回值：array
        'pluginAfter'=>'',
        //保存数据时调用
        'pluginSave'=>'',
        'caiji'=>[],
        'curl'=>[
            'setting'=>[
                'login'=>false,
                //'match'=>'',
                'timeOut'=>[7,15],
                //'tryTimes'=>3
                'opt'=>[
                    //CURLOPT_COOKIE=>''
                ]
            ],
            'options'=>[
                'opt'=>[
                    //CURLOPT_REFERER=>'htttps//:www.baidu.com',
                ],
                //'cookieFile'=>'',
                //'proxy'=>[],
                'method'=>'get',
                //'header'=>[],
            ]
        ],
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
        $this->httpClientInit();
        $this->createTable('content');
        $this->createTable('download');
    }

    /** ------------------------------------------------------------------
     * 实例化当前类，会对参数进行合法性检测，如果参数不合法会实例化失败
     * @param $option
     * @return string | \core\caiji\normal\Content : 参数合法性检测不通过 返回错误信息,否则返回实例化的当前类
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
    public function run()
    {
        $where1= (isset($this->option['where1']) && $this->option['where1']) ? $this->option['where1'] :  'where iscaiji=0 and isend=0 and caiji_name=\''.$this->option['name'].'\'';
        $where2=(isset($this->option['where2']) && $this->option['where2']) ? $this->option['where2'] :  [['iscaiji','eq',0],['caiji_name','eq',$this->option['name']],['isend','eq',0]];
        $this->doLoop([
            'sql'=>'select * from '.$this->prefix.$this->option['table'].' '.$where1,
            'params'=>[]
        ],function($v){
            if(!$v['url']){
                $this->errorCode[1]='url is empty.';
                $this->del($v['id']);
                return 1;
            }
            //采集
            //$this->downloadData=[];
            $this->debug([$v['url']],'.....上面为：目标网址',false);
            $tmp=$this->caiji($v);
            if(is_int($tmp)){
                $this->saveSpecial($v,$tmp);
                return $tmp;
            }
            $down=$this->addDownload($v['id']);
            if($down || $this->isHaveDownload){
                //$tmp=$down;
                $tmp['isdownload']=0;
            } else{
                $tmp['isdownload']=$this->option['isdownload'] ?? 1;
                $this->total['notdown']++;
            }
            //unset($down);
            $tmp['iscaiji']=1;
            $tmp['id']=$v['id'];
            $tmp['times']=$v['times'];
            $this->debug([$tmp],'.....上面为：最终入库数据',true);
            dump($tmp);
            //保存时是否使用指定插件
            if(isset($this->option['pluginSave']) && $this->option['pluginSave']){
                //使用指定插件
                $this->callback($this->option['pluginSave'],[$tmp]);
            }else{
                //使用默认保存
                $this->save($tmp);
            }
            return 0;
        },[
            'from'=>$this->option['table'],
            'where'=>$where2,
            'do'=>function($notDoCount){
                $data=[
                    'method_param'=>'',
                    'status'=>0,
                    'type'=>1,//0每天执行，1只执行一次
                    'run_time'=>time()
                ];
                //本次任务有已经采集 而且不用下载的项时，就要添加发布到队列
                /*if($this->total['notdown']>0){
                    $data['callback']='\core\caiji\normal\Fabu';
                    //固定发布，所有任务一样
                    $data['class_param']=$this->option['name'];
                    $this->addQueue($data,true);
                }*/
                //没有未完成的内容采集，就去除内容采集的队列
                if($notDoCount<=0){
                    $data['callback']='\core\caiji\normal\Content';
                    $data['class_param']=$this->option['name'];
                    //$this->model->_exec('update `'.$this->prefix.'caiji_queue` set status=1 where status=0 and name_md5=?',[md5($data['callback'].$data['class_param'].$data['method_param'])],false);
                }
                //本次任务有下载项时，就要添加下载采集到队列
                /*if($this->total['down']>0){
                    $data['callback']='\core\caiji\normal\Download';
                    $data['class_param']=$this->option['name'];
                    $this->addQueue($data,true);
                }*/
            }
        ]);
        return 0;
    }

    /** ------------------------------------------------------------------
     * caiji
     * @param array $v:数据中的单条记录
     * @return bool|array 需要update的项
     *---------------------------------------------------------------------*/
    protected function caiji($v){
        //$plugin=$this->option->plugin_single;
        if(isset($this->option['pluginBefore']) && $this->option['pluginBefore'])
            $v=$this->callback($this->option['pluginBefore'],[$v]);
        //curl 部分
        $html=$this->http($v['url']);
        $this->debug([$html],'远程html源码'.PHP_EOL,false);
        if(isset($this->option['pluginAfter']) && $this->option['pluginAfter'])
            $html=$this->callback($this->option['pluginAfter'],[$html]);
        if(is_int($html)){
            return $html;
        }
        $v=$this->query($html,$v['url'],$v);
        return $v;
    }

    protected function query($html,$url,$data)
    {
        $ret = [];
        //循环获取每个标签的值
        foreach ($this->option['caiji'] as $k => $item) {
            $item['from']=$item['from'] ?? 'html';
            switch ( $item['from']) {
                case 'html': //常规
                    $ret[$k]=$this->queryFromHtml($k,$item,$url,$html);
                    $this->debug([$ret[$k]],'--------------上面为标签“'.$k.'”未过滤前',false);
                    if($ret[$k]===''){
                        if(isset($item['notEmpty']) && $item['notEmpty']){
                            $this->errorCode[100]='Selector "'.$k.'" is empty! ';
                            return 100;
                        }
                    }
                    if(is_int($ret[$k])){
                        return $ret[$k];
                    }
                    //文件下载
                    if(isset($item['files']['type'])){
                        if($item['files']['type'] == 2){ //文件
                            //$this->downloadData[$k]=$ret[$k];
                            $ret[$k]=$this->getFileDownload($k,$ret[$k]);
                        }elseif ($item['files']['type'] == 1){ //图片
                            $ret[$k]=$this->getImgDownload($k,$ret[$k]);
                        }
                    }
                    break;
                case 'web'://从另一个网页
                    $webOption=explode('{%|||%}',$item['getUrl']);
                    if( isset($webOption[1]) && $webOption[1]){
                        switch ($webOption[0]){
                            case 'tags':
                                foreach ($data as $k1 => $v1){
                                    $webOption[1]=str_replace('{%'.$k1.'%}',$v1,$webOption[1]);
                                }
                                break;
                            case 'function':
                                $webOption[1]=$this->callback($webOption[1],[$html,$data,$url]);
                                break;
                        }
                        $html2=$this->http($webOption[1]);
                        $ret[$k]=$this->queryFromHtml($k,$item,$webOption[1],$html2);
                        unset($html2);
                        $this->debug([$ret[$k]],'--------------上面为标签“'.$k.'”未过滤前',false);
                        if($ret[$k]===''){
                            if(isset($item['notEmpty']) && $item['notEmpty']){
                                $this->errorCode[100]='Selector "'.$k.'" is empty! ';
                                return 100;
                            }
                        }
                        if(is_int($ret[$k])){
                            return $ret[$k];
                        }
                        //文件下载
                        if(isset($item['files']['type'])){
                            if($item['files']['type'] == 2){ //文件
                                //$this->downloadData[$k]=$ret[$k];
                                $ret[$k]=$this->getFileDownload($k,$ret[$k]);
                            }elseif ($item['files']['type'] == 1){ //图片
                                $ret[$k]=$this->getImgDownload($k,$ret[$k]);
                            }
                        }
                    }else{
                        if(isset($item['notEmpty']) && $item['notEmpty']){
                            $this->errorCode[109]=' from web getUrl的设置中,第二项不能为空 ';
                            return 109;
                        }else{
                            $ret[$k]='';
                        }
                    }
                    break;
                case 'page'://从page list获取
                    //$data[$k]=$data[$k] ?? '';
                    $ret[$k]=$data[$k] ?? '';
                    //文件下载
                    if($ret[$k] && isset($item['files']['type'])){
                        if($item['files']['type'] == 2){ //文件
                            $ret[$k]=$this->getFileDownload($k,$ret[$k]);
                        }elseif ($item['files']['type'] == 1){ //图片
                            $ret[$k]=$this->getImgDownload($k,$ret[$k]);
                        }
                    }
                    break;
                case 'url'://直接从url中获取
                    break;
                case 'tags'://从已有的标签中合成
                    break;
                case 'fixed'://固定一个值
                    $ret[$k]=$item['rule']['remark'];
                    break;
                case 'function'://从插件函数中获取
                    break;
            }
        }
        $this->debug([$ret],'......上面为：各标签采集后并经过过滤的结果集'.PHP_EOL,false);
        return $ret;
    }
    //quey from html时的函数
    protected function queryFromHtml($k,$item,$url,$html){
        if ($item['isMultipage']){ //开启多分页采集
            //$pages=Selector::findAll($html,$item['multiPage']['reg'],$item['multiPage']['tags'],$item['multiPage']['cut'],$item['multiPage']['type']);
            $pages=$this->selector($html,$item['multiPage']);
            if($pages){
                //确定第一页
                $this->getFirstLink($item['multiPage'],$url,$html);
                foreach ($pages as $page){
                    if(isset($this->pagination[$page['pageUrl']])){
                        continue;
                    }else{
                        $this->pagination[$page['pageUrl']]=(int)$page['pageNum'];
                    }
                }
                $this->debug([$this->pagination],'-----------------上面为起始分页'.PHP_EOL,false);
                $ret=$this->getMultiResult($item,$k);
            }else{
                $ret=$this->getResult($url,$item,$k,$html);
            }
        }else{
            $ret=$this->resultQuery($html,$item,$k,$url);
        }
        return $ret;
    }
    public function save($data){
        $id=$data['id'];
        unset($data['id']);
        if(isset($data['content'])){
            \shell\caiji\plugin\Save::checkMaxLength2($data['content']);
        }
        $this->model->eq('id',$id)->update($data);
    }

    /** ------------------------------------------------------------------
     * http查询：需要curl扩展
     * @param string $url 要访问的网址
     * @return int|string
     *---------------------------------------------------------------------*/
    public function http($url){
        $html=$this->httpClient->http($url,$this->option['http']['method']??'get',$this->option['http']['data']??[]);
        //$this->outPut($this->httpClient->getTipMsg(),false);
        if($html===false){
            $this->errorCode[2]='获取目标页面失败！';
            return 2;
        }
        if($html===''){
            $this->errorCode[4]='获取的目标页面为空！';
            return 4;
        }
        return $html;
    }


    /** ------------------------------------------------------------------
     * 获取分页第一条链接
     * @param array $multiPageOption
     * @param string $url
     * @param string $html
     *--------------------------------------------------------------------*/
    protected function getFirstLink($multiPageOption,$url,&$html){
        if($multiPageOption['firstPage']['type']=='current'){
            $firstLink=str_replace('{%current%}',$url,$multiPageOption['firstPage']['selector']);
            $this->pagination[$firstLink]=$multiPageOption['firstPage']['num'];
        }else{
            if(isset($multiPageOption['firstPage']['selector']) && $multiPageOption['firstPage']['selector'] ){
                //$firstLink=Selector::find($html,'regex,single',$multiPageOption['firstPage']['selector'],'firstLink','');
                $firstLink=$this->selector($html,$multiPageOption['firstPage']);
                $this->pagination[$firstLink]=$multiPageOption['firstPage']['num'];
            }
        }
    }
    /** ------------------------------------------------------------------
     * 获取分页链接
     * @param string $html
     * @param array $multiPageRule
     *---------------------------------------------------------------------*/
    protected function getLink(&$html,$multiPageRule){
        $pages=$this->selector($html,$multiPageRule);
        if($pages){
            foreach ($pages as $page){
                if(isset($this->pagination[$page['pageUrl']])){
                    continue;
                }else{
                    $this->pagination[$page['pageUrl']]=(int)$page['pageNum'];
                }
            }
        }
    }
    /** ------------------------------------------------------------------
     * 多分页结果获取
     * @param array $rule 当前标签对应的采集规则
     * @param string $tagName 当前标签名
     * @return string
     *--------------------------------------------------------------------*/
    protected function getMultiResult($rule,$tagName){
        $max=$rule['multiPage']['max'] ?? 0;
        $i=0;
        $j=(int)$rule['multiPage']['firstPage']['num'];
        $ret='';
        while ($max==0 || $i<$max){
            $link = array_search($j, $this->pagination, true);
            if ($link === false) {//只有所有分页链接访问完才会break
                break;
            }
            $this->pagination[$link] = true;
            $result = $this->getResult($link,$rule,$tagName);
            $i++;
            $j=$j+ $rule['multiPage']['increase'];
            if(is_string($result)){
                $ret.=$result;
            }
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 获取结果
     * @param string $url 网址
     * @param array $rule 该标签对应的采集规则
     * @param string $tagName  该标签名称
     * @param string $html
     * @return int|string
     *--------------------------------------------------------------------*/
    protected function getResult($url,$rule,$tagName,$html=''){
        //dump($html);
        $html=$html ? :$this->http($url);
        if(is_int($html))
            return $html;
        //dump($html);
        //获取分页链接
        if(isset($rule['isMultipage']) && $rule['isMultipage']){
            $this->getLink($html,$rule['multiPage']);
        }
        return $this->resultQuery($html,$rule,$tagName,$url);
    }

    /** ------------------------------------------------------------------
     * 结果筛选
     * @param string $html
     * @param array $item
     * @param string $tagName
     * @param string $url
     * @return int|string
     *--------------------------------------------------------------------*/
    public function resultQuery(&$html,$item,$tagName,$url){
        if(isset($this->option['pluginAfter']))
        $ret=$this->selector($html,$item['rule']);
        if($ret===false){
            $this->errorCode[3]= 'selector error:'.Selector::getError();
            return 3;
        }
        $isNotEmpty=((isset($item['notEmpty'])) && !empty($item['notEmpty']));
        //不为空检测
        if ($ret==='' || $ret===[] ){
            return $this->resultEmptyReturn($isNotEmpty,$tagName);
        }
        //$this->debug([$ret],'......上面为：过滤前 results',false);
        if( is_array($ret)){
            $ret=$this->resultFilter($ret,$item['filter'],$item['notEmpty'],$url);
            if($ret)
                $ret=$this->array2string($ret);
            else{
                return $this->resultEmptyReturn($isNotEmpty,$tagName);
            }
        }else{
            //过滤
            if (isset($item['filter']) && $item['filter']) {
                $ret=$this->filter($item['filter'],$ret,$url);
                //不为空检测（再检测一次）
                if ($ret==='' || $ret===[]){
                    return $this->resultEmptyReturn($isNotEmpty,$tagName);
                }
            }
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 结果为空时返回的内容
     * @param bool $isNotEmpty 当前标签规则是否允许为空
     * @param string $tagName 当前标签名
     * @return int|string
     *--------------------------------------------------------------------*/
    protected function resultEmptyReturn($isNotEmpty,$tagName){
        if($isNotEmpty){
            $this->errorCode[100]='After filter ,"'.$tagName.'" is empty';
            return 100;
        }else{
            return '';
        }
    }
    /** ------------------------------------------------------------------
     * 结果集过滤:按过滤规则过滤数据，过滤时同时检测是否有设置不能为空的标签，在结果集中去除该标签为空的项
     * @param array $results：结果集（二维数组或一维数组）
     * @param array $filter_options：各标签过滤规则集合
     * @param  array|bool $notEmpty:不能为空的标签集合
     * @param string $pageUrl
     * @return array|null 结果集过滤后，不为空返回过滤后的结果集，否则返回NULL
     *--------------------------------------------------------------------*/
    protected function resultFilter($results,$filter_options,$notEmpty,$pageUrl){
        if(!$filter_options)
            return $results;
        $ret=[];
        foreach ($results as $key =>$result){
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
        $this->debug([array_values($ret)],'---------上面为数组形式的经过滤后的分页采集结果集'.PHP_EOL,false);
        return $ret ? array_values($ret) :null;
    }

    /** ------------------------------------------------------------------
     * 数组转换为字符串
     * @param array $array
     * @return string
     *---------------------------------------------------------------------*/
    public static function array2string($array){
        $ret='';
        $count=count($array);
        $i=0;
        foreach ($array as $v){
            if(is_array($v)){
                $count1=count($v);
                $j=0;
                foreach ($v as $v1){
                    $ret.=$v1;
                    if($j<($count1-1)){
                        $ret.='{%||%}';
                    }
                    $j++;
                }
            }else{
                $ret.=$v;
            }
            if($i<($count-1)){
                $ret.='{%|||%}';
            }
            $i++;
        }
        return $ret;
    }
    //添加数据到 caiji_download
    public function addDownload($id){
        if(empty($this->downloadData))
            return false;
        $this->debug([$this->downloadData],'.....上面为：downloadData',false);
        //1、下载项分离重复的
        $data_download=Helper::array_delet_repeat($this->downloadData);
        $this->downloadData=[];
        //2、合并
        $data_download=$this->downMerge($data_download);
        //3、不相同项保存到下载表
        $this->model->table=$this->option['downloadTable'];
        foreach ($data_download['unique'] as $k => $v){
            $num=strrchr($k,':');
            if($num!==false){
                $tag=str_replace($num,'',$k);
                $num=ltrim($num,':');
            }else{
                $tag=$k;
                $num='';
            }
            $replace_path=$this->option['caiji'] [$tag]['files']['replace_path'] ?? '';
            //不是测试时才入库
            if(! $this->debug){
                if(is_array($v)){
                    $true_url=$v[0];
                    $source_url=$v[1];//'aaa:0{%|||%}aaa:1{%|||%}bbb'
                }else{
                    $true_url=$v;
                    $source_url='';
                }
                if($this->model->insert([
                        'source_url'=>$source_url,
                        'true_url'=>$true_url,
                        'replace_path'=>$replace_path,
                        //'save_path'=>trim($this->downloadOption['save_path'],'/').'/'.$replace_path,
                        'type'=>$tag.($num ? ':'.$num : ''),
                        'cid'=>$id,
                        //'file_type'=>$this->option['caiji'][$tag]['files']['type'],
                    ]) >0 ){
                    //下载项计数+1
                    $this->total['down']++;
                }
            }
        }
        $this->model->reset()->table=$this->option['table'];
        return true;
    }
    /** ------------------------------------------------------------------
     * 整合下载项
     * @param array $down
     * @return array
     *--------------------------------------------------------------------*/
    protected function downMerge($down){
        foreach ($down['unique'] as $k1 =>$v1){
            $tag=[];
            foreach ($down['change'] as  $k2=>$v2){
                if($k1===$v2){
                    $tag[]=$k2;
                }
            }
            if($tag){
                $down['unique'][$k1]=[$v1,implode('{%|||%}',$tag)];
            }
        }
        return $down;
    }

    /** ------------------------------------------------------------------
     * 替换文件为本地文件路径，替换图片占位符为本地路径
     * @param array $down
     * @param array $data
     * @return array 返回替换后的结果集
     *--------------------------------------------------------------------*/
    protected function downReplace($down,$data){
        foreach ($data as $k=>$v){
            if(isset($down[$k])){
                if(! is_array($down[$k])){
                    $data[$k]=$down[$k];
                }else{
                    $search=[];
                    $replace=[];
                    foreach ($down[$k] as $key =>$item ){
                        $search[]='{%img'.$key.'img%}';
                        $replace[]=$item;
                    }
                    $data[$k]=str_replace($search,$replace,$v);
                }
            }
        }
        return $data;
    }
    /** ------------------------------------------------------------------
     * 添加要下载的图片到 $this->downloadData中，同时把标签内容中的图片换成特殊的占位符
     * @param string $tag
     * @param string $content
     * @return string
     *--------------------------------------------------------------------*/
    public function getImgDownload($tag,$content){
        if(!$content)
            return $content;
        $reg='#<img ([^<>]*?)src=([\'"])([^"\']*)\2([^>]*)>#i';
        $i=1;
        $content= preg_replace_callback($reg,function($match)use($tag,&$i){
            $ret='';
            if($match[3]){
                $this->downloadData[$tag.':'.$i]=$match[3];
                $ret='{%@'.$tag.':'.$i.'@%}';
                $i++;
            }
            return '<img '.$match[1].'src="'.$ret.'"'.$match[4].'>';
        },$content);
        return $content;
    }
    /** ------------------------------------------------------------------
     * 添加要下载的文件到 $this->downloadData中
     * @param string $tag
     * @param string $content
     *--------------------------------------------------------------------*/
    public function getFileDownload($tag,$content){
        if(checkIsEmpty($content))
            return '';
        $arr=explode('{%|||%}',$content);
        $i=1;
        foreach ($arr as $item){
            $this->downloadData[$tag.':'.$i]=$item;
            $content=str_replace($item,'{%@'.$tag.':'.$i.'@%}',$content);
            $i++;
        }
        return $content;
    }
    /** ------------------------------------------------------------------
     * 删除某个表中某项
     * @param $id
     *---------------------------------------------------------------------*/
    protected function del($id){
        $this->model->eq('id',$id)->delete();
        //$this->model->_exec('delete FROM `'.$this->prefix.'tag_relation` WHERE tid = ? and type= ?',[$id,'weixinqun'],false);
    }

    //特殊保存
    private function saveSpecial($data,$code){
        switch ($code){
            case 100:
                $isend=($data['times'] >1) ? 1 : 0;
                $this->errorCode[100]=$this->errorCode[100] ?? '404错误！';
                $update=[
                    'update_time'=>time(),
                    'times'=>($data['times']+1),
                    'iscaiji'=>1,
                    'isend'=>$isend,
                ];
                break;
            case 101://ip被封
                $this->errorCode[101]=$this->errorCode[101] ?? 'ip被封';
                $update=[
                    'update_time'=>time(),
                    'times'=>($data['times']+1),
                    'iscaiji'=>1,
                    'isend'=>0,
                ];
                break;
            case 102:
                $this->errorCode[102]=$this->errorCode[102] ?? '获取不到结果';
                $update=[
                    'update_time'=>time(),
                    'times'=>0,
                    'iscaiji'=>1,
                    'isend'=>1,
                    'is_hide'=>1,
                ];
                break;
            default:
                return;
        }
        $this->model->eq('id',$data['id'])->update($update);
    }

    /** ------------------------------------------------------------------
     * 测试
     * @param string $url
     *--------------------------------------------------------------------*/
    public function doTest($data){
        $this->debug=true;
        $this->caiji($data);
        //dump($aa);
    }

    /** ------------------------------------------------------------------
     * 选择器规则测试
     * @param $url
     *---------------------------------------------------------------------*/
    public function qureyTest($url){
        $html=$this->http($url);
        $ret=[];
        foreach ($this->option['caiji'] as $key=>$item){
            $ret[$key]=$this->selector($html,$item['rule']);
        }
        dump($ret);
    }

}
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


use core\Conf;

class Fabu extends Base
{
    protected  $option=[
        'runMax'=>2,//每次发布的条数，0表示不限制
        'plug'=>'',//插件
        'target'=>'', //发布的目标
        'where1'=>'',
        'where2'=>[],
        //不需要发布的字段
        'fieldsFilter'=>['iscaiji','isend','isfabu','isdownload','islaji','isdone','isshenhe','times','url','caiji_name'],
    ];
    /**
     * Fabu constructor:构造函数，载入公共初始化项的同时，进行任务初始化
     * @param $option
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
        //设置需要输出的字段
        $this->setFieldsOption();
        //curl初始化
        //$this->curlInit();
        $this->httpClientInit();
    }

    /** ------------------------------------------------------------------
     * 设置过滤字段
     * 处理$this->option['fields']中的每一项，如果在$this->option['fieldsFilter']中存在就去掉，不存在的就加入
     *---------------------------------------------------------------------*/
    protected function setFieldsOption(){
        if(isset($this->option['fields']) && $this->option['fields']){
            $opt=explode(',',$this->option['fields']);
            $isUnset=false;
            foreach ($opt as $item){
                $key=array_search($item,$this->option['fieldsFilter'],true);
                if($key===false){
                    $this->option['fieldsFilter'][]=$item;
                }else{
                    unset($this->option['fieldsFilter'][$key]);
                    if(!$isUnset)
                        $isUnset=true;
                }
            }
            if($isUnset)
                $this->option['fieldsFilter']=array_values($this->option['fieldsFilter']);
        }
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

    public function run(){
        $fields=$this->getFields();
        $where1= (isset($this->option['where1']) && $this->option['where1']) ? $this->option['where1'] : 'where isfabu=0 and iscaiji=1 and isdownload=1 and isend=1';
        $where2=(isset($this->option['where2']) && $this->option['where2']) ? $this->option['where2'] :  [['isfabu','eq',0],['iscaiji','eq',1],['isdownload','eq',1],['isend','eq',1]];
        $this->doLoop([
            'sql'=>'select '.$fields.' from '.$this->prefix.$this->model->table.' '.$where1,
            'params'=>[]
        ],function($data){
            if(isset($this->option['plug'])&&$this->option['plug'] ){//使用插件
                return $this->callback($this->option['plug'],[$data,$this->option,$this->curl]);
            }else{//不使用
                //$html=$this->curl->post($this->option['target'],$data);
                $html=$this->httpClient->http($this->option['target'],'post',$data);
                if($html===false){
                    $this->errorCode[1]='curl false:message<<<'.$this->curl->errorMsg.'>>>';
                    return 1;
                }elseif($html=='发布成功'){
                    $ret=$this->model->eq('id',$data['id'])->update(['isfabu'=>1]);
                    if($ret>0)
                        return 0;
                    else{
                        $this->errorCode[1]='更新数据失败';
                        return 1;
                    }
                }else{
                    $this->errorCode[2]='发布失败，返回信息：'.$html;
                    if($this->debug){
                        echo $this->errorCode[2];
                        exit();
                    }
                    return 2;
                }
            }
        },[
            'from'=>$this->option['table'],
            'where'=>$where2,
            'do'=>function($notDoCount){
                //没有未完成的发布任务，就要把发布任务从队列中去除,否则会保留
                if($notDoCount<=0){
                    $name_md5=md5('\core\caiji\normal\Fabu'.$this->option['name']);
                    $this->model->_exec('update `'.$this->prefix.'caiji_queue` set status=1 where status=0 and name_md5=?',[$name_md5],false);
                }
            },
        ]);
    }

    /** ------------------------------------------------------------------
     * 获取字段名
     * @return string
     *---------------------------------------------------------------------*/
    protected function getFields(){
        $dbName=Conf::get('database_name','database');
        $sql='select COLUMN_NAME as name from INFORMATION_SCHEMA.Columns where table_name=\''.$this->prefix.$this->option['table'].'\' and table_schema=\''.$dbName.'\'';
        $result=$this->model->_sql($sql,[],false,false);
        if(!$result)
            return '';
        $ret=[];
        foreach ($result as $item){
            if(! in_array($item['name'],$this->option['fieldsFilter'])){
                $ret[]=$item['name'];
            }
        }
        return implode(',',$ret);
    }

    /** ------------------------------------------------------------------
     * 提交数据到远程主机，并根据结果作相应处理
     * @param array $data
     * @return int
     *---------------------------------------------------------------------*/
    protected function post($data){
        $html=$this->curl->post($this->option['target'],$data);
        if($html===false){
            $this->errorCode[1]='curl false:message<<<'.$this->curl->errorMsg.'>>>';
            return 1;
        }else{
            $html=$this->curl->encoding($html);
            //dump($html);
            if($html=='发布成功'){
                if(!$this->debug){
                    $ret=$this->model->eq('id',$data['id'])->update(['isfabu'=>1]);
                    if($ret<=0){
                        $this->errorCode[2]='更新数据失败';
                        return 2;
                    }
                }
                return 0;
            }else{
                $this->errorCode[3]='发布失败，返回信息：'.$html;
                return 3;
            }
        }
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
            echo '  发布成功'.PHP_EOL;
    }

}
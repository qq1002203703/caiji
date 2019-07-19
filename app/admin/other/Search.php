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


namespace app\admin\other;

class Search
{
    protected $sphinx;
    public $maxMun=500;
    public $host='127.0.0.1';
    public $port='9312';
    public $filters=[];
    public $mode; //默认SPH_MATCH_ALL
    public function __construct($opt=[],$filters=[])
    {
        require_once ROOT.'/sphinxapi.php';
        $this->sphinx=new \SphinxClient ();
        if(isset($opt['maxNum']))
            $this->maxMun=$opt['maxNum'];
        if(isset($opt['host']))
            $this->host=$opt['host'];
        if(isset($opt['port']))
            $this->host=$opt['port'];
        $this->mode=SPH_MATCH_ALL;
        $this->filters=$filters;
    }

    protected function _init(){
        $this->sphinx->SetServer ( $this->host, $this->port);
        //$cl->SetIndexWeights(['bbs'=>10,'portal'=>100]);
        if($this->filters){
            foreach ($this->filters as $filter){
                $this->filter($filter);
            }
        }
    }

    protected function filter($filter){
        $type=[
            'id_between'=>'setIDRange',//id取值范围,接受参数 ( $min, $max )
            'attr_in'=>'SetFilter',//属性为某些整数值 ( $attribute, $values, $exclude=false )
            'attr_between'=>'SetFilterRange', //属性必须是某个整数范围内  ( $attribute, $min, $max, $exclude=false )
            'float_between'=>'SetFilterFloatRange',//属性必须是某个小数范围内 ( $attribute, $min, $max, $exclude=false )
            'string'=>'SetFilterString',//属性必须和某个字符串值相等  ( $attribute, $value, $exclude=false )
        ];
        //dump($filter[0]);
        if(! in_array($filter[0],array_keys($type)))
            return false;
        $func=$type[array_shift($filter)];
        return call_user_func_array([&$this->sphinx,$func],$filter);
    }

    protected function limit($currentPage,$pageSize){
        $this->sphinx->SetLimits(($currentPage - 1) * $pageSize , $pageSize , $this->maxMun);
    }

    public function setSortMode ($mode, $attrName='' ){
        $this->sphinx->SetSortMode ($mode,$attrName);
    }

    public function setFilter($filterType, ...$params){

    }

    /** ------------------------------------------------------------------
     * 查询
     * @param string $keyword
     * @param string $indexName
     * @param int $currentPage
     * @param int $pageSize
     * @param int $count
     * @return array
     *--------------------------------------------------------------------*/
    public function query($keyword,$indexName,$currentPage,$pageSize,&$count=0,$func=null){
        $this->_init();
        $this->limit($currentPage,$pageSize);
        $this->sphinx->_mode=$this->mode;
        $this->sphinx->SetMaxQueryTime(3000);
        $this->sphinx->SetConnectTimeout (1);
        $this->sphinx->SetArrayResult (true);
        $res = $this->sphinx->Query ( $keyword, $indexName);
        $ret=[];
        if(empty($res) || $res['total_found'] == 0) {
            $count = 0;
            $ret= array();
        } else {
            $count = ($res['total_found'] < $this->maxMun ? $res['total_found'] : $this->maxMun);
            $tmp = $res['matches'];
            unset($res);
            array_walk($tmp,function ($value,$key)use (&$ret,$func){
                $ret[$key]['id']=$value['id'];
                $ret[$key]['weight']=$value['weight'];
                $ret[$key]=array_merge($value['attrs'],$ret[$key]);
                if(isset($ret[$key]["attr_title"])){
                    $ret[$key]['title']=$ret[$key]["attr_title"];
                    unset($ret[$key]["attr_title"]);
                }
                if(isset($ret[$key]["attr_content"])){
                    $ret[$key]['content']=$ret[$key]["attr_content"];
                    unset($ret[$key]["attr_content"]);
                }
                if($func){
                    $other=call_user_func($func,$ret[$key],$key);
                    if($other)
                        $ret[$key]=array_merge($ret[$key],$other);
                }

            });
            unset($tmp);
        }
        return $ret;
    }
}
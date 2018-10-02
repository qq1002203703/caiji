<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/


namespace app\portal\model;
use core\Model;
use core\lib\cache\File;

class Citys extends Model
{
    use \app\common\model\CategoryCommon;
    public $table='citys';
    public $primaryKey='id';

    /** ------------------------------------------------------------------
     * 把数组形式的树结构用json格式缓存起来
     * @param string|array $select:要输出的字段名
     * @param string $fileName:缓存的文件名（不包括完整路径），相对于ROOT.'/public/static/lib/citys/'
     * @param string $type
     * @param array $where where条件查询
     *---------------------------------------------------------------------*/
    public function jsonCache($select,$fileName,$type='',$where=[]){
        switch ($type){
            case 'easybui':
                $data=$this->citysArrEasybui(null,$where);
                break;
            default:
                $data=$this->citysArrRecursion(null,$select,$where);
        }
        File::write(ROOT.'/public/static/lib/citys/'.$fileName,'var cityData='.json_encode($data,JSON_UNESCAPED_UNICODE).';');
    }

    /** ------------------------------------------------------------------
     * 按一定结构要求，递归输出省市的数组形式的树结构
     * @param null|\core\lib\tree\Node $node
     * @param string|array $select:要输出的字段名
     * @param array $where where条件查询
     * @return array
     *--------------------------------------------------------------------*/
    protected function citysArrRecursion($node=null,$select='id,name,pid',$where=[]){
        /*$nodes=\is_object($node) ? $node->getChildren(): $this->newTree($this->getAll(true,[['level','get',0]]),['rootId' => 100000, 'id' => 'id', 'parent' => 'pid'])->getRootNodes();*/
        if(is_object($node))
            $nodes=$node->getChildren();
        else{
            $nodes=$this->newTree($where? $this->_where($where)->gt('level',0)->findAll(true) : $this->getAll(true,[['level','get',0]]),['rootId' => 100000, 'id' => 'id', 'parent' => 'pid'])->getRootNodes();
        }
        $arr=[];
        if(is_string($select))
            $select=explode(',',$select);
        foreach($nodes as $k=>$v){
            foreach ($select as $item){
                $arr[$k][$item]=$v->get($item);
            }
            if($v->hasChildren()){
                switch ($v->get('level')){
                    case 1:
                        $arr[$k]['city']=$this->citysArrRecursion($v,$select);
                        break;
                    case 2:
                        $arr[$k]['area']=$this->citysArrRecursion($v,$select);
                        break;
                    default:
                        continue;
                }
            }
        }
        return $arr;
    }
    /** ------------------------------------------------------------------
     * 按easybui的要求，递归输出省市的数组形式的树结构
     * @param null|\core\lib\tree\Node $node
     * @return array
     *--------------------------------------------------------------------*/
    public function citysArrEasybui($node=null,$where=[]){
        /*$nodes=\is_object($node) ? $node->getChildren(): $this->newTree($this->getAll(true,[['level','get',0]]),['rootId' => 100000, 'id' => 'id', 'parent' => 'pid'])->getRootNodes();*/
        if(is_object($node))
            $nodes=$node->getChildren();
        else{
            $nodes=$this->newTree($where? $this->_where($where)->gt('level',0)->findAll(true) : $this->getAll(true,[['level','get',0]]),['rootId' => 100000, 'id' => 'id', 'parent' => 'pid'])->getRootNodes();
        }
        $arr=[];
        foreach($nodes as $k=>$v){
            $level=$v->get('level');
            $arr[$k]['name']=$v->get('name');
            if($v->hasChildren()){
                switch ($level){
                    case '1':
                        $arr[$k]['city']=$this->citysArrEasybui($v);
                        break;
                    case '2':
                        $children=$v->getChildren();
                        foreach ($children as $child){
                            $arr[$k]['area'][]=$child->get('name');
                        }
                        break;
                    default:
                        continue;
                }
            }
        }
        return $arr;
    }


}
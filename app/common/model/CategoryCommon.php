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
 * 被有分类特性（树结构）那样的model共用
 * ======================================*/
namespace app\common\model;

trait CategoryCommon
{
    /**------------------------------------------------------------------
     * 检查一个名字是否合法：一般用于入库前的验证
     * @param $name
     * @param $id
     * @return bool 数据库中已经存在返回false,不存在返回true
     *---------------------------------------------------------------------*/
    public function checkName($name,$id){
        //编辑时排除自身的name
        $this->reset();
        if($id) $this->ne('id',$id);
        return ($this->eq('name',$name)  ->find()===false)?true:false;
    }

    /** ------------------------------------------------------------------
     * 检测pid是否合法：一般用于入库前的验证
     * @param $pid
     * @param $id
     * @return bool pid为0时直接返回true,数据库存在时返回true,不存在时返回false
     *---------------------------------------------------------------------*/
    public function checkPid($pid,$id){
        if($pid==0) return true;
        $this->reset();
        if($id) {
            //编辑分类时排除子孙分类的id作父id
            $current=$this->getById($id);
            $this->notlike('path', $current['path']);
        }
        return ($this->eq('id',$pid)  ->find()===false) ? false : true;
    }
    /**
     * 检查分类别名在数据库中是否已经存在
     * @param $slug string
     * @return bool：存在返回false,不存在返回true
     */
    public function checkSlug($slug){
        $id=get('id') ? get('id') :post('id');
        //编辑分类时排除自身的slug
        if($id) $this->ne('id',$id);
        return ($this->eq('slug',$slug)  ->find()===false)?true:false;
    }
    /**
     * 分类别名slug生成
     * @param $slug
     * @param string $catname
     * @return string
     */
    protected function getSlug($slug,$catname=''){
        if (!$slug)
            $slug=\extend\PinYin::get($catname);
        if($this->checkSlug($slug)){
            return $slug;
        }else{
            $ret='';
            for($i=1;$i<1000;$i++){
                if($this->checkSlug($slug.$i)){
                    $ret=$slug.$i;
                    break;
                }
            }
        }
        if($ret==''){
            $ret=$slug . uniqid() ;
        }
        return $ret;
    }

    /**
     * 查询所有原数据
     * @param bool $is_cache：是否要从缓存文件中读取数据
     * @param array $where
     * @return array|bool
     */
    public function getAll($is_cache=true,$where=array()){
        $result=[];
        if($is_cache){
            $result=$this->cache();
        }
        if(empty($result)) {
            $result = $this->_where($where)->findAll(true);
            $result && $this->cache($result);
        }
        return $result;
    }

    /**
     * 把原数据缓存到文件中
     * @param null|mixed $data：要写入缓存文件的数据，如果不提供或null时表示是要读取缓存
     * @return void|array
     */
    public function cache($data=null){
        $cache=app('cache',[['path'=>ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'database']]);
        if($data===null){
            return $cache->get('tree_'.$this->table);
        }
        $cache->set('tree_'.$this->table,$data,0);
    }
    /** ------------------------------------------------------------------
     * 更新缓存
     *---------------------------------------------------------------------*/
    public function updateCache(){
        $this->cache($this->getAll(false));
    }

    /**
     * 通过id获取对应节点的数据
     * @param int $id
     * @return array
     */
    public function getById($id){
        return $this->getNodeById($id)->toArray();
    }

    /** ------------------------------------------------------------------
     * 实例化树类
     * @return \core\Tree|object
     *---------------------------------------------------------------------*/
    public function newTree($data=array(),$option=[]){
        if(!$data)
            $data=$this->getAll();
        if(empty($option))
            $option=['rootId' => 0, 'id' => 'id', 'parent' => 'pid'];
        return \core\Container::get('tree',[$data,$option]);
    }

    /** ------------------------------------------------------------------
     * 获取一个子节点
     * @param int $id
     * @return \core\lib\tree\Node
     *---------------------------------------------------------------------*/
    protected function getNodeById($id){
        return $this->newTree()->getNodeById($id);
    }

    /** ------------------------------------------------------------------
     * 获取层级关系路径
     * @param $id
     * @param $pid
     * @return string
     *---------------------------------------------------------------------*/
    public function getPath($id,$pid){
        if($pid==0) return $id;
        $parent=$this->getById($pid);
        return $parent['path'].'-'.$id;
    }

    /** ------------------------------------------------------------------
     * 获取层级
     * @param $pid
     * @return int
     *---------------------------------------------------------------------*/
    public function getLevel($pid){
        if($pid==0) return 1;
        $parent=$this->getById($pid);
        return $parent['level']+1;
    }
    /**-------------------------------------------------------------------------------
     * 按一定格式输出树型结构的字符串形式
     * @param array $output:需要输出的字段名称,如 ['id','title']
     * @param string $format:对应$output中的字段名的格式化输出，还可以加上'%select%'用来被后面的$select['text']格式化替换,如：'<option title="%name%" value="%id%"%select%>%__repeat_content__%</option>'
     * @param array $select :选中项，数组中要包含两个值，选中id和选中时的文本输出text,如['id'=>1,'text'=>' selected="selected"']
     * @param int|string $id :节点id，如果提供的是0,将得到全部的树型结构，否则得到对应节点id的树型结构
     * @param bool $is_self:是否包括自身，id不是0时，如果本项为true时，则返回自身和它包含的后代分类，为false只返回它的后代分类
     * @param string $repeat_name
     * @return string
     *--------------------------------------------------------------------------------*/
    public function getTree($output,$format,$select='',$id=0,$is_self=true,$repeat_name='name'){
        $tree=$this->newTree();
        return $tree->getTree($id,$is_self,$repeat_name,$output,$format,$select);
    }

    /** ------------------------------------------------------------------
     * 获取顶层的节点
     * @param string|int $id:排除的节点id
     * @return array：二维数组
     *--------------------------------------------------------------------*/
    public function getTop($id=''){
        if($id) {
            $ret=$this->ne('id',$id)->eq('pid',0)->findAll(true);
        }else
            $ret=$this->newTree()->getRootNodes(true);
        return $ret;
    }

    /**
     * 判断一个分类是否有子分类
     * @param int $id
     * @return bool
     */
    public function hasChildren($id){
        return $this->getNodeById($id)->hasChildren();
    }

    /**
     * 获取某个节点的子孙节点
     * @param  int $id:节点id
     * @param bool $is_all : true时全部后代获取，false时只获取子分类
     * @param bool $is_self ：是否包括自己
     * @return array|\core\lib\tree\Node[]
     */
    public function getChildren($id,$is_all,$is_self=false){
        $node=$this->getNodeById($id);
        if($is_all){
            if($is_self)
                return $node->getDescendantsAndSelf();
            else
                return $node->getDescendants();
        }else{
            return $node->getChildren();
        }
    }

    /** ------------------------------------------------------------------
     * 节点改变后，同步更改子孙节点的path和level值
     * @param array $new_data:当前节点的新数据
     *--------------------------------------------------------------------*/
    public function childrenChange($new_data,$old_data){
        if($new_data['pid'] ==$old_data['pid']) return ;
        $data=$this->getChildren($new_data['id'],true);
        if($data){
            foreach ($data as $value){
                $this->reset();
                $this->update([
                    'path'=>\extend\Helper::str_replace_once($old_data['path'],$new_data['path'],$value->get('path')),
                    'level'=>$value->get('level')+($new_data['level']-$old_data['level']),
                    'id'=>$value->get('id')
                ]);
            }
        }
    }

}
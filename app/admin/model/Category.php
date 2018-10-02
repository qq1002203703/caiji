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

namespace app\admin\model;

class Category extends \core\Model
{
    //use \app\common\model\CategoryCommon;
    //use \app\common\model\RecycleCommon;
    /**
     * @var string 表名
     */
    public $table='category';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';
    /**
     * @var string 分类种类
     */
    public $type='weixinqun';

    /**------------------------------------------------------------------
     * 检查一个名字是否合法：用于入库前的验证
     * @param  string $name
     * @param string $type
     * @param int $id
     * @return bool 数据库中已经存在返回false,不存在返回true
     *---------------------------------------------------------------------*/
    public function checkName($name,$id=0){
        //编辑时排除自身的name
        $this->reset();
        if($id) $this->ne('id',$id);
        return ($this->eq('name',$name) ->eq('type',$this->type) ->find()===false)?true:false;
    }
    /** ------------------------------------------------------------------
     * 检测pid是否合法：一般用于入库前的验证
     * @param $pid
     * @param $id
     * @return bool pid为0时直接返回true,数据库存在时返回true,不存在时返回false
     *---------------------------------------------------------------------*/
    public function checkPid($pid,$id=0){
        if($pid==0) return true;
        $this->reset();
        if($id) {
            //编辑分类时排除子孙分类的id作父id
            $current=$this->getById($id);
            $this->notlike('path', $current['path']);
        }
        return ($this->eq('id',$pid) ->eq('type',$this->type) ->find()===false) ? false : true;
    }
    /**
     * 检查分类别名在数据库中是否已经存在
     * @param $slug string
     * @return bool：存在返回false,不存在返回true
     */
    public function checkSlug($slug,$id=0){
        //$id=get('id') ? get('id') :post('id');
        //编辑分类时排除自身的slug
        if($id) $this->ne('id',$id);
        return ($this->eq('slug',$slug)->eq('type',$this->type)  ->find()===false)?true:false;
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
            $result = $this->_where($where)->eq('type',$this->type)->findAll(true);
            $this->cache($result);
        }
        return $result;
    }

    /**
     * 从缓存文件读取数据，或把数据缓存到文件中
     * @param null|mixed $data：要写入缓存文件的数据，如果不提供或null时表示是要读取缓存
     * @return void|array：缓存时没有返回值，
     */
    public function cache($data=null){
        $cache=app('cache',[['path'=>ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'database']]);
        if(!$data){
            return $cache->get('tree_'.$this->table.'_'.$this->type);
        }
        $cache->set('tree_'.$this->table.'_'.$this->type,$data,0);
    }
    /** ------------------------------------------------------------------
     * 更新缓存
     * @param array $where
     *---------------------------------------------------------------------*/
    public function updateCache($where=array()){
        $this->cache($this->getAll(false,$where));
    }

    /** ------------------------------------------------------------------
     * 实例化树类
     * @param array $data
     * @param array $option
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

    /**
     * 通过id获取对应节点的数据
     * @param int $id
     * @return array
     */
    public function getById($id){
        return $this->getNodeById($id)->toArray();
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
    public function getTree($output,$format,$select=array(),$id=0,$is_self=true,$repeat_name='name'){
        $tree=$this->newTree();
        return $tree->getTree($id,$is_self,$repeat_name,$output,$format,$select);
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
     * @param array $old_data:当前节点的旧数据
     *--------------------------------------------------------------------*/
    public function childrenChange($new_data,$old_data){
        if($new_data['pid'] ==$old_data['pid']) return ;
        //获取所有子孙节点
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
    /** ------------------------------------------------------------------
     * 获取顶层的节点
     * @param string|int $id:排除的节点id
     * @param array $where
     * @return array：二维数组
     *--------------------------------------------------------------------*/
    public function getTop($id='',$where=array()){
        if($id) {
            $ret=$this->_where($where)->ne('id',$id)->eq('pid',0)->eq('type',$this->type)->findAll(true);
        }else
            $ret=$this->newTree()->getRootNodes(true);
        return $ret;
    }
/****************************************************************************************************
*上面为比较通过,下面一般比较独特
******************************************************************************************************/
    /**
     * 添加分类
     * @param $data:已经验证过的数据
     * @param array $where
     * @return bool|int:添加成功返回插入id,失败返回false
     */
    public function add($data,$where=array()){
        $data['slug']= $data['slug'] ?? '';
        $data['slug']=$this->getSlug($data['slug'],$data['name']);
        $data['level']=$this->getLevel($data['pid']);
        $id=$this->insert($data);
        if($id){
            //更新path
            $path=$this->getPath($id,$data['pid']);
            if($path !== '')
                $this->update(['path'=>$path,'id'=>$id]);
            $this->reset();
            $this->updateCache($where);
            $this->cache($this->findAll(true));
            return $id;
        }
        return false;
    }
    /**
     * 通过id删除一个分类
     * @param $id
     * @param array $where
     * @return bool
     */
    public function del($id,$where=array()){
        if($this->eq('id',$id)->delete()){
            $this->updateCache($where);
            return true;
        }
        return false;
    }

}
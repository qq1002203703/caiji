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

namespace app\admin\model;
use core\Model;
use extend\PinYin;
use extend\Helper;

/**
 * Class Category
 * @package app\admin\model
 * @method string getTreeSelect();
 */
class Category extends Model
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
    protected $type;

    /** -----------------------------------------------------------------
     * 设置种类
     * @param string $type
     * @return static $this
     *--------------------------------------------------------------------*/
    public function setType($type){
        $this->type=$type;
        return $this;
    }
    /**------------------------------------------------------------------
     * 检查一个名字是否合法：用于入库前的验证
     * @param  string $name
     * @param int $id
     * @return bool 数据库中已经存在返回false,不存在返回true
     *---------------------------------------------------------------------*/
    public function checkName($name,$id=0){
        //编辑时排除自身的name
        $this->reset();
        if($id) $this->ne('id',$id);
        $ret=$this->eq('name',$name) ->eq('type',$this->type) ->find();
        return ($ret===false)?true:false;
    }
    /** ------------------------------------------------------------------
     * 检测pid是否合法：一般用于入库前的验证
     * @param int $pid
     * @param int $id
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
     * @param string $slug
     * @param int $id
     * @return bool 存在返回false,不存在返回true
     */
    public function checkSlug($slug,$id=0){
        //$id=get('id') ? get('id') :post('id');
        //编辑分类时排除自身的slug
        if($id) $this->ne('id',$id);
        return ($this->eq('slug',$slug)->eq('type',$this->type) ->find(null,true) == false);
    }

    /**
     * 分类别名slug生成
     * @param string $slug
     * @param string $catname
     * @return string
     */
    protected function getSlug($slug,$catname=''){
        if (!$slug)
            $slug=PinYin::get($catname,'all','');
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
     * @param int $id
     * @param int $pid
     * @return string
     *---------------------------------------------------------------------*/
    public function getPath($id,$pid){
        if($pid==0) return $id;
        $parent=$this->getById($pid);
        return $parent['path'].'-'.$id;
    }
    /** ------------------------------------------------------------------
     * 获取层级
     * @param int $pid
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
     * @return bool|array
     */
    public function cache($data=null){
        $cache=app('cache',[['path'=>ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'database']]);
        if($data===null){
            return $cache->get('tree_'.$this->table.'_'.$this->type);
        }
        $cache->set('tree_'.$this->table.'_'.$this->type,$data,0);
        return true;
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
     * @param string $repeat_name 需要重复的字段名称，这个字段的值用来替换$format里的%__repeat_content__%
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
        $data=$this->getChildren($old_data['id'],true);
        if($data){
            foreach ($data as $value){
                $this->reset();
                $this->eq('id',$value->get('id'))->update([
                    'path'=>Helper::str_replace_once($old_data['path'],$new_data['path'],$value->get('path')),
                    'level'=>$value->get('level')+($new_data['level']-$old_data['level']),
                ]);
            }
        }
    }
    /** ------------------------------------------------------------------
     * 获取顶层的节点
     * @param string|int $id:排除的节点id
     * @param array $where
     * @return array 二维数组
     *--------------------------------------------------------------------*/
    public function getTop($id='',$where=array()){
        if($id) {
            $ret=$this->_where($where)->ne('id',$id)->eq('pid',0)->eq('type',$this->type)->findAll(true);
        }else
            $ret=$this->newTree()->getRootNodes(true);
        return $ret;
    }
/****************************************************************************************************
*上面为比较通用的部分,下面一般比较独特
******************************************************************************************************/
    /**
     * 添加分类
     * @param $data:已经验证过的数据
     * @param array $where
     * @return bool|int:添加成功返回插入id,失败返回false
     */
    public function add($data,$where=array()){
        //缩略图处理
        if(isset($data['images_url']) && $data['images_url']){
            $tmp=explode('_',$this->type);
            File::thumb($data,$tmp[0]);
        }
        $data['slug']= $data['slug'] ?? '';
        $data['slug']=$this->getSlug($data['slug'],$data['name']);
        $data['level']=$this->getLevel($data['pid']);
        $data=$this->_filterData($data);
        $id=$this->insert($data);
        if($id){
            //更新path
            $path=$this->getPath($id,$data['pid']);
            if($path !== '')
                $this->eq('id',$id)->update(['path'=>$path]);
            $this->reset();
            $this->updateCache($where);
            return $id;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 修改分类
     * @param array $data 新数据
     * @param array $where
     * @return bool
     *--------------------------------------------------------------------*/
    public function edit($data,$where=array()){
        if(isset($data['images_url']) && $data['images_url']){
            $tmp=explode('_',$this->type);
            File::thumb($data,$tmp[0]);
        }
        $data['slug']=$data['slug'] ?? '';
        $data['slug']=$this->getSlug($data['slug'],$data['name']);
        $data['level']=$this->getLevel($data['pid']);
        $data['path']=$this->getPath($data['id'],$data['pid']);
        $data=$this->_filterDataE($data);
        $old_data=$this->getById($data['id']);
        if($this->update($data)){
            $this->childrenChange($old_data,$data);
            $this->updateCache($where);
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 通过分类名获取id
     * @param string $name 分类名
     * @param bool $add 当分类名在数据表里不存在时，是否自动添加此分类名进数据表
     * @return int: 获取不到返回0,否则返回对应分类名的id
     *--------------------------------------------------------------------*/
    public function getIdByName($name,$add=false){
        $data=$this->eq('name',$name)->eq('type',$this->type)->find(null,true);
        if($data)
            return $data['id'];
        if(!$add)
            return 0;
        $id=$this->add([
            'pid'=>0,
            'name'=>$name,
            'type'=>$this->type
        ]);
        return ($id===false) ? 0 : $id;
    }

    /**
     * 通过分类id删除分类
     * @param $id
     * @param string 内页的数据表名
     * @param array $where
     * @return bool
     */
    public function del($id,$table='portal_post',$where=array()){
        if($this->hasChildren($id)) {
            return '存在子分类不能删除';
        }
        //查询有没有文档
        if($this->from($table)->eq('category_id',$id)->find()){
            return '此分类存在文档，不能直接删除，需要先把文档转移到其它分类';
        }

        if($this->eq('type',$this->type)->eq('id',$id)->delete()){
            $this->updateCache($where);
            return true;
        }
        return '删除失败，'.$this->type.'种类下没有id为'.$id.'的分类';
    }
}
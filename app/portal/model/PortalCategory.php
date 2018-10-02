<?php
namespace app\portal\model;
use core\Tree;
class PortalCategory extends \core\Model
{
    public $table='portal_category';
    public $primaryKey = 'id';
    /**
     * 检查分类名在数据库中是否已经存在
     * @param $name string
     * @return bool：存在返回false,不存在返回true
     */
    public function checkName($name){
        $id=get('id') ? get('id') :post('id');
        //编辑分类时排除自身的name
        if($id) $this->ne('id',$id);
        return ($this->eq('name',$name)  ->find()===false)?true:false;
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
     * 检查pid是否可用
     * @param $slug string
     * @return bool：pid为0时直接返回true,数据库存在时返回true,不存在时返回false
     */
    public function checkPid($pid){
        if($pid==0) return true;
        $id=get('id') ? get('id') :post('id');
        if($id) {
            //编辑分类时排除子孙分类的id作父id
            $current=$this->getById($id);
            $this->reset();
            $this->notlike('path', $current['path']);
        }
        return ($this->eq('id',$pid)  ->find()===false) ? false : true;
    }

    /**
     * 添加分类
     * @param $data:已经验证过的数据
     * @return bool|int:添加成功返回插入id,失败返回false
     */
    public function add($data){
        $data['slug']=isset($data['slug'])?$data['slug']:'';
        $data['slug']=$this->getSlug($data['slug'],$data['name']);
        $data['level']=$this->getLevel($data['pid']);
				$id=$this->insert($data);
        if($id){
            //更新path
            $path=$this->getPath($id,$data['pid']);
            if($path !== '')
                $this->update(['path'=>$path,'id'=>$id]);
            $this->reset();
            $this->cache($this->findAll(true));
            return $id;
        }
        return false;
    }

    public function edit($data){
        $data['slug']=isset($data['slug'])?$data['slug']:'';
        $data['slug']=$this->getSlug($data['slug'],$data['name']);
        $data['level']=$this->getLevel($data['pid']);
        $data['path']=$this->getPath($data['id'],$data['pid']);
        if($this->update($data)){
            $this->childrenChange($data);
            $this->cache($this->findAll(true));
            return true;
        }
        return false;
    }
    protected function childrenChange($new_data){
        $data=$this->getChildren($new_data['id'],true);
        if($data){
            $old_data=$this->getById($new_data['id']);
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
            $ret=$slug . mt_rand(10000,99999999);
        }
        return $ret;
    }
    public function getPath($id,$pid){
        if($pid==0) return $id;
        $parent=$this->getById($pid);
        return $parent['path'].'-'.$id;
    }
    public function getLevel($pid){
        if($pid==0) return 1;
        $parent=$this->getById($pid);
        return $parent['level']+1;
    }
    /**
     * 分类格式化为select字符串
     * @return string
     */
    public function getTreeSelect($select=''){
        $select=$select ?? 1;
       return $this->getTree(0,true,['id','name'],'<option value="%id%"%select%>%__repeat_content__%</option>',['id'=>$select,'text'=>' selected="selected"']);
    }

    /**
     * 分类格式化为table的字符串
     * @return string
     */
    public function getTreeTable(){
        return $this->getTree(0,true,['id','name','description','list_order'],
            '<tr><td>%id%</td><td>%__repeat_content__%</td><td>%description%</td><td align="center"><a class="pure-button btn-success btn-xs edit" href="javascript:;" data="%id%">编辑</a> <a class="pure-button btn-warning btn-xs delete" href="javascript:;" data="%id%">删除</a></td></tr>','');
    }

    /**-------------------------------------------------------------------------------
     * 得到格式化的字符串类型的分类树型结构
     * param $id int|string:分类id，如果提供的是0,将得到全部分类的树型结构，否则得到对应分类id的树型结构
     * param $is_self bool:是否包括自身，分类id不是0时，如果本项为true时，则返回自身和它包含的后代分类，为false只返回它的后代分类
     * param $output array:需要输出的字段名
     * param $format string:对应$output中的字段名的文本格式化输出，还可以加上'%select%'用来被后面的$select['text']格式化替换
     * param $select array:选中项，数组中要包含两个值，选中id和选中时的文本输出text
     * @return string
     *--------------------------------------------------------------------------------*/
    public function getTree($id,$is_self=true,$output=['id','title'],$format='<option title="%title%" value="%id%"%select%>%__repeat_content__%</option>',$select=['id'=>1,'text'=>' selected="selected"']){
        $tree=$this->newTree();
        return $tree->getTree($id,$is_self,'name',$output,$format,$select);
    }

    /**
     * 查询所有分类
     * @param bool $is_cache：是否要从缓存文件中读取分类
     * @return array|bool
     */

    public function getAll($is_cache=true){
        $result=[];
        if($is_cache){
            $result=$this->cache();
        }
        if(empty($result)) {
            $result = $this->findAll(true);
            $result && $this->cache($result);
        }
        return $result;
    }

    /**
     * 把分类缓存到文件中
     * @param null $data：要写入缓存文件的数据，如果不提供或是null表示是要读取缓存
     * @return void|array
     */
    public function cache($data=null){
        $cache=app('cache',[array('path'=>ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'database')]);
        if($data===null){
            return $cache->get('Tree_portal_category');
        }
        $cache->set('Tree_portal_category',$data,0);
    }

    /**
     * 通过id获取对应分类的数据
     * @param $id
     * @return array
     */
    public function getById($id){
        return $this->getNodeById($id)->toArray();
    }

    /**
     * 通过id删除一个分类
     * @param $id
     * @return bool
     */
    public function del($id){
        if($this->eq('id',$id)->delete()){
            $this->cache($this->order('')->findAll(true));
            return true;
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 实例化树类
     * @return \core\Tree|object
     *---------------------------------------------------------------------*/
    public function newTree(){
        return \core\Container::get('tree',[$this->getAll(),['rootId' => 0, 'id' => 'id', 'parent' => 'pid']]);
        //return (new Tree($this->getAll(),['rootId' => 0, 'id' => 'id', 'parent' => 'pid']));
    }
    //获取一个子节点
    protected function getNodeById($id){
        return $this->newTree()->getNodeById($id);
    }

    /**
     * 判断一个分类是否有子分类
     * @param $id
     * @return bool
     */
    public function hasChildren($id){
        return $this->getNodeById($id)->hasChildren();
    }

    /**
     * 获取某个分类的子孙分类
     * @param $id int:分类id
     * @param $is_all bool:true时全部后代获取，false时只获取子分类
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

    /**
     * 除自己及子孙外，其他所有节点的树结构
     */
    public function getTreeNotIn($id){
        $cat=$this->getById($id);
        $data=$this->notlike('path',$cat['path'].'%')->findAll(true);
        if($data){
            $tree= new Tree($data,['rootId' => 0, 'id' => 'id', 'parent' => 'pid']);
            return $tree->getTree(0,true,'name',['id','name'],'<option value="%id%"%select%>%__repeat_content__%</option>',['id'=>$cat['pid'],'text'=>' selected="selected"']);
        }
        return '';
    }

    /** ------------------------------------------------------------------
     * 更新分类缓存，会同步重新统计每个分类的文章个数
     *---------------------------------------------------------------------*/
    public function update_cache(){
        $cates=$this->_sql('SELECT category_id,count(1) AS counts FROM `'.self::$prefix.'portal_relation` GROUP BY category_id',[],false);
        foreach ($cates as $cate){
            $this->update(['id'=>$cate['category_id'],'post_count'=>$cate['counts']]);
        }
        $this->cache($this->getAll());
    }

    /** ---------------------------------------------------------------------
     * 分类面包屑
     * @param int $id
     * @return string
     *-----------------------------------------------------------------------*/
    public function bread($id){
        $tree=$this->newTree();
        $catNode=$tree->getNodeById($id);
        $nodeArray=$catNode->getAncestorsAndSelf();
        return $tree->getTreeAny(array_reverse($nodeArray),0,'name',['id','name','slug'],'<a href="'.url('@category@','slug=%slug%').'">%name%</a>&gt;','');
    }
}
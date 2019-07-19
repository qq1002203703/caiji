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
use core\Model;
use extend\PinYin;
class Tag extends Model
{
    public $table='tag';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';

    /**
     * @var string 验证错误提示消息
     */
    protected $validateMsg='';


    /**------------------------------------------------------------------
     * 检查一个名字是否合法：用于入库前的验证
     * @param  string $name
     * @param string $type
     * @param int $id
     * @return bool 数据库中已经存在返回false,不存在返回true
     *---------------------------------------------------------------------*/
    public function checkName($name,$id=0){
        //编辑时排除自身的name
        if($id) $this->ne('id',$id);
        return ($this->eq('name',$name) ->find()===false)?true:false;
    }
    /**
     * 添加标签
     * @param $data:已经验证过的数据
     * @return bool|int:添加成功返回插入id,失败返回false
     */
    public function add($data){
        $data['slug']=$data['slug'] ?? '';
        $data['slug']=$this->getSlug($data['name'],$data['slug']);
        $data['create_time']=time();
        $id = $this->insert($this->_filterData($data));
        if($id && isset($data['is_people']) && $data['is_people']){//添加到people表
            $this->from('people')->insert(['tid'=>$id,'name'=>$data['name']]);
        }
        return $id;
    }

    /**
     * 修改标签
     * @param $data:已经验证过的数据
     * @return int: 受影响的行数
     */
    public function edit($data){
        $data['slug']=$data['slug'] ?? '';
        $id=$data['id'];
        unset($data['id']);
        //图片处理
        if($data['thumb']){
            $fileModel=app('\app\admin\model\File');
            $data['thumb']=$fileModel->addRemoteFile($data['thumb'],$data['name'],true,true);
        }
        $data['slug']=$this->getSlug($data['name'],$data['slug'],$id);
        $data=$this->_filterDataE($data);
        return $this->eq('id',$id)->update($data);
    }

    /** ------------------------------------------------------------------
     * 为一个页面添加标签
     * @param int $oid
     * @param string $tags
     * @param string $type tag_relation表的type 如 portal_article
     * @param int $status
     * @return int
     *---------------------------------------------------------------------*/
    public function addFromOid($oid,$tags,$type,$status=1,$is_people=0){
        $tags=explode(',',$tags);
        $count=0;
        foreach ($tags as $tag){
            $tid=$this->getTagId($tag,$status,$is_people);
            if($tid){
                if($this->_exec('replace into `'.self::$prefix.'tag_relation`(`tid`, `oid`, `type`) VALUES (?,?,?)',[$tid,$oid,$type],false))
                    $count++;
            }
        }
        return $count;
    }

    /** ------------------------------------------------------------------
     * 编辑一个页面的标签
     * @param int $oid
     * @param string $tags
     * @param string $type
     * @return int
     *---------------------------------------------------------------------*/
    public function editFromOid($oid,$tags,$type,$is_people=0){
        //删除原来的tag
        $this->from('tag_relation')->eq('oid',$oid)->eq('type',$type)->delete();
        //把新的标签添加上去
        $count=0;
        $tags=explode(',',$tags);
        foreach ($tags as $tag){
            if(!$tag)
                continue;
            $tid=$this->getTagId($tag,1,$is_people);
            if($tid){
                if($this->from('tag_relation')->insert(['oid'=>$oid,'type'=>$type,'tid'=>$tid]))
                    $count++;
            }
        }
        return $count;
    }

    /**
     * 检查别名在数据库中是否已经存在
     * @param string $slug
     * @param int $id
     * @return bool 存在返回false,不存在返回true
     */
    public function checkSlug($slug,$id=0){
        if($id) $this->ne('id',$id);
        return ($this->eq('slug',$slug) ->find()===false)?true:false;
    }

    /**
     * 由标签名生成slug
     * @param string $name
     * @param string $slug
     * @return string
     */
    public function getSlug($name,$slug='',$id=0){
        if($slug && $this->checkSlug($slug,$id)){
            return $slug;
        }
        $slug=PinYin::get($name,'all','');
        if($this->checkSlug($slug,$id)){
            return $slug;
        }else{
            $ret='';
            for($i=1;$i<1000;$i++){
                if($this->checkSlug($slug.$i,$id)){
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
     * 从标签名获取tag_id，数据库没有的标签名会自动添加进去
     * @param string $name
     * @param int $status
     * @param int $is_people 必须是0和1
     * @return bool|int
     *--------------------------------------------------------------------*/
    public function getTagId($name,$status=1,$is_people=0){
        if(!$name)
            return 0;
        $data=$this->select('id,is_people')->eq('name',$name)->find(null,true);
        if($data){
            if($is_people && $data['is_people']==0)
                $this->eq('id',$data['id'])->update(['is_people'=>1]);
            return $data['id'];
        }
        $is_people=(int)$is_people;
        if($is_people !==0 && $is_people!==1)
            return 0;
        return $this->add(['name'=>$name,'status'=>$status,'is_people'=>$is_people]);
    }

    /** ------------------------------------------------------------------
     * 添加标签到对应表中
     * @param string|array $tags
     * @param int $id 当前id
     * @param string $type 频道种类
     * @param bool $isPeople 是否是人
     *--------------------------------------------------------------------*/
    public function addTagMap($tags,$oid,$type,$isEdit=false,$isPeople=0){
        if(!$tags)
            return;
        if(is_string($tags))
            $tags=explode(',',$tags);
        //如果是编辑，先删除原标签
        if($isEdit){
            $this->_exec('DELETE FROM `'.self::$prefix.'tag_relation` where oid='.$oid.' and type=?',[$type],false);
        }
        foreach ($tags as $tag){
            //添加到tag表
            $tag_id=$this->getTagId($tag,1,$isPeople);
            if(!$tag_id)
                continue;
            //添加到tag_relation表
            $this->addTagRelation($tag_id,$oid,$type);
        }
    }

    /** ------------------------------------------------------------------
     * 添加数据到tag_relation表中
     * @param int $tid
     * @param int $oid
     * @param string $type
     * @return int|bool|string
     *---------------------------------------------------------------------*/
    public function addTagRelation($tid,$oid,$type){
        return $this->_exec('replace into `'.self::$prefix.'tag_relation`(`tid`, `oid`, `type`) VALUES (?,?,?)',[$tid,$oid,$type],false);
    }

    /** ------------------------------------------------------------------
     * 验证数据
     * @param $data
     * @param int $id
     * @return bool
     *---------------------------------------------------------------------*/
    public function checkData($data,$id=0){
        if(! isset($data['name']) || !$data['name']){
            $this->validateMsg='标签名不能为空';
            return false;
        }
        if(!$this->checkName($data['name'],$id)){
            $this->validateMsg='标签名已经存在';
            return false;
        }
        return true;
    }

    /* ------------------------------------------------------------------
     * 获取验证错误时的消息
     * @return string
     *--------------------------------------------------------------------*/
    public function getValidateMsg(){
        return $this->validateMsg;
    }

    /** ------------------------------------------------------------------
     * 查询某个内页的标签名
     * @param int $oid
     * @return string
     *---------------------------------------------------------------------*/
    public function getName($oid,$type){
        $result= $this->_sql('select group_concat(t.name) as tag_name from '.self::$prefix.'tag_relation as r left join '.self::$prefix.'tag as t on r.tid=t.id where r.oid=? and r.type=? group by r.oid',[$oid,$type],false,true);
        return $result ? $result['tag_name'] :'';
    }

    public function getNameList($oid,$type){
        return $this->_sql('select t.name,t.id,t.slug from '.self::$prefix.'tag_relation as r left join '.self::$prefix.'tag as t on r.tid=t.id where r.oid=? and r.type=?',[$oid,$type],false);
    }

    public function getTagList($id,$table,$where=[],$limit=10,$order='id desc',$select='o.*'){
        return $this->select($select)->from('tag_relation as r')->join($table.' as o','oid=id')->eq('r.tid',$id)->_where($where)->order($order)->_limit($limit)->findAll(true);
    }

    /** ------------------------------------------------------------------
     * 直接输出经格式化为html的标签
     * @param int $oid
     * @param string $type
     * @param string $router
     * @param string $format
     * @return string
     *--------------------------------------------------------------------*/
    public function listFormat($oid,$type,$router='',$format='<a href="{%url%}">{%name%}</a>'){
        $data=$this->getNameList($oid,$type);
        if($data){
            $ret='';
            if(!$router)
                $router='@tags@';
            foreach ($data as $v){
                $ret.=str_replace(['{%url%}','{%name%}'],[url($router,['name'=>urlencode($v['name'])]),$v['name']],$format);
            }
            return $ret;
        }
        return '';
    }

    /** ------------------------------------------------------------------
     * 随机标签获取
     * @param int $limit 一次获取最大个数
     * @param string $select
     * @return array|bool 成功返回结果集 失败返回false
     *---------------------------------------------------------------------*/
    public function getRandom($limit,$id,$select='*'){
        return $this->_sql('SELECT '.$select.' FROM `'.self::$prefix.$this->table.'` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.self::$prefix.$this->table.'` where status=1 and id <> '.$id.'))) and status=1 and id <> '.$id.' ORDER BY id LIMIT '.$limit,[],false);
    }

    public function getAll($where,$limit,$order='t.create_time desc',$select='t.*,r.*',$single=false){
        $this->select($select.',COUNT(*) as total,group_concat(distinct r.type) as all_type')->from($this->table.' as t')->join('tag_relation as r','id=tid')->_where($where)->group('r.tid')->order($order)->_limit($limit);
        if($single)
            return $this->find(null,false);
        else{
            return $this->findAll(true);
        }

    }

    public function delOne($id){
        $res=$this->eq('id',$id)->delete();
        if($res){
            $this->from('tag_relation')->eq('tid',$id)->delete();
        }
        return $res;
    }
}


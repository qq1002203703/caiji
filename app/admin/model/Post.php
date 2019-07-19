<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 内容公共类：论坛帖子、cms文章等的都可以继承这个类，
 * 这个类集合了这种模型共同的增、删、改、查方法
 * ======================================*/


namespace app\admin\model;
use core\Conf;
use core\Model;
use app\common\model\Func;
abstract class Post extends Model
{
    public function checkPid($pid,$type,$id=0){
        if($pid=='0') return true;
        $this->reset();
        if($id) {
            //编辑时排除当前id,以及下级的id作父id
            $this->ne('pid', $id)->ne('id',$id);
        }
        return ($this->eq('id',$pid) ->eq('type',$type) ->find(null,true)===false) ? false : true;
    }
    /**
     * 添加内容
     * @param array $data: 已经验证的数据
     * @return bool|int:成功返回插入的id，否则返回false
     */
    public function add($data){
        $tags=$data['tags'] ?? '';
        $data=$this->filter($data);
        $id=$this->insert($data);
        if($id >0){
            //标签
            $this->addTagMap($tags,$id,$data['type']);
            //关键词自动添加锚文本
            if(Conf::get('keyword_link','portal')==1){
                $model=app('app\admin\model\KeywordLink');
                $model->doLoop($data['content'],[
                    'id'=>$id,
                    'url'=>url('@'.$data['type'].'@',['id'=>$id]),
                    'table'=>$this->table,
                    'tagContent'=>'content',
                ]);
            }
        }
        return $id;
    }
    /**
     * 编辑内容
     * @param array $data
     * @return bool|int
     */
    public function edit($data){
        $children=$data['children_id'] ?? '';
        if(isset($data['tags'])) //不要tags
            unset($data['tags']);
        $data=$this->filter($data);
        $primaryKey=$data[$this->primaryKey];
        unset($data[$this->primaryKey]);
        if($children)
            $this->_exec('update `'.self::$prefix. $this->table.'` set pid='.$primaryKey.' where '.$this->primaryKey.' in ('.$children.')',[],false);
       return $this->eq($this->primaryKey,$primaryKey)->update($data);
    }

    /** ------------------------------------------------------------------
     * 入库前的过滤、数据处理等
     * @param array $data
     * @param bool $isEdit 是否是编辑 true时表示是编辑 要进行的是更新；false时表示不是编辑，要进行的是插入
     * @return array
     *--------------------------------------------------------------------*/
    abstract public function filter($data,$isEdit=false);

    /** ------------------------------------------------------------------
     * 三表联合搜索帖子：当前table、user和category三个表
     * @param array $where：当前表字段不用加表前缀，另外两个表的字段如果有重复必须加上表名，如id  [['user.id','eq','xxxx']]
     * @param string|int|array $limit
     * @param string $order 当前表字段不用加表前缀，另外两个表的字段如果有重复必须加上表名
     * @param bool $single 是否只输出一篇
     * @param string $select
     * @return array|bool
     *--------------------------------------------------------------------*/
    public function search($where=[],$limit='0,10',$order='id desc',$single=false,$select=''){
        if(!$select)
            $select=$this->table.'.*,user.username as username ,user.avatar as avatar,category.name as category_name,category.slug as category_slug,category.thumb as category_thumb,category.pid as category_pid';
        else
            $select.=',user.username as username ,user.avatar as avatar,category.name as category_name,category.slug as category_slug,category.thumb as category_thumb';
        $sql='SELECT '.$select.' FROM `'.self::$prefix.$this->table.'` as '.$this->table.'  left join '.self::$prefix.'user as user on '.$this->table.'.uid=user.id left join '.self::$prefix.'category as category on '.$this->table.'.category_id=category.id';
        $where=Func::whereAddTable($where,$this->table);
        $order=Func::orderAddTable($order,$this->table);
        $this->_where($where)->_limit($limit)->order($order);
        $sql.=$this->_buildSql(['where','order','limit']);
        $param=$this->params;
        $this->reset(false);
        return $this->_sql($sql,$param,false,$single);
    }

    /** ------------------------------------------------------------------
     * 获取一个内容
     * @param int|string $primaryKeyValue  主键值
     * @return bool|array
     *--------------------------------------------------------------------*/
    public function getOne($primaryKeyValue){
        $result=$this->search([[$this->primaryKey,'eq',$primaryKeyValue]],1,'',true);
        if(isset($result['thumb_ids']) && $result['thumb_ids']){//图集
            $result['thumb_ids']=$this->_sql('select id,uri from '.self::$prefix.'file where id in ('.$result['thumb_ids'].')',[],false);
        }
        return $result;
    }

    /** ------------------------------------------------------------------
     * 随机帖子获取
     *--------------------------------------------------------------------*/
    public function ramdom(){

    }

    /** ------------------------------------------------------------------
     * 添加标签到对应表中
     * @param string|array $tags
     * @param int $id 当前id
     * @param string $type 频道种类
     * @param bool $isEdit 是否是编辑
     *--------------------------------------------------------------------*/
    public function addTagMap($tags,$id,$type,$isEdit=false){
        if(!$tags)
            return;
        if(is_string($tags))
            $tags=explode(',',$tags);
        //如果是编辑，先删除原标签
        if($isEdit){
            $this->_exec('DELETE FROM `'.self::$prefix.'tag_relation` where oid='.$id.' and type=?',['portal_'.$type],false);
        }
        $tagModel=app('\app\admin\model\Tag');
        foreach ($tags as $tag){
            $tag_id=$tagModel->getTagId($tag);
            if(!$tag_id) continue;
            $this->_exec('replace into `'.self::$prefix.'tag_relation`(`tid`, `oid`, `type`) VALUES (?,?,?)',[$tag_id,$id,'portal_'.$type],false);
        }
    }

    /** ------------------------------------------------------------------
     * 查询标签
     * @param int $oid
     * @return bool|array
     *---------------------------------------------------------------------*/
    public function tags($id,$type){
        $data=$this->_sql('select t.name,t.id from '.self::$prefix.'tag_relation as r left join '.self::$prefix.'tag as t on r.tid=t.id where r.oid=? and r.type=?',[$id,'portal_'.$type],false,false);
        return $data? $data :'';
    }

    public function tagsHtml($id,$type,$router='',$format='<a href="{%url%}">{%name%}</a>'){
        $data=$this->tags($id,$type);
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

}
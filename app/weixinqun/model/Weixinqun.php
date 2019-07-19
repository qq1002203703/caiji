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

namespace app\weixinqun\model;

class Weixinqun extends \core\Model
{
    /**
     * @var string 表名
     */
    public $table='weixinqun';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';

    /**
     * 获取一篇文章
     * @param string $select
     * @param array $where
     * @return bool|array
     */
    public function getOne($select='',$where=array()){
        if($ret=$this->getSome( $select, $where, '1' )){
            return $ret[0];
        }
            return false;
    }

    /**------------------------------------------------------------------
     * 将用户名、分类名、城市名压入单条结果中
     * @param $ret
     * @return mixed
     *---------------------------------------------------------------------*/
    public function getMore($ret){
        if(isset($ret['uid'])){
            $ret['username']=$this->_sqlField('username','select username from '.self::$prefix.'user WHERE id=?',[$ret['uid']],false);
        }
        if(isset($ret['city_id'])){
            $ret['city_name']=$this->_sqlField('name','select name from '.self::$prefix.'city WHERE id=?',[$ret['city_id']],false);
        }
        if(isset( $ret['category_id'])){
            $ret['category_name']=$this->_sqlField('name','select name from '.self::$prefix.'category WHERE id=?',[$ret['category_id']],false);
        }
        return $ret;
    }
    /**
     * 查询文章
     * @param string $select
     * @param array $where
     * @param string|array $limit
     * @param string $order
     * @return array
     */
    public function getSome($select='',$where=array(),$limit='10',$order='id DESC'){
        $select=$select ? $select : '*';
        return $this->select($select)
            ->_where($where)
            ->order($order)
            ->_limit($limit)
            ->findAll(true);
    }

    /*------------------------------------------------------------------
     * 数据入库
     * @param $data：已验证过的安全数据
     * @return bool|int
     *---------------------------------------------------------------------
     */
    public function add($data){
        $tags=$data['tags'] ?? '';
        $data=$this->filter($data);
        $id=$this->insert($data);
        if($id>0){
            //添加标签
            $this->addTagsMap($tags,$id);
        }
        return $id;
    }

    /**
     * 数据入库前过滤和处理
     * @param $data
     * @return array
     */
    public function filter($data){
        $data=$this->_filterData($data);
        $data['uid']=(isset($data['uid']) && $data['uid']) ? $data['uid'] : ($_SESSION['uid'] ?? 1);
        $data['create_time']=(isset($data['create_time']) && $data['create_time']) ? strtotime($data['create_time']): TIME;
        $data['published_time']=isset($data['published_time']) && $data['published_time']? strtotime($data['published_time']): TIME;
        $data['update_time']=isset($data['update_time']) && $data['update_time']? strtotime($data['update_time']):TIME;
        return $data;
    }

    /** ------------------------------------------------------------------
     * 按分类名获取分类id,如果此分类名在分类数据表中还没添加过，会自动添加
     * @param string $name:分类名
     * @param int $default：分类为空时，默认返回的分类id
     * @return int|bool:插入数据失败时返回false,否则返回对应分类id
     *---------------------------------------------------------------------*/
    public function getCategoryId($name,$default=1,$type='weixinqun'){
        if(!$name)
            return $default;
        $id=$this->_sqlField('id','select id from '.self::$prefix.'category where name=? and type= ? ',[$name,$type],false);
        if($id)
            return $id;
        return app('\app\admin\model\Category',['type'=>$type])->add([
            'name'=>$name,
            'pid'=>0,
            'type'=>$type
        ]);
    }

    /** ------------------------------------------------------------------
     * 获取分类名
     * @param int $id
     * @return string
     *---------------------------------------------------------------------*/
    public function getCategoryName($id){
        return $this->_sqlField('name','select name from '.self::$prefix.'category where id= ?',[$id],false) ?? '';
    }
    public function getCategory($limit,$type){
        return $this->_sql('select * from '.self::$prefix.'category where type= ? order by name limit '.$limit,[$type],false);
    }

    /**------------------------------------------------------------------
     * 从地区名获取地区id
     * @param string $name 地区名
     * @param int $default 默认地区id
     * @return int|string
     *---------------------------------------------------------------------*/
    public function getCityId($name,$default=110100){
        if(!$name)
            return $default;
        $id=$this->_sqlField('id','select id from '.self::$prefix.'city where  level=2 and name like ? order by id desc',['%'.$name.'%'],false);
        if($id)
            return $id;
        return $default;
    }

    /** ------------------------------------------------------------------
     * 获取成市名
     * @param int $id
     * @return string
     *---------------------------------------------------------------------*/
    public function getCityName($id){
        return $this->_sqlField('name','select name from '.self::$prefix.'city where id= ?',[$id],false) ?? '';
    }

    /** ------------------------------------------------------------------
     * 从标签名获取tag_id，数据库没有的标签名会自动添加进去
     * @param string $name
     * @return bool|int
     *---------------------------------------------------------------------
     */
    public function getTagsId($name){
        if(!$name)
            return 0;
        $id=$this->_sqlField('id','select id from '.self::$prefix.'tag where name=? and type=? ',[$name,'weixinqun'],false);
        if($id)
            return $id;
        return $this->_exec('INSERT INTO `'.self::$prefix. 'tag`(`name`, `type`) VALUES (?,?)',[$name,'weixinqun'],false);

    }

    /** ------------------------------------------------------------------
     * 添加到对应表中
     * @param $tags
     * @param $id
     *---------------------------------------------------------------------
     */
    public function addTagsMap($tags,$id){
        if(!$tags)
            return;
        $tags=explode(',',$tags);
        foreach ($tags as $tag){
            $tag_id=$this->getTagsId($tag);
            if(!$tag_id) continue;
            $this->_exec('replace into `'.self::$prefix.'tag_relation`(`tid`, `oid`, `type`) VALUES (?,?,?)',[$tag_id,$id,'weixinqun']);
        }
    }

    /** ------------------------------------------------------------------
     * 查询标签
     * @param int $oid
     * @return string
     *---------------------------------------------------------------------*/
    public function getTagName($oid){
        return $this->_sql('select t.name,t.id,t.slug from '.self::$prefix.'tag_relation as r left join '.self::$prefix.'tag as t on r.tid=t.id where r.oid=? and r.type=?',[$oid,'weixinqun'],false,false);
    }

    /** ------------------------------------------------------------------
     * 随机获取N条记录
     * @param int $limit:获取记录数
     * @param string $format
     * @return string
     *---------------------------------------------------------------------*/
    public function getRandomItem($limit,$format='<li><a href="{%url%}"><img src="{%thumb%}">{%id%}.{%title%}</a></li>',$length=50){
        //$data=$this->_sql('SELECT * FROM `'.self::$prefix.'weixinqun` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.self::$prefix.'weixinqun` where have_img=1 and caiji_isfabu=1))) and have_img=1 and caiji_isfabu=1 ORDER BY id LIMIT '.$limit,[],false);
        $data=$this->_sql('SELECT * FROM `'.self::$prefix.'weixinqun` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.self::$prefix.'weixinqun` ))) ORDER BY id LIMIT '.$limit,[],false);
        if(!$data)
            return '';
        $ret='';
        foreach ($data as $v){
            $ret.=str_replace([
                '{%url%}',
                '{%title%}',
                '{%qun_qrcode%}',
                '{%qrcode%}',
                '{%id%}',
                '{%content%}'
            ],[
                url('@weixinqun@',['id'=>$v['id']]),
                $v['title'],
                $v['qun_qrcode']?:'/uploads/images/no.gif',
                $v['qrcode']?:'/uploads/images/no.gif',
                $v['id'],
                mb_substr(strip_tags($v['content']),0,$length),
            ],$format);
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 获取最新公众号文章
     *--------------------------------------------------------------------*/
    public function getNewestGzh($limit,$select='*'){
        return $this->select($select)->eq('type',3)->order('create_time desc')->limit($limit)->findAll(true);
    }

    /** ------------------------------------------------------------------
     * getImg
     * @param array $arrImg
     * @param string $who
     * @return string
     *---------------------------------------------------------------------*/
    public function getImg(array $arrImg, $who){
        if($arrImg[$who]) {
            return '/uploads/images/'.$arrImg[ $who ];
        }
        unset($arrImg[$who]);
        if(isset($arrImg['qrcode']) && $arrImg['qrcode'])
            return '/uploads/images/'.$arrImg['qrcode'];
        foreach ($arrImg as $k =>$v){
           if($arrImg[$k])
               return '/uploads/images/'.$arrImg[$k];
        }
        return '/uploads/images/qun_default.png';
    }

    /** ------------------------------------------------------------------
     * 微信群搜索
     * @param array $where:二维数组，例 [['id','>',10],['title','like','%红包群%']]
     * @param string|int $limit：
     * @param string $order
     * @return array|bool
     *---------------------------------------------------------------------*/
    public function seachWeixinqun($where,$limit='0,10',$order=''){
        $sql='SELECT * FROM `'.self::$prefix.'weixinqun`';
        if($where)
            $sql.=' where ';
        $whereArr=[];
        foreach ($where as $k=>$v){
            if($k==0){
                $sql.="{$v[0]} {$v[1]} :ph{$k}";
            }else{
                $sql.=" and {$v[0]} {$v[1]} :ph{$k}";
            }

            $whereArr[':ph'.$k]=$v[2];
        }
        if($order){
            $sql.=' order by '.$order;
        }
        if(is_int($limit)){
            $sql.=' limit :limit';
            $whereArr[':limit']=$limit;
        }elseif(is_string($limit)){
            $limit=explode(',',$limit);
            $n=count($limit);
            if($n ==1){
                $sql.=' limit :limit';
                $whereArr[':limit']=$limit[0];
            }elseif($n ==2){
                $sql.=' limit :limit1 , :limit2';
                $whereArr[':limit1']=$limit[0];
                $whereArr[':limit2']=$limit[1];
            }
        }
        return $this->_sql($sql,$whereArr,false,false);
    }

    /** -----------------------------------------------------------------
     * 获取上一篇 下一篇
     * @param array $pre_where
     * @param array $next_where
     * @param string $order
     * @param bool $outTitle 是否输出标题.
     * @return string
     *--------------------------------------------------------------------*/
    public function getPreNext($pre_where,$next_where,$order,$type,$outTitle=false){
        $pre=$this->reset()->_where($pre_where)->order($order)->find(null,true);
        $next=$this->reset()->_where($next_where)->order($order)->find(null,true);
        $ret='';
        $typeArr=[1=>'weixinqun',2=>'weixin',3=>'gzh'];
        if($pre){
            $ret.='<a class="pre" href="'.url('@'.$typeArr[$type].'@',['id'=>$pre['id']]).'">上一篇'.($outTitle ? ':'.$pre['title'] : '').'</a>';
        }
        if($next)
                $ret.='<a class="next" href="'.url('@'.$typeArr[$type].'@',['id'=>$next['id']]).'">下一篇'.($outTitle ? ':'.$next['title'] : '').'</a>';
        return $ret;
    }
}
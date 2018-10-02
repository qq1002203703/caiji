<?php
namespace app\portal\model;

class PortalPost extends \core\Model
{
    public $table='portal_post';
    public $primaryKey = 'id';
    /**
     * 添加文章
     * @param array $data: 已经验证的数据
     * @return bool|int:成功返回插入的id，否则返回false
     */
    public function add($data){
        $category=$data['cateid'];
        $data=$this->filter($data);
        $id=$this->insert($data);
        if($id>0){
            //同步分类
            $this->add_category($category,$id);
        }
        return $id;
    }

    /**
     * 同步分类
     * @param $category
     * @param $post_id
     */
    protected function add_category($category,$post_id){
        $category=explode(',',$category);
        $values='';
        $param[':post_id']=$post_id;
        foreach ($category as $key=>$item){
            if($key==0)
                $values="(:post_id ,:item{$key}) ";
            else
                $values.=",(:post_id ,:item{$key}) ";
            $param[':item'.$key]=$item;
            //$this->_exec('update '.self::$prefix.'portal_category SET post_count=post_count+1',[],false);
        }
        //echo 'insert into '.self::$prefix.'portal_relation (`post_id`, `category_id`) VALUES '.$values;
        $this->_exec('replace into '.self::$prefix.'portal_relation (`post_id`, `category_id`) VALUES '.$values,$param,false);
    }

    /**
     * 数据入库前过滤和处理
     * @param $data
     * @return array
     */
    public function filter($data){
        $data=$this->_filterData($data);
        $data['type']= $data['type'] ?? 1;
        $data['status']=$data['status'] ?? 1;
        $data['allow_comment']=$data['allow_comment'] ?? 1;
        $data['uid']=$data['uid'] ?? $_SESSION['uid'];
        $data['create_time']=isset($data['create_time']) && $data['create_time'] ? strtotime($data['create_time']): TIME;
        $data['published_time']=isset($data['published_time']) && $data['published_time']? strtotime($data['published_time']): TIME;
        $data['update_time']=isset($data['update_time']) && $data['update_time']? strtotime($data['update_time']):TIME;
        $data=$this->getPay($data);
        $data=$this->getCount($data);
        $data['files']=isset($data['files'])?$this->getFiles($data['files']):'';
        return $data;
    }

    /**
     * 附件获取
     * @param $files
     * @return string
     */
    public function getFiles($files){
       if(is_string($files)){
           return $files;
       }
       if(is_array($files)){
           foreach ($files as $k =>$v){
               if(!isset($v['url']) || $v['url'] =='')
                   unset($files[$k]);
           }
           if($files)
               return json_encode(array_merge($files));
       }
        return '';
    }

    /**
     * 计数的获取
     * @param $data
     * @return mixed
     */
    public function getCount($data){
        $counts=app('config')::get('counts','site');
        $data['views']=(int) ($data['views'] ?? mt_rand(40,$counts['views']));
        $data['likes']=(int)($data['likes'] ?? mt_rand(10,$counts['likes']));
        $data['downloads']=(int)($data['downloads'] ?? mt_rand(0,$counts['downloads']));
        return $data;
    }

    /**
     * 售价信息获取
     * @param $data
     * @return mixed
     */
    public function getPay($data){
        if(isset($data['pay_type'])){
            switch ($data['pay_type']){
                case 1: //金币
                    $data['coin']=$data['coin'] ?? 0;
                    $data['money']=0;
                    break;
                case 2: //金钱
                    $data['money']=$data['money']  ?? 0;
                    $data['coin']=0;
                    break;
                default: //免费
                    $data['pay_type']=0;
                    $data['money']=0;
                    $data['coin']=0;
            }
        }else{
            $data['pay_type']=0;
            $data['money']=0;
            $data['coin']=0;
        }
        return $data;
    }

    /**
     * * 联合三表查询文章
     * @param string|array $limit
     * @param array $where
     * @param string $order
     * @return array
     */

    public function getPost( $select='',$where=array(),$limit='10',$order='p.id DESC'){
        $select=$select ? $select : 'p.*';
        $select.=',GROUP_CONCAT(c.id SEPARATOR ",") as cateid,GROUP_CONCAT(c.name SEPARATOR ",") as catename';
        return $this->select($select)
            ->from('portal_post as p')
            ->join('portal_relation r',  'r.post_id=p.id' )
            ->join('portal_category c','r.category_id=c.id')
            ->_where($where)
            ->group('p.id')
            ->order($order)
            ->_limit($limit)
            ->findAll(true);
    }

    /**
     * 三表联合查询获取一篇文章
     * @param string $select
     * @param array $where
     * @return bool|array
     */
    public function getOne($select='',$where=array()){
        if($ret=$this->getPost( $select, $where, '1' )){
            return $ret[0];
        }else{
            return false;
        }
    }

    /**
     * 两表联合查询总数
     * @param array $where
     * @return bool
     */
    public function getPostCout($where=array()){
        $where1='';
        $where2='';
        $param=[];
        if($where){
            if(isset($where['r.category_id'])){
                $where1=' where r.category_id=:where1 ';
                $param[':where1']=$where['r.category_id'];
            }
            if(isset($where['p.keywords'])){
                $where2=" and p.{$where['p.keywords'][0]} {$where['p.keywords'][1]} :where2 ";
                $param[':where2']=$where['p.keywords'][2];
            }
        }
        if($where1=='' && $where2==''){
            $sql=' SELECT count(1) as counts FROM '.self::$prefix.'portal_post';
        }else{
             $sql='select count(1) as counts from (
                        SELECT count(1) FROM '.self::$prefix.'portal_post as p 
                          LEFT JOIN '.self::$prefix.'portal_relation r on r.post_id=p.id '.$where2.$where1.'
                          GROUP BY p.id 
                           ) as ta';
        }
        $ret= $this->_sql($sql,$param,false);
        if($ret)
            return $ret[0]['counts'];
        return false;
    }

    /**
     * 编辑文章
     * @param $data
     * @return bool|\core\AR|int
     */
    public function edit($data){
        $category=$data['cateid'];
        $data=$this->filter($data);
        $ret=$this->update($data);
        if($ret>0){
            //同步分类
            $this->change_category($category,$data['id']);
        }
        return $ret;
    }

    /**
     * 更改分类
     * @param $category_id
     * @param $post_id
     */
    public function change_category($category_id,$post_id){
        $this->_exec('delete from '.self::$prefix.'portal_relation where post_id=?', [$post_id],false);
        $this->add_category($category_id,$post_id);
    }
    /** ------------------------------------------------------------------
     * 随机文章
     * @param int $num
     *-------------------------------------------------------------------*/
    public function rang_post($select,$num=10){
        $this->reset();
        return $this->getPost($select,[],$num,'rand()');
    }
    /** ------------------------------------------------------------------
     * 最新文章
     * @param int $num
     *-------------------------------------------------------------------*/
    public function newest_post($select,$num=10){
        $this->reset();
        return $this->getPost($select,[],$num,'id desc');
    }
}
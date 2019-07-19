<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * portal常用shell工具
 * ======================================*/

namespace shell\ctrl;
use shell\BaseCommon;
class Portal extends BaseCommon
{
    protected $fileBodyName='crontab';
    protected $path='cache/shell/ctrl/';
    public $taskName='';
    protected function _init(){
        $this->_setCommandOptions(['-n'=>['taskName']],$this->param);
    }

    public function crontab(){
        switch ($this->taskName){
            case 'zhihu':
                $this->zhihu();
                break;
            case 'qilinyue':
                $this->qilinyue();
                break;
            case 'bilibili':
                $this->bilibili();
                break;
            default:
                $this->outPut('请输入正确的任务名，格式: -n taskName'.PHP_EOL,true);
        }
    }
    //知乎答案定时发布
    public function zhihu(){
        $table='caiji_zhihu_answer';
        $where=[['isfabu','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        //$num=mt_rand(2,4);
        $num=1;
        $i=0;
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table,$num,&$i){
            $this->outPut('开始处理：id=>'.$item['id'].',from_id=>'.$item['from_id'].'-------------'.PHP_EOL);
            if($this->model->from('portal_post')->select('id')->eq('from_id',$item['from_id'])->find(null,true)){
                $this->outPut('  已经存在相同的from_id'.PHP_EOL);
                $this->update($item['id'],['isfabu'=>1],$table);
                return 1;
            }
            $data=[
                'title'=>mb_substr(strip_tags($item['content']),0,18),
                'content'=>$item['content'],
                'type'=>'goods',
            ];
            $data['category_id']=mt_rand(3,574);
            $data['uid']=$this->getUserId($item['username']);
            $data['from_id']=$item['from_id'];
            $item['comment']=explode('{%|||%}',$item['comment']);
            $data['comments_num']=count($item['comment']);
            $data['create_time']=(int)strstr($item['comment'][$data['comments_num']-1],'{',true);
            if(!$data['create_time']) $data['create_time']=time();
            else $data['create_time'] -= mt_rand(20,40)*60;
            $data['update_time']=$data['create_time'];
            if($id=$this->model->from('portal_post')->insert($data)){
                $this->outPut(' 成功添加到portal_post表'.PHP_EOL);
                $this->addComment($item['comment'],$id,'portal_post');
                $this->addTag($id,$item['tag'],'portal_goods',0);
                $this->update($item['id'],['isfabu'=>1],$table);
                $this->updateCateNum($data['category_id'],'portal_post');
            }
            $i++;
            if($i>=$num)
                return 'break all';
            return 0;
        });
    }

   public function qilinyue(){
       $table='caiji_boxcom';
       $where=[['isfabu','eq',0]];
       $total=$this->model->count([
           'from'=>$table,
           'where'=>$where
       ]);
       $num=mt_rand(1,3);
       //$num=1;
       $i=0;
       $this->doLoop($total,function ($perPage)use ($table,$where){
           return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
       },function ($item)use ($table,$num,&$i){
           $this->outPut('开始处理：id=>'.$item['id'].',-------------'.PHP_EOL);
           $data=[
               'title'=>$item['title'],
               'type'=>'goods',
           ];
           $data['category_id']=mt_rand(2,16);
           $item['username']='';
           $data['uid']=$this->getUserId($item['username']);
           $this->keywordLink2($item['content']);
           $item['comment']=explode('{%|||%}',$item['content']);
           $data['content']=array_shift($item['comment']);
           $data['comments_num']=count($item['comment']);
           $data['create_time']=time()-mt_rand(3600*2,3600*4);
           $data['update_time']=$data['create_time'];
           if($id=$this->model->from('portal_post')->insert($data)){
               $this->outPut(' 成功添加到portal_post表'.PHP_EOL);
               $this->addComment2($item['comment'],$id,$data['create_time'],'portal_post');
               //$this->addTag($id,$item['tag'],'portal_goods',0);
               $this->update($item['id'],['isfabu'=>1],$table);
               $this->updateCateNum($data['category_id'],'portal_post');
           }
           $i++;
           if($i>=$num)
               return 'break all';
           return 0;
       });
   }

   public function bilibili(){
       $this->outPut('开始进行bilibili数据定时发布'.PHP_EOL,true);
       $table='caiji_bilibili';
       $where=[['isfabu','eq',0]];
       $num=mt_rand(1,2);
       //$num=1;
       $data=$this->model->from($table)->_where($where)->order('id')->limit($num)->findAll(true);
       foreach ($data as $item){
           $this->outPut('开始处理：id=>'.$item['id'].',-------------'.PHP_EOL);
           $where=['from_id'=>$item['from_id'],['pfid',($item['pfid']>0 ? 'gt' : 'eq'),0]];
            if($this->model->from('portal_post')->_where($where)->find(null,true)){
                $this->outPut(' 此from_id已入库'.PHP_EOL);
                $this->update($item['id'],['isfabu'=>1],$table);
                continue;
            }
           $data=[
               'title'=>$item['title'],
               'seo_title'=>$item['seo_title'],
               'type'=>'goods',
               'from_id'=>$item['from_id'],
               'content'=>$item['content'],
               'videos'=>$item['videos'],
           ];
            //确定pid
           if($item['pfid']>0){
               $parent=$this->model->from('portal_post')->eq('from_id',$item['pfid'])->eq('pid',0)->find(null,true);
               if(!$parent){
                   $this->outPut('  id=>'.$item['id'].'没有找到对应的父级'.PHP_EOL,true);
                   continue;
               }
                $data['pid']=$parent['id'];
           }
           $data['category_id']=mt_rand(2,14);
           $item['username']='';
           $data['uid']=$this->getUserId($item['username']);
           $this->keywordLink2($data['content']);
           $item['comment']=$item['comment']?explode('{%@@@%}',$item['comment']):[];
           $data['comments_num']=count($item['comment']);
           $data['create_time']=time()-mt_rand(3600*2,3600*4);
           $data['update_time']=$data['create_time'];
           if($id=$this->model->from('portal_post')->insert($data)){
               $this->outPut(' 成功添加到portal_post表'.PHP_EOL);
               $this->addComment3($item['comment'],$id,$data['create_time'],'portal_post');
               $this->addTag($id,$item['tag'],'portal_goods',0);
               $this->update($item['id'],['isfabu'=>1],$table);
               $this->updateCateNum($data['category_id'],'portal_post');
           }
       }
   }

    protected function addTag($oid,$tags,$type,$isPeople){
        return app('\app\admin\model\Tag')->addFromOid($oid,$tags,$type,1,$isPeople);
    }

    /** ------------------------------------------------------------------
     * update
     * @param int $id
     * @param array $data
     *---------------------------------------------------------------------*/
    protected function update($id,$data,$table){
        if($this->model->from($table)->eq('id',$id)->update($data))
            $this->outPut('成功：更新'.$table.'！'.PHP_EOL);
        else
            $this->outPut('失败：更新'.$table.'！'.PHP_EOL);
    }

    /** ------------------------------------------------------------------
     * 获取分类id
     * @param string $name
     * @return int
     *--------------------------------------------------------------------*/
    protected function getCategoryId($name){
        $data=$this->model->from('category')->select('id')->eq('name',$name)->eq('type','portal_goods')->find(null,true);
        if($data)
            return (int)$data['id'];
        $arr=[3,4,5,25,22,29];
        return $arr[array_rand($arr,1)];
    }

    /** ------------------------------------------------------------------
     * 获取用户id : 用户名为空时取随机用户，用户名不存时自动添加用户
     * @param $username
     * @return int
     *---------------------------------------------------------------------*/
    protected function getUserId(&$username){
        $model=app('\app\portal\model\User');
        //用户名为空时 取随机用户
        if(!$username){
            $data=$model->getRandomUser(1,'id,username');
            if($data){
                $username=$data['0']['username'];
                return $data[0]['id'];
            }
            return 0;
        }
        return $model->addFromName($username);
    }

    protected function updateCateNum($id,$table){
        $num=$this->model->count([
            'from'=>$table,
            'where'=>[['category_id','eq',$id]]
        ]);
        if($this->model->from('category')->eq('id',$id)->update(['counts'=>$num]))
            $this->outPut(' 成功更新分类下post个数:'.$num.PHP_EOL);
        else
            $this->outPut(' 失败更新分类下post个数时:'.$num.PHP_EOL);
    }

    /** ------------------------------------------------------------------
     * 添加评论
     * @param array $data
     * @param int $id
     * @param string $table
     * @param array $separator {%||%}
     *---------------------------------------------------------------------*/
    protected function addComment($data,$oid,$table,$separator=['{%|||%}','{%||%}']){
        if(is_string($data))
            $data=explode($separator[0],$data);
        foreach ($data as $item){
            if(!$item){
                $this->outPut(' 本项评论为空跳过!'.PHP_EOL);
                continue;
            }
            $item=explode($separator[1],$item);
            $count=count($item);
            if(count($item) !==3){
                $this->outPut(' 本项评论项目不对：'.$count.PHP_EOL);
                continue;
            }
            $in=[];
            $in['create_time']=$item[0];
            $in['username']=$item[1];
            $in['content']=$item[2];
            unset($item);
            $in['uid']=$this->getUserId($in['username']);
            $in['table_name']=$table;
            $in['oid']=$oid;
            if(!$this->model->from('comment')->insert($in)){
                $this->outPut(' 插入comment表失败!'.PHP_EOL);
                $this->outPut(' 最后的sql:'.$this->model->getSql().PHP_EOL);
                dump($data);
                exit();
            }
        }
    }
    /** ------------------------------------------------------------------
     * 添加评论
     * @param array $data
     * @param int $id
     * @param int $create_time
     * @param string $table
     * @param array $separator {%||%}
     *---------------------------------------------------------------------*/
    protected function addComment2($data,$oid,$create_time,$table,$separator='{%|||%}'){
        if(is_string($data))
            $data=explode($separator,$data);
        foreach ($data as $item){
            if(!$item){
                $this->outPut(' 本项评论为空跳过!'.PHP_EOL);
                continue;
            }
            $create_time+=mt_rand(700,31*60);
            $in=[];
            $in['create_time']=$create_time;
            $in['username']='';
            $in['content']=$item;
            $in['uid']=$this->getUserId($in['username']);
            $in['table_name']=$table;
            $in['oid']=$oid;
            if(!$this->model->from('comment')->insert($in)){
                $this->outPut(' 插入comment表失败!'.PHP_EOL);
                $this->outPut(' 最后的sql:'.$this->model->getSql().PHP_EOL);
                dump($data);
                exit();
            }
        }
    }
    /** ------------------------------------------------------------------
     * 添加评论
     * @param string|array $data
     * @param int $id
     * @param int $create_time
     * @param string $table
     * @param array $separator {%||%}
     *---------------------------------------------------------------------*/
    protected function addComment3($data,$oid,$create_time,$table,$separator=['{%@@@%}','{%@@%}'],$pid=0){
        if(is_string($data))
            $data=explode($separator[0],$data);
        foreach ($data as $item){
            if(!$item){
                $this->outPut(' 本项评论为空跳过!'.PHP_EOL);
                continue;
            }
            $item=explode($separator[1],$item);
            $count=count($item);
            if(count($item) !==3){
                $this->outPut(' 本项评论项目不对：'.$count.PHP_EOL);
                continue;
            }
            $create_time+=mt_rand(700,31*60);
            $in=[];
            $in['create_time']=$create_time;
            $in['username']='';
            $in['pid']=$pid;
            $children='';
            if(strpos($item[2],'{%##%}')){
                list($in['content'],$children)=explode('{%##%}',$item[2]);
            }else
                $in['content']=$item[2];
            $this->keywordLink2($in['content']);
            $in['uid']=$this->getUserId($in['username']);
            $in['table_name']=$table;
            $in['oid']=$oid;
            $id=$this->model->from('comment')->insert($in);
            if(!$id){
                $this->outPut(' 插入comment表失败!'.PHP_EOL);
                $this->outPut(' 最后的sql:'.$this->model->getSql().PHP_EOL);
                dump($data);
                exit();
            }
            if($children){
                $this->outPut(' 开始处理子评论!'.PHP_EOL);
                $this->addComment3($children,$oid,$create_time,$table,['{%|||%}','{%||%}'],$id);
            }
        }
    }

    /** ------------------------------------------------------------------
     * 站内描文本添加
     * @param string $content
     * @return int 返回发生替换的次数
     *--------------------------------------------------------------------*/
    protected function keywordLink(&$content){
        //去掉用户名和发布时间  时间{%||%}用户名{%||%}内容{%|||%}时间{%||%}用户名{%||%}内容{%|||%}
        $i=-1;
        $user_filter=[];
        $content=preg_replace_callback('#\{%\|\|%\}.*?\{%\|\|%\}#' ,function ($mat) use (&$i,&$user_replace,&$user_filter){
            $i++;
            $user_replace[$i]=$mat[0];
            $user_filter[$i]='{%user_filter_'.$i.'%}';
            return $user_filter[$i];
        },$content);
        $model=app('\app\admin\model\KeywordLink');
        $count=$model->doLoop($content);
        if($i>-1){
            $content=str_replace($user_filter,$user_replace,$content);
        }
        return $count;
    }

    protected function keywordLink2(&$content){
        return app('\app\admin\model\KeywordLink')->doLoop($content);
    }

}
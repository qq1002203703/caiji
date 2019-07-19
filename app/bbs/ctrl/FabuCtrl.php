<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 论坛数据入库接口
 * ======================================*/
namespace app\bbs\ctrl;
use app\common\ctrl\ApiCtrl;
use extend\Download;

/**
 * Class FabuCtrl：入口start() 和start2()两个方法
 * @package app\bbs\ctrl
 */
class FabuCtrl extends ApiCtrl
{
    protected $post=[];
    /** ------------------------------------------------------------------
     * 自动发布入口
     *---------------------------------------------------------------------*/
    public function start(){
        if(!$this->checkPermissions())
            die('权限不足!');
        /*if(($is_cate=get('is_cate','int',0))==1){
            //$cateModel=app('\app\portal\model\PortalCategory');
            //echo $cateModel->getTreeSelect();
            exit();
        }*/
        if(!isset($_POST) || !$_POST ) die('无权访问!');
        $data=$this->getPost(['from_id','comments_num','title','content','tu','category_id','category_second','type','from_id','source']);
        //不能为空项-------------------------
        if(!$data['title']) die('标题不能为空');
        if(!$data['content']) die('内容不能为空');
        //默认项--------------------------------
        if($data['from_id']>0){
            if(app('\core\Model')->from('bbs')->select('id')->eq('from_id',$data['from_id'])->find(null,true))
                die('发布成功');
        }else{
            $data['from_id']=0;
        }

        if(!$data['category_id']){
            $data['category_id']=isset($data['category'])? $this->getCategoryId($data['category']) : 1;
        }
        if(!$data['category_second']){
            $data['category_second']='讨论';
        }
        if(!$data['type']){
            $data['type']='1';
        }
        $tag=$data['tag'] ?? '';
        //数据处理并入库
        $id=$this->handler($data);
        if($id > 0){
            //添加tag
            if($tag)
                app('\app\admin\model\Tag')->addFromOid($id,$tag,'bbs_normal',0);
            echo '发布成功';
            //后续处理
            if($this->post){//有图片就下载图片
                $cmd='php '.ROOT.'/cmd tools/download bbsImg '.str_replace('&','@',http_build_query($this->post).' >'.ROOT.'/cache/1.txt 2>&1');
                system($cmd);
            }
        }else{
            echo '入库失败，原因未明';
        }
    }

    /** ------------------------------------------------------------------
     * 自动处理$_POST数据：去掉两头的空白，并对必要的字段进行初始化即如果没有设置就设置为空
     * @param array $require
     * @return array
     *---------------------------------------------------------------------*/
    protected function getPost($require=[]){
        $data=[];
        if($_POST){
            array_walk($_POST,function(&$v,$k){$v=trim($v);});
            $data=$_POST;
            unset($_POST);
            //删除id字段
            if(isset($data['id']))
                unset($data['id']);
            foreach ($require as $v){
                $data[$v]=$data[$v] ?? '';
            }
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * 数据处理器
     * @param array $data
     * @return int 成功返回新插入数据的id，否则返回0
     *--------------------------------------------------------------------*/
    protected function handler($data){
        //内链添加
        $this->keywordLink($data['content']);
        $contents=explode('{%|||%}',$data['content']);
        $count=count($contents);
        $id=0;
        foreach ($contents as $i =>$item){
            if($i==0){ //主贴
                $inData=$this->getItemData($item,false);
                if($inData===false)
                    break;
                $data=array_merge($data,$inData);
                unset($inData);
                $data['comments_num']=$count-1;
                $data['more']=$this->image($data['content'],$data['title'],$data['tu']);
                $data['views']=mt_rand(105,950);
                $data['update_time']=$data['create_time'];
                unset($data['tu']);
               $id=(int) $this->add($data,'bbs');
                if($id <1)
                    break;
                if($data['more']){
                    $this->post['z']=1;
                }
            }else{//评论贴
                $inData=$this->getItemData($item);
                if($inData===false)
                    continue;
                $inData['pid']=0;
                $inData['oid']=$id;
                $inData['table_name']='bbs';
                $inData['more']=$this->image($inData['content'],$data['title']);
                $commentId=$this->add($inData,'comment');
                if($inData['more']){
                    $this->post['c']=isset($this->post['c']) ? $this->post['c'].','.$commentId : $commentId;
                }
            }
        }
        if($this->post)
            $this->post['id']=$id;
        return $id;
    }

    /** ------------------------------------------------------------------
     * 循环采集的内容每项数据获取
     * @param string $item
     * @return array|bool
     *--------------------------------------------------------------------*/
    protected function getItemData($item,$isEdit=true){
        if(!$item)
            return false;
        $data=[];
        list($date,$username,$data['content'])=explode('{%||%}',$item);
        if($data['content']=='')
            return false;
        $data['uid']=$this->getUserId($username);
        if($data['uid']==0)
            return false;
        $data['username']=$username;
        $data['create_time']=$this->getDate($date);
        if(!$isEdit)
            $data['update_time']=$data['create_time'];
        return $data;
    }

    /** ------------------------------------------------------------------
     * 获取发贴时间
     * @param string $strDate
     * @return int
     *--------------------------------------------------------------------*/
    protected function getDate($strDate){
        $date=strtotime($strDate);
        return $date===false ? time() :$date;
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

    /** ------------------------------------------------------------------
     * 获取分类id
     * @param $name
     * @return int
     *--------------------------------------------------------------------*/
    protected function getCategoryId($name){
        return app('\app\bbs\model\BbsCategory')->setType('bbs_normal')->getIdByName($name);
    }

    /** ------------------------------------------------------------------
     * 下载图片的整理
     * @param string $content 内容，会替换内容中的图片为占位符
     * @param string $title 标题
     * @param string $tu 额外图片
     * @return string json格式化的字符串
     *--------------------------------------------------------------------*/
    protected function image(&$content,$title='',$tu=''){
        if($tu){
            $arr=explode('{%|||%}',$tu);
            foreach ($arr as $item){
                $content.='<p><img src="'.$item.'" alt="'.$title.'"></p>';
            }
        }
        $download=new Download();
        $content=$download->addImg('content',$content);
        return $download->all();
    }

    /** ------------------------------------------------------------------
     * 数据入库
     * @param array $data 数据
     * @param string $table 表名
     * @return bool|int
     *--------------------------------------------------------------------*/
    protected function add($data,$table){
        $model=app('\core\Model');
        $model->table=$table;
        $data=$model->_filterData($data);
        return $model->insert($data);
    }

    /** ------------------------------------------------------------------
     * 站内描文本添加
     * @param string $content
     * @return int 返回发生替换的次数
     *--------------------------------------------------------------------*/
    protected function keywordLink(&$content){
        //选去掉用户名和发布时间  时间{%||%}用户名{%||%}内容{%|||%}时间{%||%}用户名{%||%}内容{%|||%}
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

    protected function checkPermissions(){
        return (trim(get('pwd')) === 'Djidksl$$EER4ds58cmO');
    }


    //手动发布入口
    public function start2(){
        if(!$this->_checkIsAdmin())
            return json(['code'=>1,'msg'=>'权限不足']);
        if(!isset($_POST) || !$_POST )
            return json(['code'=>2,'msg'=>'数据不能为空']);
        $data=$this->getPost(['title','content','tu','category_id','category_second','type']);
        //不能为空项-------------------------
        if(!$data['title'])
            return json(['code'=>3,'msg'=>'标题不能为空']);
        if(!$data['content'])
            return json(['code'=>4,'msg'=>'内容不能为空']);
        $data['content']=htmlspecialchars_decode($data['content']);
        //默认项--------------------------------
        if(!$data['category_id']){
            $data['category_id']=isset($data['category'])? $this->getCategoryId($data['category']) : 1;
        }
        if(!$data['category_second']){
            $data['category_second']='讨论';
        }
        if(!$data['type']){
            $data['type']='1';
        }
        $tag=$data['tag'] ?? '';
        //dump($data);return;
        //$data['content']=htmlspecialchars_decode((string)$data['content']);
        //数据处理并入库
        $id=$this->handler($data);
        if($id > 0){
            //添加tag
            if($tag){
                $tag=str_replace('，',',',$tag);
                app('\app\admin\model\Tag')->addFromOid($id,$tag,'bbs_normal',0);
            }
            json(['code'=>0,'msg'=>'发布成功','action'=>url('bbs/post/add_multi')]);
            //后续处理
            if($this->post){//有图片就下载图片
                $cmd='php '.ROOT.'/cmd tools/download bbsImg '.str_replace('&','@',http_build_query($this->post).' >'.ROOT.'/cache/1.txt 2>&1');
                system($cmd);
            }
            return null;
        }else{
            return json(['code'=>5,'msg'=>'入库失败，原因未明']);
        }
    }
}
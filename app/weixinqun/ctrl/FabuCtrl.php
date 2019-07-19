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
namespace app\weixinqun\ctrl;
use app\common\ctrl\ApiCtrl;

/**
 * Class FabuCtrl：入口start() 和start2()两个方法
 * @package app\bbs\ctrl
 */
class FabuCtrl extends ApiCtrl
{
    protected $post=[];
    /** ------------------------------------------------------------------
     * 学校自动发布入口
     *---------------------------------------------------------------------*/
    public function xuexiao(){
        if(!$this->checkPermissions())
            die('权限不足!');
        if(!isset($_POST) || !$_POST)
            die('无权访问!');
        $data=$this->getPost(['from_id','title','shuxing','xingzhi','leixing','content','phone','address','photo','city_id','comment']);
        //不能为空项-------------------------
        if(!$data['title']) die('标题不能为空');
        //if(!$data['from_id']) die('内容不能为空');
        //默认项--------------------------------
        if($data['from_id']>0){
            if(app('\core\Model')->from('xuexiao')->select('id')->eq('from_id',$data['from_id'])->find(null,true))
                die('发布成功');
        }else{
            $data['from_id']=0;
        }
        //数据处理并入库
        header("Content-Type:text/html; charset=utf-8");
        $this->handler($data);
    }

    /** ------------------------------------------------------------------
     * 数据处理器
     * @param array $data
     *--------------------------------------------------------------------*/
    protected function handler($data){
        //内链添加
        $this->keywordLink($data['content']);
        if($data['comment']){
            $comments=explode('{%|||%}',$data['comment']);
            $data['comments_num']=count($comments);
        }else{
            $comments=[];
            $data['comments_num']=0;
        }
        $id=$this->add($data,'xuexiao');
        if(!$id)
            die('学校入库失败');
        //echo '  最新插入的id'.$id.PHP_EOL;
        //入库评论
        if($data['comments_num']>0){
            $this->commentAdd($comments,$id);
        }
        //入库图片
        if($data['photo']){
            $this->imageAdd($data['photo'],$id,$data['title']);
        }
        echo '发布成功';
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
     * 添加评论
     * @param array $data
     * @param int $id
     *---------------------------------------------------------------------*/
    protected function commentAdd($data,$id){
        $timestamp=0;
        foreach ($data as $item){
            $insertData=$this->getItemData($item);
            if($insertData===false)
                continue;
            $insertData['create_time']=time();
            $insertData['pid']=0;
            $insertData['oid']=$id;
            $insertData['table_name']='xuexiao';
            $timestamp=$this->getDate($timestamp);
            $insertData['create_time']=$timestamp;
            //$commentId=
                $this->add($insertData,'comment');
            //echo '  最新插入的评论id为：'.$commentId.PHP_EOL;
        }
    }

    /** ------------------------------------------------------------------
     * 图片处理
     * @param string $photo
     * @param string $title
     * @return bool
     *--------------------------------------------------------------------*/
    protected function imageAdd($photo,$id,$title=''){
        if(!$photo)
            return false;
        $thumbArr=explode('{%|||%}',$photo);
        $fileModel=app('\app\admin\model\File');
        $thumb_ids=[];
        $thumb_uri=[];
        $i=1;
        $count=count($thumbArr);
        foreach ($thumbArr as $thumb){
            if(!is_file(ROOT.'/public'.$thumb)){
                continue;
            }
            $data=[];
            $data['uri']=$thumb;
            if($title)
                $data['title']=($count>1 ? ($title.$i) : $title);
            $ret=$fileModel->add($data,true,true,'xuexiao');
            if($ret){
                $thumb_ids[]=$ret['id'];
                $thumb_uri[]=$ret['uri'];
                $i++;
            }
        }
        if(!$thumb_ids){
            $update=[
                'thumb'=>'',
                'thumb_ids'=>'',
            ];
        }else{
            $update= [
                'thumb'=>$thumb_uri[0],
                'thumb_ids'=>implode(',',$thumb_ids),
            ];
        }
        $fileModel->from('xuexiao')->eq('id',$id)->update($update);
        return true;
    }

    /** ------------------------------------------------------------------
     * 评论的每项数据解析
     * @param string $item
     * @return array|bool
     *--------------------------------------------------------------------*/
    protected function getItemData($item){
        if(!$item)
            return false;
        $data=[];
        list($username,$data['content'])=explode('{%||%}',$item);
        if($username=='游客' || $username=='学生'){
            $username='';
        }
        if($data['content']=='')
            return false;
        $data['uid']=$this->getUserId($username);
        if($data['uid']==0)
            return false;
        $data['username']=$username;
        return $data;
    }

    /** ------------------------------------------------------------------
     * 获取发贴时间
     * @param int $timestamp
     * @return int
     *--------------------------------------------------------------------*/
    protected function getDate($timestamp){
        if($timestamp==0){
            return time()-3600*24*mt_rand(200,300);
        }
        return $timestamp+(mt_rand(20,60)*3600);
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
     * 数据入库
     * @param array $data 数据
     * @param string $table 表名
     * @return bool|int
     *--------------------------------------------------------------------*/
    protected function add($data,$table){
        $model=app('\core\Model');
        $model->table=$table;
        return $model->insert($model->_filterData($data));
    }

    /** ------------------------------------------------------------------
     * 站内描文本添加
     * @param string $content
     * @return int 返回发生替换的次数
     *--------------------------------------------------------------------*/
    protected function keywordLink(&$content){
        return (new \app\admin\model\KeywordLink())->doLoop($content);
    }

    protected function checkPermissions(){
        return (trim(get('pwd')) === 'Djidksl$$EER4ds58cmO');
    }
}
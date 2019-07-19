<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * Bbs模板下的 Post控制器
 * ======================================*/

namespace app\bbs\ctrl;
use core\Conf;
use extend\Helper;
use extend\Paginator;
class PostCtrl extends BbsCommon
{
    /** ------------------------------------------------------------------
     * 帖子展示页
     * @param int $id : 帖子id
     *--------------------------------------------------------------------*/
    public function show($id){
        $this->allTypePost($id,1,'bbs/show');
    }
    public function show2($id){
        $this->allTypePost($id,2,'bbs/show2');
    }
    protected function allTypePost($id,$type,$tpl=''){
        if(!$this->_checkPermissions('show',[])){
            show_error('你没有权限阅读本贴');
        }
        $model=app('\app\bbs\model\Bbs');
        //$data=$model->eq('id',$id)->find(null,true);
        $data=$model->getOne($id);
        if(!$data || ((int)$data['type']) !== ((int)$type))
            show_error('不存在的id');
        $comments=[];
        $re_comments=[];
        $currentPage=get('page','int',1);
        $perPage=Conf::get('bbs','site')['comment_perpage'];
        $page='';
        $url = url('@bbs_post@',['id'=>$id]).'?page=(:num)';
        if($data['comments_num'] >0){
            $cmModel=app('\app\admin\model\Comment');
            $comments=$cmModel->getSome(['c.status'=>1,'recommended'=>0,'pid'=>0,'table_name'=>'bbs','oid'=>$id],20,'create_time desc,id desc');
            $re_comments=$cmModel->getSome(['c.status'=>1,'recommended'=>1,'pid'=>0,'table_name'=>'bbs','oid'=>$id],10,'id asc');
            $page=(string) new Paginator($data['comments_num'],$perPage,$currentPage,$url,[
                'page'=>'laypage-main',
                'current'=>'laypage-curr',
                'next'=>'laypage-next',
                'disabled'=>'laypage-disabled'
            ]);
        }
        if(!$data['keywords']){
            $data['keywords']=$data['title'];
        }
        if(!$data['excerpt']){
            $data['excerpt']=Helper::text_cut($data['content'],250);
        }
        $this->_display($tpl,[
            'title'=>$data['title'],
            'data'=>$data,
            'comments'=>$comments,
            're_comments'=>$re_comments,
            'page'=>$page,
            'tags'=>app('\app\admin\model\Tag')->getNameList($id,'bbs_normal'),
            'isMore'=>(count($comments) > $perPage)
        ],false);
        $this->views_click($id);
    }

    /** ------------------------------------------------------------------
     * 帖子分类列表页
     * @param int $id : 分类id
     *--------------------------------------------------------------------*/
    public function channel($id){
        if(!$this->_checkPermissions('show_list',[])){
            echo '你没有权限阅读本板块贴子';
            return;
        }
        $cateModel=app('\app\bbs\model\BbsCategory');
        $category=$cateModel->find($id,true);
        if(!$category){
            show_error('版块不存在');
        }
        $where=[['category_id','eq',$id]];
        $model=app('\app\bbs\model\Bbs');
        $currentPage=get('page','int',1);
        $perPage=Conf::get('bbs','site')['comment_perpage'];
        $url = url('@bbscategory@',['id'=>$id]).'?page=(:num)';
        $total=$model->count(['where'=>$where]);
        $data=$model->search($where,[($currentPage-1)*$perPage,$perPage],'create_time desc,id desc');
        $page=(string) new Paginator($total,$perPage,$currentPage,$url,[
            'page'=>'laypage-main',
            'current'=>'laypage-curr',
            'next'=>'laypage-next',
            'disabled'=>'laypage-disabled'
        ]);
        $this->_display('bbs/list',[
            'title'=>'',
            'data'=>$data,
            'page'=>$page,
        ],false);
    }

    public function tag_caiji($slug){
        //$this->allTypeTag('bbs_normal',$slug,'bbs/tag_caiji');
        header('HTTP/1.1 301 Moved Permanently');//发出301头部
        header('Location:'.url('@tag@',['slug'=>$slug]));//跳转到带www的网址
    }

    /**--------------------------------------------------
     * 查看次数增加1
     * @param int $id
     *---------------------------------------------------*/
    protected function views_click($id){
        //$id=get('id','int',0);
        if($id<1) return;
        $model=app('\app\bbs\model\Bbs');
        $model->setField('views',1,['id'=>$id]);
    }

    public function add_multi(){
        if(!$this->_checkPermissions('post_add_m',[])){
            $this->_redirect('/','你的权限不足');
        }
        $perPage=(int)get('p','int',5);
        if($perPage <1){
            header('Content-Type:text/html;charset=utf-8');
            echo '每页个数不能小于1';
            return;
        }
        $user=app('\app\portal\model\User');
        $data=$user->getRandomUser($perPage,'username');
        $str=[];
        if($data){
            $time=time()-720*$perPage+mt_rand(200,700);
            array_walk($data,function ($v,$i) use (&$str,&$time){
                if($i==0)
                    $str[]=date('Y-m-d H:i',$time).'{%||%}'.$v['username'].'{%||%}主贴';
                else
                    $str[]=date('Y-m-d H:i',$time).'{%||%}'.$v['username'].'{%||%}评论';
                $time+=mt_rand(200,700);
            });
            unset($data,$time);
        }
        $this->_display('bbs/add_multi',[
            'title'=>'批量发贴',
            'data'=>$str ? implode('{%|||%}'."\n",$str):'',
        ],false);
    }
}
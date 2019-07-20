<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 门户
 * ======================================*/


namespace app\portal\ctrl;
use core\Conf;
use core\Ctrl;
use core\Model;
use extend\Helper;
use extend\Paginator;
class PostCtrl extends Ctrl
{
    /**--------------------------------------
     * 文章内容页
     * @param $id
     *----------------------------------------*/
    public function article($id){
        $this->allTypePost($id,'article','portal/article');
    }

    public function goods($id){
        $this->allTypePost($id,'goods','portal/goods');
    }
    public function group($id){
        $this->allTypePost($id,'group','group/group_details');
    }

    /** ------------------------------------------------------------------
     * 所有文章类型内容页输出
     * @param int $id
     * @param int $type  article,goods,soft,group
     * @param $tpl
     *--------------------------------------------------------------------*/
    protected function allTypePost($id,$type,$tpl){
        if($id<1)
            show_error('输入不正确的id');
        $model=app('\app\portal\model\PortalPost');
        $data=$model->getOne($id);
        if(!$data || ($data['type']) !== $type)
            show_error('不存在的id');
        //$data['content']=Helper::replace_outlink($data['content'],url('portal/index/outlink'));
        if(isset($data['more']) && $data['more'])
            $data['more']=json_decode($data['more'],true);
        if(isset($data['files'])&&$data['files'])
            $data['files']=json_decode($data['files'],true);
        $comments=[];
        //$currentPage=get('page','int',1);
        $perPage=Conf::get('bbs','site')['comment_perpage'];
        //$page='';
        //$url = url('@'.$type.'@',['id'=>$id]).'?page=(:num)';
        if($data['comments_num'] >0){
            $comment_model=app('\app\admin\model\Comment');
            $comments=$comment_model->getSome(['c.status'=>1,'c.pid'=>0,'table_name'=>'portal_post','oid'=>$id],$perPage);
            /*$page=(string) new Paginator($data['comments_num'],$perPage,$currentPage,$url,[
                'page'=>'laypage-main',
                'current'=>'laypage-curr',
                'next'=>'laypage-next',
                'disabled'=>'laypage-disabled'
            ]);*/
            unset($comment_model);
        }
        $this->_display($tpl,[
            'title'=>$data['title'],
            'data'=>$data,
            'allow'=>$this->checkPostPermissions($data),
            'tags'=>app('\app\admin\model\Tag')->getNameList($id,'portal_'.$type),
            'comments'=>$comments,
            'isMore'=>(count($comments) > $perPage),
            'page'=>'',
            'bread'=>app('\app\portal\model\PortalCategory')->setType('portal_'.$type)->bread($data['category_id'],$type.'_list'),
        ],false);
        $this->views_click($id);
    }
    /**--------------------------------------------------
     * 下载次数增加1
     * @param int $id
     *---------------------------------------------------*/
    public function downloads_click($id=0){
        if($id==0)
            $id=(int)get('id','int',0);
        if($id<1) return;
        $model=app('\app\portal\model\PortalPost');
        $model->_exec('update table set `downloads`=`downloads`+1 where id= ?',[$id]);
    }
    /**--------------------------------------------------
     * 查看次数增加1
     * @param int $id
     *---------------------------------------------------*/
    protected function views_click($id=0){
        if($id==0)
            $id=(int)get('id','int',0);
        if($id<1) return;
        $model=app('\app\portal\model\PortalPost');
        $model->_exec('update table set `views`=`views`+1 where id= ?',[$id]);
    }
   /**--------------------------------------------------
     * 分类列表页
     *---------------------------------------------------*/
    public function article_list($slug){
        $this->getCategoryList($slug,'article','portal/article_list');
    }

    public function goods_list($slug){
        $this->getCategoryList($slug,'goods','portal/goods_list');
    }

    public function group_list($slug){
        $this->getCategoryList($slug,'group','group/group_list');
    }
    /** ------------------------------------------------------------------
     * 分类列表
     * @param string $slug 分类的slug
     * @param string $type 种类
     * @param string $tpl 模板名称
     *---------------------------------------------------------------------*/
    protected function getCategoryList($slug,$type,$tpl){
        $catModel=app('\app\portal\model\PortalCategory');
        $category=$catModel->eq('slug',$slug)->eq('type','portal_'.$type)->find(null,true);
        if(!$category) show_error('不存在的分类slug:'.$slug);
        $catModel->setType($category['type']);
        //$groupCateId=Conf::get('group_category_id','portal',false)?:'1';
        if ($type=='group'){
            //'where pid='.$category['pid'] .' and id<>'.$category['id']
            $randoms=$catModel->randomItem(10,['pid'=>$category['pid'],['id','ne',$category['id']]],'id,slug,name');
            $perPage=30;
        }else{
            $randoms='';
            $perPage=10;
        }
        $path=$catModel->bread($category['id'],$type.'_list');
        $postModel=app('\app\portal\model\PortalPost');
        $currentPage=get('page','int',1);

        $where=[['type','eq',$type],['status','eq',1],['category_id','eq',$category['id']]];
        $total = $postModel->count(['where'=>$where]);
        dump($postModel->getSql());
        if($total>0)
            $data=$postModel->search($where,[($currentPage-1)*$perPage,$perPage],'create_time desc,id desc');
        else
            $data=[];
        $url = url('@'.$type.'_list@',['slug'=>$slug]).'?page=(:num)';
        $page=new Paginator($total,$perPage,$currentPage,$url);
        $this->_display($tpl,[
            'title'=>$category['name'],
            'category'=>$category,
            'data'=>$data,
            'page'=>(string)$page,
            'bread'=>$path.'列表',
            'randoms'=>$randoms
        ],false);
    }

    /**--------------------------------------------------
     * 余部分类
     *---------------------------------------------------*/
    public function article_all(){
        $this->getCategoryAll('article','portal/article_all');
    }
    public function goods_all(){
        $this->getCategoryAll('goods','portal/goods_all');
    }
    public function group_all(){
        $this->getCategoryAll('group','group/group_all');
    }
    protected function getCategoryAll($type,$tpl){
        $currentPage=get('page','int',1);
        $perPage=20;
        $where=[['type','eq','portal_'.$type],['status','eq',1]];
        $data=[];
        $cateModel=app('\app\portal\model\PortalCategory');
        $data['total'] = $cateModel->count(['where'=>$where]);
        if($data['total'] >0){
            $data['data']=$cateModel->_where($where)->order('id')->limit(($currentPage-1)*$perPage,$perPage)->findAll(true);
            $url = url('@'.$type.'_list@').'?page=(:num)';
            $data['page']=(string)new Paginator($data['total'],$perPage,$currentPage,$url);
        }else{
            $data['page']='';
            $data['data']=[];
        }
        $this->_display($tpl,$data,false);
    }

    /** ------------------------------------------------------------------
     * 权限控制
     * @param array $data
     * @return array
     *---------------------------------------------------------------------*/
    protected function checkPostPermissions(&$data){
        if($this->_checkIsAdmin())
            return ['show'=>true,'download'=>true];
        $ret=[];
        switch ($data['type']){
            case 'article': //文章
                $ret['show']=$this->checkPostPermissions2($data);
                $ret['download']=true;
                break;
            case 'soft'://软件
            case 'goods'://虚拟商品,
                $ret['show']=true;
                $ret['download']=$this->checkPostPermissions2($data);
                break;
            case 'group':
                $ret['show']=true;
                $ret['download']=true;
                break;
            default:
                $ret['show']=false;
                $ret['download']=false;
        }
        return $ret;
    }

    protected function checkPostPermissions2(&$data){
        //Permissions字段种类：1 公开 2 money购买  3 coin购买  4、积分达到要求 5、vip用户
        $permissions=(int)$data['permissions'];
        switch ($permissions){
            case 1:
                return true;
            case 2:
            case 3:
                return $this->checkPostBuy($data['buy_uid']);
            case 4:
                return $this->checkUserScore();
            case 5:
                return $this->checkIsVip();
            default:
                return false;
        }
    }

    /**-------------------------------------------------
     * 检测当前用户是否已经购买
     * @param  string $buy_uid  当前文章的buy_uid字段的值
     * @return bool
    ----------------------------------------------------*/
    protected function checkPostBuy($buy_uid){
        if(!$this->_is_login()) return false;
        if($_SESSION['user']['gid']<10) return true;
        if($buy_uid){
            return in_array((string)$_SESSION['uid'],explode(',',$buy_uid));
        }
        return false;
    }
    //检测当前用户积分是否达到要求
    protected function checkUserScore(){
        if(!$this->_is_login()) return false;
        return false;
    }
    //检测当前用户是否是vip
    protected function checkIsVip(){
        if(!$this->_is_login()) return false;
        return false;
    }

    /** ------------------------------------------------------------------
     * 标签展示页
     * @param string $slug
     *--------------------------------------------------------------------*/
    public function tag($slug){
        $tagModel=app('\app\admin\model\Tag');
        $tag=$tagModel->select('id,name,create_time,seo_title,seo_keywords,seo_description,thumb,content,slug')->eq('slug',$slug)->eq('status',1)->find(null,true);
        if(!$tag)
            $this->show404();
        $cacheFile=ROOT.'/cache/html/tag/'.date('Y/m/d/i/',$tag['create_time']).$tag['id'].'.html';
        $is_cache=Conf::get('tag_cache','portal');
        $cacheTime=Conf::get('tag_cache_time','portal');
        if( $is_cache && $this->_readCache($cacheFile,$cacheTime)){
            return ;
        }
        //相关内容
        $index_name=Conf::get('index_tag','portal');
        $img=[];
        $data=[];
        $isOpenSearch=($index_name!=='');
        if($isOpenSearch){
            $sphinx=new \app\admin\other\Search();
            if(mb_strlen($tag['name'])<4){
                $sphinx->mode=2;
            }
            $sphinx->setSortMode(1,'source_id');
            $sphinx->setSortMode(1,'myorder');
            $data=$sphinx->query($tag['name'],$index_name,1,20,$total,function($value,$key)use (&$img){
                $data=[];
                $data['id']='';
                $bbsRouter=[1=>'bbs_post',2=>'bbs_show'];
                if($value['source_id'] >0){
                    $data['id']=($value['id']-2)/10;
                    $router=$value['type'];
                    $pindao=['article'=>'文章','goods'=>'商品'];
                    $data['pindao']=$pindao[$value['type']];
                }else{
                    $data['id']=($value['id']-1)/10;
                    $data['type']=(int)$value['type'];
                    $router=$bbsRouter[$data['type']];
                    $pindao=[1=>'讨论',2=>'问答'];
                    $data['pindao']=$pindao[$data['type']];
                }
                $data['url']=url('@'.$router.'@',['id'=>$data['id']]);
                if($value['thumb']){
                    //$img[]='<img src="'.$value['thumb'].'" alt="'.$value['title'].'">';
                    $img[$key]['src']=$value['thumb'];
                    $img[$key]['alt']=$value['title'];
                    $img[$key]['url']=$data['url'];
                }else{
                    //var_dump($value['content']);
                    if(@preg_match('%<img .*?src="([^"]+)"[^>]*>%i',$value['content'],$match)){
                        //$img[]='<img src="'.$match[1].'" alt="'.$value['title'].'">';
                        $img[$key]['src']=$match[1];
                        $img[$key]['alt']=$value['title'];
                        $img[$key]['url']=$data['url'];
                    }
                }
                $data['content']=\extend\Helper::text_cut($value['content'],130);
                return $data;
            });
            unset($sphinx);
        }
        if(!$isOpenSearch || !$data){
            $total=$tagModel->count([
                'from'=>'tag_relation',
                'where'=>['tid'=>$tag['id']]
            ]);
            if($total>0){
                $data1=$tagModel->getTagList($tag['id'],'portal_post',[['status','eq',1]],15,'create_time desc,id desc','id,title,thumb,content,excerpt,comments_num,views,create_time,o.type,1 as source_id');
                $data2=$tagModel->getTagList($tag['id'],'bbs',[['status','eq',1]],15,'create_time desc,id desc','id,title,thumb,content,excerpt,comments_num,views,create_time,o.type,0 as source_id');
                if($data1)
                    $data=$data1;
                if($data2)
                    $data=array_merge($data,$data2);
                unset($data2,$data1);
                if($data){
                    $bbsRouter=[1=>'bbs_post',2=>'bbs_show'];
                    foreach ($data as $i =>$item){
                        if($item['source_id'] >0 ){
                            $pindao=['article'=>'文章','goods'=>'商品'];
                            $router=$item['type'];
                            $data[$i]['pindao']=$pindao[$item['type']];
                        } else{
                            $data[$i]['type']=(int)$item['type'];
                            $router=$bbsRouter[ $data[$i]['type']];
                            $pindao=[1=>'讨论',2=>'问答'];
                            $data[$i]['pindao']=$pindao[$data[$i]['type']];
                        }
                        $data[$i]['url']=url('@'.$router.'@',['id'=>$item['id']]);
                        if($item['thumb']){
                            //$img[]='<img src="'.$item['thumb'].'" alt="'.$item['title'].'">';
                            $img[$i]['src']=$item['thumb'];
                            $img[$i]['alt']=$item['title'];
                            $img[$i]['url']=$data[$i]['url'];
                        }else{
                            if(@preg_match('%<img .*?src="([^"]+)"[^>]*>%i',$item['content'],$match)){
                                //$img[]='<img src="'.$match[1].'" alt="'.$item['title'].'">';
                                $img[$i]['src']=$match[1];
                                $img[$i]['alt']=$item['title'];
                                $img[$i]['url']=$data[$i]['url'];
                            }
                        }
                        $data[$i]['content']=\extend\Helper::text_cut($item['content'],130);
                    }
                }
            }
        }
        //$currentPage=get('page','int',1);
        //$perPage=Conf::get('bbs','site')['comment_perpage'];

        /*$url=url('bbs/post/caiji').'?page=(:num)';
        $page=(string) new Paginator($total,$perPage,$currentPage,$url,[
            'page'=>'laypage-main',
            'current'=>'laypage-curr',
            'next'=>'laypage-next',
            'disabled'=>'laypage-disabled'
        ]);*/
        $asin=[
            'title'=>$tag['name'],
            'tag'=>$tag,
            'data'=>$data,
            'images'=>$img,
            //'data1'=>$data1,
            //'data2'=>$data2,
            //'page'=>$page,
            'randomTags'=>$tagModel->getRandom(10,$tag['id']),
            'isOpenSearch'=>$isOpenSearch
        ];
        unset($data,$img);
        if($is_cache)
            $this->display($cacheFile,'portal/tag',$asin,false);
        else
            $this->_display('portal/tag',$asin,false);
    }

    /** ------------------------------------------------------------------
     * 所有标签集合页
     *--------------------------------------------------------------------*/
    public function tag_all(){
        $tagModel=app('\app\admin\model\Tag');
        $data=$tagModel->select('name,id,slug')->eq('status',1)->limit(300)->order('id desc')->findAll(true);
        $tag_all_name=Conf::get('tag_all_name','portal');
        $asin=[
            'title'=>$tag_all_name.'话题大全',
            'data'=>$data,
            //'page'=>$page,
        ];
        $this->_display('portal/tag_all',$asin,false);
    }
}
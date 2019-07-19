<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/


namespace app\video\ctrl;
use core\Ctrl;
use extend\Paginator;

class IndexCtrl extends Ctrl
{
    /*首页数据*/
    public function index(){
        $postModel=app('\app\portal\model\PortalPost');
        $data=[
            'articles'=>$postModel->search([['type','eq','article'],['status','eq',1]],10,'create_time desc,id desc'),
            //'groups'=>$postModel->from('category')->eq('pid',23)->order('counts desc,id desc')->limit(12)->findAll(true),
            'groups'=>[],
            'videos'=>$postModel->select('id,title,type,actor,thumb,score,director')->from('video')->limit(9)->order('recommended desc,id desc')->findAll(true),
        ];
        return $data;
        //return ['data_random'=>$model->getRandomItem(10,'<li><a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="{%url%}"><div class="box col-3"><img src="{%qun_qrcode%}"></div><div class="box col-9"><h3 class="f34">{%title%}</h3><p class="f30 color4">{%content%}…</p></div></a></li>')];
    }

    public function details($id){
        $id=(int)$id;
        if($id<1)
            show_error('输入不正确的id');
        $model=app('\app\video\model\Post');
        $data['data']=$model->getOne($id);
        if(!$data['data'])
            show_error('不存在的id');
        $data['comments']=[];
        $data['page']='';
        $perPage=20;
        //获取评论
        if($data['data']['comments_num'] >0){
            $data['comments']=app('\app\admin\model\Comment')->getSome(['table_name'=>'video','oid'=>$id],$perPage);
            //dump($data['comments']);
        }
        $data['title']=$data['data']['title'];
        $data['isMore']=(($data['comments']?count($data['comments']):0) > $perPage);
        $data['tags']=app('\app\admin\model\Tag')->getNameList($id,'video');
        $data['source']=$model->from('video_source')->eq('vid',$id)->findAll(true);
        $data['bread']=app('\app\video\model\Category')->bread($data['data']['category_id'],'video_list');
        $this->_display('video/details',$data,false);
    }

    public function play($vid,$id){
       $pid=$this->parseId($id);
       $vid=(int)$vid;
        if($vid==0||$id==0)
            show_error('vid或id格式不符');
        $data=[];
        $model=app('\app\video\model\Post');
        //查询id
        $data['play']=$model->from('video_source')->eq('id',$id)->find(null,true);
        if(!$data['play'])
            show_error('不存在的id');
        if(!$data['play']['url'])
            show_error('url为空');
        $data['play']['url']=explode("\n",$data['play']['url']);
        if(!isset($data['play']['url'][$pid]))
            show_error('不存在的pid');
        //查询vid
        $data['video']=$model->getOne($vid);
        if(!$data['video'])
            show_error('不存在的vid');
        //解析$
        $url2player=[];
        foreach ($data['play']['url'] as $i =>$item){
            $data['play']['url'][$i]=explode('$',$item);
            $url2player['quality'][]=[
                'name'=>$data['play']['url'][$i][0],
                'url'=>trim($data['play']['url'][$i][1]),
                'type'=>'hls'
            ];
        }
        $url2player['defaultQuality']=$pid;
        $data['extendData']=$model->extendData;
        $data['currentPlayUrl']=$data['play']['url'][$pid];
        //$data['title']=$data['video']['title'].$data['play']['url'][$pid][0].'免费在线播放_'.$data['playType'][$data['play']['type']].'_'.$data['play']['name'];
        $data['otherPlay']=$model->from('video_source')->eq('vid',$vid)->ne('id',$id)->findAll(true);
        $data['url2play']=$url2player;
        $data['title']=$data['video']['title'].$data['currentPlayUrl'][0];
        $this->_display('video/play',$data,false);
    }
    //解析id
    protected function parseId(&$id){
        if(preg_match('/\d+\-\d+/',$id)==0)
            show_error('id格式不符');
        list($id,$pid)=explode('-',$id);
        $id=(int)$id;
        return (int)$pid;
    }

    public function list($slug){
        $catModel=app('\app\video\model\Category');
        $data=[];
        $data['category']=$catModel->eq('slug',$slug)->eq('type','video')->find(null,true);
        if(!$data['category']) show_error('不存在的分类slug:'.$slug);
        $data['bread']=$catModel->bread($data['category']['id'],'video_list');
        unset($catModel);
        $postModel=app('\app\video\model\Post');
        $data['currentPage']=get('page','int',1);
        $perPage=20;
        $where=[['status','eq',1],['category_id','eq',$data['category']['id']]];
        $total = $postModel->count(['where'=>$where]);
        $data['data']=[];
        $data['page']='';
        $data['title']=$data['category']['name'];
        if($total>0){
            $data['data']=$postModel->search($where,[($data['currentPage']-1)*$perPage,$perPage],'create_time desc,id desc');
            $url = url('@video_list@',['slug'=>$slug]).'?page=(:num)';
            $data['page']=new Paginator($total,$perPage,$data['currentPage'],$url);
        }
        $this->_display('video/video_list',$data,false);
    }
}
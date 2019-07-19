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


namespace app\weixinqun\ctrl;

use core\Ctrl;
use core\Conf;
use extend\Paginator;

class XuexiaoCtrl extends Ctrl
{
    public function details($id){
        $id=(int)$id;
        if($id<1)
            show_error('输入不正确的id');
        $model=app('\app\admin\model\Comment');
        $data['data']=$model->from('xuexiao')->eq('id',$id)->find(null,true);
        if(!$data['data'])
            show_error('不存在的id');
        $data['comments']=[];
        $data['page']='';
        $perPage=20;
        //获取评论
        if($data['data']['comments_num'] >0){
            $data['comments']=$model->getSome(['c.status'=>1,'pid'=>0,'table_name'=>'xuexiao','oid'=>$id],$perPage);
        }
        //获取图集
        if($data['data']['thumb_ids']){
            $data['data']['thumb_ids']=$model->from('file')->in('id',explode(',',$data['data']['thumb_ids']))->order('id')->findAll(true);
        }
        $data['title']=$data['data']['title'].'微信群_同校交友群';
        $data['isMore']=(($data['comments']?count($data['comments']):0) > $perPage);
        $data['data']['city']=$model->from('city')->eq('id',$data['data']['city_id'])->find(null,true);
        $this->_display('weixinqun/xuexiao',$data,false);
    }

    /**--------------------------------------------------
     * 查看次数增加1
     * @param int $id
     *---------------------------------------------------*/
    protected function views_click($id=0){
        if($id==0)
            $id=(int)get('id','int',0);
        if($id<1) return;
        $model=app('\app\weixinqun\model\Xuexiao');
        $model->_exec('update table set `views`=`views`+1 where id= ?',[$id]);
    }

    //最新学校列表，让搜索引擎抓取
    public function zuixin(){
        $model=app('\core\Model');
        $total= $model->count([
            'from'=>'xuexiao',
            //'where'=>$where
        ]);
        $data['title']='最新学校列表';
        $data['data']=[];
        $data['page']='';
        if($total>0){
            $perPage=30;
            $currentPage=get('page','int',1);
            $data['data']=$model->from('xuexiao')->limit(($currentPage-1)*$perPage,$perPage)->order('id desc')->findAll(true);
            $url = url('weixinqun/xuexiao/zuixin').'?page=(:num)';
            $data['page']=new \extend\Paginator($total,$perPage,$currentPage,$url);
        }
        $this->_display('weixinqun/xuexiao_zuixin',$data,false);
    }
}
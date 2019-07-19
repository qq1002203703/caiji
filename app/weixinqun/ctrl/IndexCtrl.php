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

namespace app\weixinqun\ctrl;

use core\Conf;

class IndexCtrl extends \core\Ctrl
{
    /*首页数据*/
    public function index(){
        $model=app('\app\weixinqun\model\Weixinqun');
        return ['data_random'=>$model->getRandomItem(10,'<li><a class="w75 grid mb2 mt2 clearfix pr2 pl2" href="{%url%}"><div class="box col-3"><img src="{%qun_qrcode%}"></div><div class="box col-9"><h3 class="f34">{%title%}</h3><p class="f30 color4">{%content%}…</p></div></a></li>')];
    }
    //微信群
   public function weixinqun($id){
        $this->details($id,1,'weixinqun/weixinqun');
   }

   public function gongzhonghao($id){
       $this->details($id,3,'weixinqun/gongzhonghao');
   }

    /**------------------------------------------------------------------
     * 内容详情页
     * @param int $id:内容id
     * @param string $type :内容种类
     * @param string $tpl 模板文件名
     *--------------------------------------------------------------------*/
    protected function details($id,$type,$tpl){
        if($id<1)
            show_error('输入不正确的id');
        $cacheFile=ROOT.'/cache/html/weixinqun/'.get_path_from_id($id).'/'.$id.'.txt';
        $is_cache=app('config')::get('weixinqun_cache','site');
        if( $is_cache && $this->read_details_cache($cacheFile)){
             $this->views_click($id);
             return ;
         }
        $model=app('\app\weixinqun\model\Weixinqun');
        $data=$model->getOne('',[['id','eq',$id],['type','eq',$type]]);
        if(!$data)
            show_error('不存在的id');
        $data['city']=$model->getCityName($data['city_id']);
        $data['category']=$model->getCategoryName($data['category_id']);
        $data['tags']=$model->getTagName($data['id']);
        if($data['views'] < 50){
            $model->_exec('update table set `views`=?,`likes`=? where id= ?',[mt_rand(1000,1800),mt_rand(100,300),$data['id']],true);
        }
        $this->_assign([
            'title'=>$data['title'],
            'is_login'=>$this->_is_login(),
            'data'=>$data,
            'pre_next'=>$model->getPreNext([['type','eq',$type],['id','lt',$id]],[['type','eq',$type],['id','gt',$id]],'id desc',$type),
        ]);
        if($is_cache)
            $this->display($cacheFile,$tpl,[],false);
        else
            $this->_display($tpl,[],false);
        $this->views_click($id);
    }
    //分类列表页
    public function fenlei($id){
        $id=(int) $id;
        $city_id=get('city','int',0);
        $model=app('\app\weixinqun\model\Weixinqun');
        if($id<1){
            $cateName='全部';
        }else{
            $cateName=$model->getCategoryName($id);
        }
        if(!$cateName)
            show_error('不存在的分类');
        $currentPage=get('page','int',1);
        $perPage=10;
        $where1=$where2=[];
        $qs='';
        if($id>0){
            $where1[]=['category_id','=',$id];
            $where2['where'][]=['category_id','eq',$id];
        }
        $cityName='';
        if($city_id >0){
            $where1[]=['city_id','=',$id];
            $where2['where'][]=['city_id','eq',$id];
            $qs.='city='.$city_id.'&';
            $cityName=$model->getCityName($city_id);
            if(!$cityName)
                show_error('不存在的地区');
        }
        // is_top desc,recommended desc,create_time
        $total=$model->count($where2);
        $data=[];
        $page='';
        if($total>0){
            $data=$model->seachWeixinqun($where1,(($currentPage-1)*$perPage) .','.$perPage,'create_time desc,id desc');
            $url = url('@weixinqun_list@',['id'=>$id]).'?'.$qs.'page=(:num)';
            $page=new \extend\Paginator($total,$perPage,$currentPage,$url);
        }
        $this->_assign([
            'title'=>$cateName.'行业分类'.$cityName,
            'seo_title'=>$cateName.'行业分类'.$cityName.'微信群大全|微信群查找',
            'data'=>$data,
            'page'=>(string)$page,
            'seo_description'=>'你好这是'.$cateName.'行业分类'.$cityName.'微信群列表，下面列出了所有'.$cateName.'行业分类'.$cityName.'下的群大全，你可以在这里查找你需要的微信群，当然每个群的群主都无比欢迎你加入哦！'
        ]);
        $this->_display('weixinqun/weixinqun_list',[],false);
    }

    public function weixinqun_city($id){
        $this->city($id,1,'weixinqun/weixinqun_city');
    }
    public function xuexiao_city($id){
        $this->city($id,2,'weixinqun/xuexiao_city');
    }
    //地区列表页
    protected function city($id,$type,$tpl){
        $id=(int) $id;
        if($id<1)
            show_error('错误的地区');
        $cityModel=app('\app\admin\model\City');
        $data['city']=$cityModel->getById($id);
        if(!$data['city']){
            show_error('不存在的地区');
        }
        $data['cityChildren']=$cityModel->getChildren($id);
        $data['cityParent']=$cityModel->getById($data['city']['pid']);
        switch ($type){
            case 1://行业群
                $table='weixinqun';
                $routerName='@weixinqun_city@';
                $data['title']=($data['cityParent']?$data['cityParent']['name']:'').$data['city']['name'].'微信群大全';
                break;
            case 2://学校群
                $table='xuexiao';
                $routerName='@xuexiao_city@';
                $data['title']=($data['cityParent']?$data['cityParent']['name']:'').$data['city']['name'].'学校微信群大全';
                break;
            default:
                show_error('不存在的群类型');
        }

        $where=[['city_id','eq',$id]];
        $total= $cityModel->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $data['data']=[];
        $data['page']='';
        if($total>0){
            $perPage=10;
            $currentPage=get('page','int',1);
            $data['data']=$cityModel->from($table)->_where($where)->limit(($currentPage-1)*$perPage,$perPage)->order('create_time desc,id desc')->findAll(true);
            $url = url($routerName,['id'=>$id]).'?page=(:num)';
            $data['page']=new \extend\Paginator($total,$perPage,$currentPage,$url);
        }
        $this->_display($tpl,$data,false);
    }

    /**--------------------------------------------------
     * 查看次数增加1
     * @param int $id
     *---------------------------------------------------*/
    protected function views_click($id){
        //$id=get('id','int',0);
        if($id<1) return;
        $model=app('\app\weixinqun\model\Weixinqun');
        $model->_exec('update table set `views`=`views`+1 where id= ?',[$id],true);
    }

    /** ------------------------------------------------------------------
     * 读取详情页缓存内容
     * @param $cacheFile
     * @return bool
     *---------------------------------------------------------------------*/
    protected function read_details_cache($cacheFile){
        if(Conf::get('weixinqun_cache','site')=='0')
            return false;
        $cacheTime=(int) Conf::get('weixinqun_cache_time','site');
        // 检测：缓存是否存在并在有效期内
        if(\core\lib\cache\File::checkFile($cacheFile,$cacheTime)==false)
            return false;
        echo file_get_contents($cacheFile);
        return true;
    }


}
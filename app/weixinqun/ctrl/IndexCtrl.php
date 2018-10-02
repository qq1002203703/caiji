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

class IndexCtrl extends \core\Ctrl
{
    //微信群
   public function weixinqun($id){
        $this->details($id,1,'details');
   }

   public function gongzhonghao($id){
       $this->details($id,3,'gongzhonghao');
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
        $model=app('\app\weixinqun\model\Weixinqun');
        $data=$model->getOne('',[['id','eq',$id],['type','eq',$type]]);
        if(!$data)
            show_error('不存在的id');
        //缓存文件完整路径
        $cacheFile=ROOT.'/cache/html/weixinqun'.date('/Y/m/d/i/',$data['create_time']).$id.'.html';
        $is_cache=app('config')::get('weixinqun_cache','site');
        if( $is_cache && $this->read_details_cache($cacheFile)){
            $this->views_click($id);
            return ;
        }
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
            $this->display($cacheFile,$tpl);
        else
            $this->_display($tpl);
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
        $perPage=36;
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
        $data=$model->seachWeixinqun($where1,(($currentPage-1)*$perPage) .','.$perPage,'create_time desc,id desc');
        $total=$model->count($where2);
        $url = url('@fenlei@',['id'=>$id]).'?'.$qs.'page=(:num)';
        $page=new \extend\Paginator($total,$perPage,$currentPage,$url);
        $this->_assign([
            'title'=>$cateName.'行业分类'.$cityName,
            'seo_title'=>$cateName.'行业分类'.$cityName.'微信群大全|微信群查找',
            'data'=>$data,
            'page'=>(string)$page,
            'seo_description'=>'你好这是'.$cateName.'行业分类'.$cityName.'微信群列表，下面列出了所有'.$cateName.'行业分类'.$cityName.'下的群大全，你可以在这里查找你需要的微信群，当然每个群的群主都无比欢迎你加入哦！'
        ]);
        $this->_display('list');
    }
    //地区列表页
    public function diqu($id){
        $id=(int) $id;
        $category_id=get('fenlei','int',0);
        $model=app('\app\weixinqun\model\Weixinqun');
        if($id<1){
            $cityName='全部地区';
        }else{
            $cityName=$model->getCityName($id);
        }
        if(!$cityName)
            show_error('不存在的地区');
        $currentPage=get('page','int',1);
        $perPage=36;
        $where1=$where2=[];
        $qs='';
        if($id>0){
            $where1[]=['city_id','=',$id];
            $where2['where'][]=['city_id','eq',$id];
        }
        $cateName='';
        if($category_id >0){
            $where1[]=['category_id','=',$id];
            $where2['where'][]=['category_id','eq',$id];
            $qs.='fenlei='.$category_id.'&';
            $cateName=$model->getCategoryName($category_id);
            if(!$cateName)
                show_error('不存在的分类');
            else
                $cateName='且所属行业为'.$cateName;
        }
        // is_top desc,recommended desc,create_time
        $data=$model->seachWeixinqun($where1,(($currentPage-1)*$perPage) .','.$perPage,'create_time desc,id desc');
        $total=$model->count($where2);
        $url = url('@diqu@',['id'=>$id]).'?'.$qs.'page=(:num)';
        $page=new \extend\Paginator($total,$perPage,$currentPage,$url);
        $this->_assign([
            'title'=>$cityName.$cateName,
            'seo_title'=>$cityName.$cateName.'微信群大全|微信群查找',
            'data'=>$data,
            'page'=>(string)$page,
            'seo_description'=>'你好这是'.$cityName.$cateName.'微信群列表，下面列出了所有'.$cityName.$cateName.'下的群大全，你可以在这里查找你需要的微信群，当然每个群的群主都无比欢迎你加入哦！'
        ]);
        $this->_display('list');
    }

    public function tags($name){
        $name=urldecode($name);
        echo $name;
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
        $config=app('config');
        if($config::get('weixinqun_cache','site')=='0')
            return false;
        $cacheTime=(int) $config::get('weixinqun_cache_time','site');
        // 检测：缓存是否存在并在有效期内
        if(\core\lib\cache\File::checkFile($cacheFile,$cacheTime)==false)
            return false;
        echo file_get_contents($cacheFile);
        return true;
    }
    public function test(){
        //dump(app('\app\weixinqun\model\Weixinqun'));
        //dump(app('\app\weixinqun\model\Weixinqun'));
    }

}
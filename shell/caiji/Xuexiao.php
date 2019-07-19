<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 批处理工具
 * ======================================*/


namespace shell\caiji;
use extend\Helper;
use extend\Selector;
use shell\CaijiCommon;

class Xuexiao extends CaijiCommon
{
    public function __construct($param=[])
    {
        parent::__construct($param);
    }


    /**------------------------------------------------------------------------------------------------
     * 51sxue.com,地区入库
     */
    public function area(){
        //$str=file_get_contents(ROOT.'/data/xuexiao_area.txt');
        $str=$this->http('http://xuexiao.51sxue.com/');
        //$match=Selector::find($str,'regex,multi','%<a href="http://xuexiao.51sxue.com/schoolByArea/areaCodeS_(?P<from_id>\d+)_t_2.html" title="[^"]+">(?P<name>.+)</a>%','name,from_id','');
        $match=Selector::find($str,'regex,multi','%<li><a href="http://xuexiao\.51sxue\.com/slist/\?areaCodeS=(?P<from_id>\d+)"[^>]*>(?P<name>[^<]+)</a></li>%','name,from_id','<div class="city_con">{%|||%}<div class="year_city_r">');
        //dump($match);
        if(!$match){
            echo '无法转成数组';
            return;
        }
        $error_arr=[];
        foreach ($match as $key => $item){
            //查询对应的1级省市
            $data=$this->model->from('caiji_51sxue_area')->select('name,id,level')->eq('level',2)->like('name',$item['name'].'%')->findAll(true);
            if(!$data){
                $error_arr[]=[
                    'name'=>$item['name'],
                    'from_id'=>$item['from_id'],
                    'msg'=>'找不到对应的区域',
                ];
                continue;
            }
            $n=count($data);
            if($n>1){
                $error_arr[]=[
                    'name'=>$item['name'],
                    'from_id'=>$item['from_id'],
                    'multi'=>$data,
                    'msg'=>'找到多个区域'
                ];
                continue;
            }
            //入库
            if($this->model->from('caiji_51sxue_area')->eq('id',$data[0]['id'])->update([
                'from_id'=>$item['from_id'],
                'isdo'=>1
            ]))
                echo $item['name'].'=>'.$item['from_id'].' 入库成功'.PHP_EOL;
            else
                echo $item['name'].'=>'.$item['from_id'].' 入库失败'.PHP_EOL;
        }
        dump($error_arr);
    }
    /**
     * 51sxue.com,采集page页
     */
    public function page(){
        $caijiRule=$this->getCaijiRules('51sxue','page','');
        //dump($caijiRule);
        if($caijiRule===false){
            echo '规则名不正确'.PHP_EOL;
            return 1;
        }
        /*$caijiRule['url']='http://xuexiao.51sxue.com/slist/?areaCodeS=0&page=4999';
        $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
        $callback->start();
        exit();*/
       $data=$this->model->from('caiji_51sxue_list')->eq('isdo',0)->order('id')->findAll(true);
        foreach ($data as $item){
            $caijiRule['url']=$item['url'];
            $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
            if(is_object($callback)) {
                $callback->start();
                $this->model->from('caiji_51sxue_list')->eq('id',$item['id'])->update(['isdo'=>1]);
            }else{
                //$this->outPut(' '.$callback.',pageId:'.$item['id'].PHP_EOL,true);
                echo '无法访问'.PHP_EOL;
            }
        }
        return 0;
    }

    public function getlist(){
        $data=$this->model->from('caiji_51sxue_area')->eq('level',1)->findAll(true);
        foreach ($data as $item){
            $url='http://xuexiao.51sxue.com/slist/?areaCodeS='.$item['from_id'];
            echo '---正在处理：'.$item['name'].','.$item['id'].'=>'.$url.PHP_EOL;
            $html=$this->http($url);
            $page_count=Selector::find($html,'regex,cut','<span class="down">共{%|||%}</span>');
            $page_count=(int) $page_count;
            $new_url=($page_count > 1) ? $url.'&page={%0,1,'.$page_count.',1,1,0%}' : $url;
            if($this->model->from('caiji_51sxue_list')->insert([
                'url'=>$new_url,
                'page_count'=>$page_count,
                'from_id'=>$item['from_id'],
                'isend'=>1,
            ]))
                echo '  成功添加地址: '.$new_url.' -----'.PHP_EOL;
            else
                echo '  添加地址失败'.PHP_EOL;
            msleep(1000,3000);
        }
    }
    //page页结果检测插件
   static public function check_result_page($html){
        $res=Selector::find($html,'regex,cut','<span>所有学校</span>{%|||%}<p>');
        return $res !=='';
   }

    public function getlist_comment(){
        $where=[['level','gt',1],['from_id','gt',0],['isdo','eq',0]];
        $count=$this->model->count(['from'=>'caiji_51sxue_area','where'=>$where]);
        if($count<=0){
            echo '没有需要处理的数据';
            return;
        }
        $this->doLoop($count,function ($perPage,$i) use ($where){
            return $this->model->from('caiji_51sxue_area')->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key){
            //http://pinglun.51sxue.com/commentList/areaCodeS_3204.html
            $url='http://pinglun.51sxue.com/commentList/areaCodeS_'.$item['from_id'];
            echo '---正在处理：'.$item['name'].','.$item['id'].'=>'.$url.PHP_EOL;
            $html=$this->http($url.'.html');
            $page_count=Selector::find($html,'regex,cut','<span class="down">共{%|||%}</span>');
            //dump($page_count);
            //return ;
           $page_count=(int) $page_count;
            $new_url=($page_count > 1) ? ($url.'_page_{%0,1,'.$page_count.',1,1,0%}.html') : ($url.'.html');
            if($this->model->from('caiji_51sxue_comment_list')->insert([
                'url'=>$new_url,
                'page_count'=>$page_count,
                'from_id'=>$item['from_id'],
                'isend'=>1,
            ])){
                echo '  成功添加地址: '.$new_url.' -----'.PHP_EOL;
                $this->model->from('caiji_51sxue_area')->where('id='.$item['id'])->update(['isdo'=>1]);
            }

                else
                    echo '  添加地址失败'.PHP_EOL;
            msleep(1000,3000);
        });
    }

    /**
     * 51sxue.com,采集page comment页
     */
    public function page_comment(){
        $caijiRule=$this->getCaijiRules('51sxue','page','');
        //dump($caijiRule);
        if($caijiRule===false){
            echo '规则名不正确'.PHP_EOL;
            return 1;
        }
        /*dump($caijiRule);
        exit();*/
        $where=[['isend','eq',1],['isdo','eq',0]];
        $count=$this->model->count(['from'=>'caiji_51sxue_comment_list','where'=>$where]);
        if($count<=0){
            echo '没有需要处理的数据';
            return -1;
        }
        /*$caijiRule['url']='http://pinglun.51sxue.com/commentList/areaCodeS_6403.html';
        $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
        $callback->start();
        exit();*/
        $this->doLoop($count,function ($perPage,$i) use ($where){
            return $this->model->from('caiji_51sxue_comment_list')->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key)use ($caijiRule){
            $caijiRule['url']=$item['url'];
            $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
            if(is_object($callback)) {
                $callback->start();
                $this->model->from('caiji_51sxue_comment_list')->eq('id',$item['id'])->update(['isdo'=>1]);
            }else{
                //$this->outPut(' '.$callback.',pageId:'.$item['id'].PHP_EOL,true);
                echo '无法访问'.PHP_EOL;
            }
        });
        return 0;
    }

    public function content(){
        $this->caiji('51sxue_diqu','content');
    }

    public function download(){
        $this->caiji('51sxue','download');
    }

    protected function caiji_test($type='content',$config='51sxue'){
        $caijiRule=$this->getCaijiRules($config,$type,'');
        if($caijiRule===false){
            echo '规则名不正确'.PHP_EOL;
            return 1;
        }
        $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
          if(is_object($callback)){
              $callback->doTest([
                  'url'=>'http://xuexiao.51sxue.com/detail/id_77973.html',
                  'from_id'=>8314
              ]);
          }else{
              dump($callback);
          }
          return 0;
    }

    static public function getCommentUrl($v){
        $v['url']='http://pinglun.51sxue.com/school/comment/id_'.$v['from_id'].'.html';
        return $v;
    }

    public function comment(){
        $this->caiji('51sxue_comment','content');
    }

    static public function commentSave($data){
        unset($data['iscaiji']);
        unset($data['isdownload']);
        unset($data['times']);
        $id=$data['id'];
        unset($data['id']);
        $data['isdone']=1;
        app('\core\Model')->from('caiji_51sxue')->eq('id',$id)->update($data);
    }

	public function fabu(){
        $this->caiji('51sxue','fabu');
    }



    // ---后期处理---------------------------------------------------------------------------------------------------

    /** ------------------------------------------------------------------
     * 处理地区的对应关系
     *---------------------------------------------------------------------*/
    public function do_area(){
        $table='caiji_51sxue';
        $where=[['isdone','eq',0]];
        $counts=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        if($counts<1){
            echo '没有需要处理的数据了'.PHP_EOL;
            return;
        }
        $this->doLoop($counts,function ($perPage,$i) use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table){
            echo '------------开始处理：'.$item['id'].'=>'.$item['diqu'].'-------------'.PHP_EOL;
            $update=[];
            $error=[];
            if($item['diqu']){
                $item['diqu']=preg_replace('#\{%\|\|\|%\}\d+\{%\|\|%\}市辖区#','',$item['diqu']);
                $arr=explode('{%|||%}',$item['diqu']);
                $count1=count($arr);
                $diqu=explode('{%||%}',$arr[$count1-1]);
                //查from_id
                $data=$this->model->from('caiji_51sxue_area')->eq('from_id',$diqu[0])->order('id')->findAll(true);
                if($data){
                    $count2=count($data);
                    $update['city_id']=$data[$count2-1]['id'];
                }else{
                    //查name
                    $diqu[1]=preg_replace('/(\s|　)+/','',$diqu[1]);
                    $data=$this->model->from('caiji_51sxue_area')->like('name','%'.$diqu[1].'%')->findAll(true);
                    /*echo $this->model->getSql().PHP_EOL;
                    dump($diqu);
                    dump($data);exit();*/
                    if($data){
                        $count2=count($data);
                        if($count2==1){
                            $update['city_id']=$data[0]['id'];
                        }else{
                            if($count1 >1){
                                $parent_diqu=explode('{%||%}',$arr[$count1-2]);
                                $data2=[];
                                $parent_diqu[1]=str_replace('市','',$parent_diqu[1]);
                                foreach ($data as $v){
                                    if(strpos($v['merger_name'],$parent_diqu[1])!==false){
                                        $data2[]=$v;
                                    }
                                }
                                $count3=count($data2);
                                if($count3==0){
                                    $error['msg']='从name2找不到地区';
                                    $error['diqu']=$data;
                                } elseif($count3==1) {
                                    $update['city_id'] = $data2[0]['id'];
                                }else{
                                    $error['msg']='多个地区:从name2';
                                    $error['diqu']=$data2;
                                }
                            }else{
                                $error['msg']='多个地区:从name';
                                $error['diqu']=$data;
                            }
                        }
                    }else{
                        $data=$this->model->from('caiji_51sxue_area')->like('old_name','%'.$diqu[1].'%')->findAll(true);
                        if($data){
                            $count2=count($data);
                            if($count2==1){
                                $update['city_id']=$data[0]['id'];
                            }else{
                                $error['msg']='多个地区:从old_name';
                                $error['diqu']=$data;
                            }
                        }else{
                            $error['msg']='找不到对应的';
                            $error['diqu']='';
                        }
                    }
                }
                if($error){
                    dump($error);
                    exit();
                    $this->model->from($table)->eq('id',$item['id'])->update([
                        'isdone'=>3,
                        'more'=>json_encode($error),
                    ]);
                    return;
                }
                $update['isdone']=1;
                if($this->model->from($table)->eq('id',$item['id'])->update($update)){
                    echo '  成功更新=>'.$item['id'].'-----'.PHP_EOL;
                }else{
                    echo '  更新失败=>'.$item['id'].'-----'.PHP_EOL;
                }
            }else{
                echo '  地区为空'.PHP_EOL;
                $this->model->from($table)->eq('id',$item['id'])->update(['isdone'=>2]);
            }
        });
    }

    /** ------------------------------------------------------------------
     * 第二种处理地区的对应关系
     * $update['diqu_type']=1;//单层没找到from_id
     * $update['diqu_type']=2;//多层找不到父级
     * $update['diqu_type']=3; //没去掉（市、县、区、省）找到多个name
     * $update['diqu_type']=4; //name去掉（市、县、区、省）也没找到
     * $update['diqu_type']=5; //去掉（市、县、区、省）找到多个name
     * $update['diqu_type']=6;//原数据没有地区
     *---------------------------------------------------------------------*/
    public function do_area1(){
        $table='caiji_51sxue';
        $where=[['isdone','eq',0]];
        $counts=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->dieEcho($counts<1,'没有需要处理的数据了'.PHP_EOL);

        $this->doLoop($counts,function ($perPage,$i) use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table) {
            echo '------------开始处理：' . $item['id'] . '=>' . $item['diqu'] . '-------------' . PHP_EOL;
            $update = [];
            if ($item['diqu']) {
                $item['diqu']=preg_replace('#\{%\|\|\|%\}\d+\{%\|\|%\}市辖区#','',$item['diqu']);
                $arr = explode('{%|||%}', $item['diqu']);
                $count1 = count($arr);
                $diqu = explode('{%||%}', $arr[$count1 - 1]);
                $data = $this->model->from('caiji_51sxue_area')->eq('from_id', $diqu[0])->order('id desc')->find(null, true);
                if ($data) {
                    $update['city_id'] = $data['id'];
                } else {
                    if ($count1 == 1) {
                        $update['diqu_type'] = 1;//单层没找到from_id
                        echo '  //单层没找到from_id' . PHP_EOL;
                    } else {
                        $diqu2 = explode('{%||%}', $arr[$count1 - 2]);
                        $data2 = $this->model->from('caiji_51sxue_area')->eq('from_id', $diqu2[0])->order('id desc')->find(null, true);
                        if ($data2) {
                            $update['diqu_p'] = $data2['id'];
                            $diqu[1] = preg_replace('/[\s　]+/', '', $diqu[1]);
                            $data3 = $this->model->from('caiji_51sxue_area')->eq('pid', $data2['id'])->like('name', '%' . $diqu[1] . '%')->findAll(true);
                            if ($data3) {
                                $count3 = count($data3);
                                if ($count3 == 1) {
                                    $update['city_id'] = $data3[0]['id'];
                                } else {
                                    $update['diqu_type'] = 3; //没去掉（市、县、区、省）找到多个name
                                    echo '  //没去掉（市、县、区、省）找到多个name' . PHP_EOL;
                                }
                            } else {
                                //去掉市、县、区、省再去找
                                $diqu[1] = str_replace(['市', '县', '区', '省'], '', $diqu[1]);
                                $data3 = $this->model->from('caiji_51sxue_area')->eq('pid', $data2['id'])->like('name', '%' . $diqu[1] . '%')->findAll(true);
                                if ($data3) {
                                    $count3 = count($data3);
                                    if ($count3 == 1) {
                                        $update['city_id'] = $data3[0]['id'];
                                    } else {
                                        $update['diqu_type'] = 5; //去掉（市、县、区、省）找到多个name
                                        echo '  //去掉（市、县、区、省）找到多个name' . PHP_EOL;
                                    }
                                } else {
                                    $update['diqu_type'] = 4; //name去掉（市、县、区、省）也没找到
                                    echo '  //name去掉（市、县、区、省）也没找到' . PHP_EOL;
                                }
                            }
                        } else {
                            $update['diqu_type'] = 2;//找不到父级
                            echo '  //找不到父级' . PHP_EOL;
                        }
                    }
                }
            }else{
                $update['diqu_type'] = 6;//原数据没有地区
                echo '  //原数据没有地区' . PHP_EOL;
            }
            $update['isdone']=1;
            if($this->model->from($table)->eq('id',$item['id'])->update($update)){
                echo '  成功更新=>'.$item['id'].'-----'.PHP_EOL;
            }else{
                echo '  更新失败=>'.$item['id'].'-----'.PHP_EOL;
            }
            //exit();
        });
    }

    /** ------------------------------------------------------------------
     * 处理下载项:删除太多的图片，只保留7张
     *---------------------------------------------------------------------*/
    public function do_down(){
        $table='caiji_51sxue';
        $table_down='caiji_51sxue_download';
        $where=[['isdone','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->dieEcho($total<1,'没有需要处理的数据了'.PHP_EOL);

        $this->doLoop($total,function ($perPage,$i) use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key) use ($table,$table_down) {
            echo '------------开始处理：' . $item['id'] . '-------------' . PHP_EOL;
            $data_update=[];
            //查询下载项
            $data_down=$this->model->from($table_down)->eq('cid',$item['id'])->findAll(true);
            if($data_down){
                $num=count($data_down);
                if($num >7){
                    $data_update['photo']='';
                    shuffle($data_down);
                    foreach ($data_down as $k =>$value){
                        if($k >6){//不需要的项
                            //删除文件
                            if($value['status']==1){
                                $file=ROOT.'/'.$value['save_path'];
                               if(is_file($file)){
                                    if(unlink($file))
                                        echo '  成功->删除文件：'.$file.PHP_EOL;
                                    else
                                        echo '  失败->删除文件：'.$file.PHP_EOL;
                                }
                            }
                            //删除download数据库对应项
                            if($this->model->from($table_down)->eq('id',$value['id'])->delete()){
                                echo '  成功->删除download数据中的'.$value['id'].PHP_EOL;
                            }else
                                echo '  失败->删除download数据中的'.$value['id'].PHP_EOL;
                        }else{//需要的项
                            if($value['status']==1){
                                if($value['replace_path'])
                                    $data_update['photo'].=$value['replace_path'].'{%|||%}';
                            }else{
                                //格式：{%@photo:1@%}
                                $data_update['photo'].='{%@'.$value['type'].'@%}{%|||%}';
                            }
                        }
                    }
                    $data_update['photo']=preg_replace('/\{%\|\|\|%\}$/','',$data_update['photo']);
                }
            }else{//查询不到data_down
                $data_update['photo']='';
                $data_update['isdownload']=1;
            }
            $data_update['isdone']=1;
            if($this->model->from($table)->eq('id',$item['id'])->update($data_update)){
                echo '  成功->更新：'.$item['id'].PHP_EOL;
            }else{
                echo '  失败->更新：'.$item['id'].PHP_EOL;
            }
        });
    }

    /** ------------------------------------------------------------------
     * 处理下载项:删除太多的图片，只保留尺寸最大的5张
     *---------------------------------------------------------------------*/
    public function do_down2(){
        $table='caiji_51sxue';
        $table_down='caiji_51sxue_download';
        $where=[['isdone','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->dieEcho($total<1,'没有需要处理的数据了'.PHP_EOL);

        $this->doLoop($total,function ($perPage,$i) use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key) use ($table,$table_down) {
            echo '------------开始处理：' . $item['id'] . '-------------' . PHP_EOL;
            $data_update=[];
            //$item=$this->model->from($table)->eq('id',18)->find(null,true);
            if($item['photo']){
                $photos=explode('{%|||%}',$item['photo']);
                $count=count($photos);
                if($count >5){
                    $photosInfo=[];
                    $photoIndex=[];
                    foreach ($photos as $i =>$photo){
                        $info=@getimagesize(ROOT.'/public'.$photo);
                        if($info){
                            $photosInfo[$i]=$info[0]*$info[1];
                        }else{
                            $photosInfo[$i]=0;
                        }
                        $photoIndex[$photo]=$photosInfo[$i];
                    }
                    sort($photosInfo);
                    $photoKey=[];
                    if($keyTmp=array_search($photosInfo[0],$photoIndex,true)){
                        $photoKey[]=$keyTmp;
                    }
                    if($count>6){
                        echo '>6'.PHP_EOL;
                        if($keyTmp=array_search($photosInfo[1],$photoIndex,true)){
                            $photoKey[]=$keyTmp;
                        }
                    }
                    foreach ($photoKey as $value){
                        unset($photoIndex[$value]);
                        $file=ROOT.'/public'.$value;
                        if(is_file($file) && unlink($file)){
                            echo '  删除了文件'.$file.PHP_EOL;
                        }

                    }
                    $data_update['photo']=implode('{%|||%}',array_keys($photoIndex));
                }
            }
            $data_update['isdone']=1;
            //dump($data_update);
            if($this->model->from($table)->eq('id',$item['id'])->update($data_update)){
                echo '  成功 更新=>'.$item['id'].PHP_EOL;
            }else{
                echo '  失败 更新=>'.$item['id'].PHP_EOL;
            }
        });
    }

}
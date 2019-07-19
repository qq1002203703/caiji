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



namespace shell\caiji;
use extend\Helper;
use extend\Selector;
use shell\CaijiCommon;

class Kebao5 extends CaijiCommon
{
    protected $task=[
        0=>[
            'cate_table'=>'caiji_kebao5_category',
            'cate_url'=>'http://www.kebao5.com/forum.php?mod=forumdisplay&fid=156',
            'cate_name'=>'淘宝',
            //'cate_name2'=>'潭州电商学院',
            'rule_name'=>'kebao5',
        ]
    ];

    public function all(){
        $this -> cate();
        $this->cate_count();
        $this->cate_page();
    }

    //分类url获取：获取分类1的url及相相关参数
    public function cate(){
        $taskId=0;
        $str=$this->http($this->task[$taskId]['cate_url']);
        //$match=Selector::find($str,'regex,multi','%<dt><a href="(?P<url>forum\.php\?mod=forumdisplay&fid=(?P<from_id>\d+))">(?P<name>[^<]+?)</a>%','url,name,from_id','<h2><a href="forum.php?gid=83"{%|||%}<h2><a href="forum.php?gid=1"');
        $match=Selector::find($str,'regex,multi','%<dt><a href="(?P<url>forum\.php\?mod=forumdisplay&fid=(?P<from_id>\d+))"[^>]*>(?P<name>[^<]+?)</a>%','url,name,from_id','<h2>子版块</h2>{%|||%}<!--[diy=diy4]-->');
        //dump(Selector::getError());
        //淘宝<h2><a href="forum.php?gid=1{%|||%}<h2><a href="forum.php?gid=78
        $this->dieEcho((!$match),'采集失败'.PHP_EOL);
        $error_arr=[];
        foreach ($match as $key => $item){
            $data=$this->model->from($this->task[$taskId]['cate_table'])->select('name,id')->eq('from_id',$item['from_id'])->findAll(true);
            if($data){
                $error_arr[]=[
                    'name'=>$item['name'],
                    'from_id'=>$item['from_id'],
                    'url'=>$item['url'],
                    'msg'=>'已经存在的from_id',
                ];
                continue;
            }
            $item['url']='http://www.kebao5.com/'.$item['url'];
            $item['category']=$this->task[$taskId]['cate_name'];
            $item['from_id']=(int)$item['from_id'];
            $item['iscaiji']=0;
            if(isset($this->task[$taskId]['cate_name2']) && $this->task[$taskId]['cate_name2']){
                $item['name'] =$this->task[$taskId]['cate_name2'].'{||}'.$item['name'];
            }
            //入库
            if($this->model->from($this->task[$taskId]['cate_table'])->insert($item))
                echo $item['name'].'=>'.$item['from_id'].' 入库成功'.PHP_EOL;
            else{
                echo $item['name'].'=>'.$item['from_id'].' 入库失败'.PHP_EOL;
                exit();
            }
        }
        dump($error_arr);
    }

    //分类页数的获取:获取分类列表的页数
    public function cate_count(){
        $where=[['iscount','eq',0]];
        $taskId=0;
        $count=$this->model->count(['from'=>$this->task[$taskId]['cate_table'],'where'=>$where]);
        $this->dieEcho( $count<=0 ,'没有需要处理的数据'.PHP_EOL);
        $this->doLoop($count,function ($perPage,$i) use ($where,$taskId){
            return $this->model->from($this->task[$taskId]['cate_table'])->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key)use ($taskId){
            $item['url']='http://www.kebao5.com/forum.php?mod=forumdisplay&fid='.$item['from_id'];
            echo '---正在处理：'.$item['name'].','.$item['id'].'=>'.$item['url'].PHP_EOL;
            $html=$this->http($item['url']);
            $page_count=trim(Selector::find($html,'regex,cut','<span title="共{%|||%}页">'));
            //dump($page_count);
            //exit();
            $page_count=(int) $page_count;
            $new_url=($page_count > 1) ? ($item['url'].'&page={%0,1,'.$page_count.',1,1,0%}') : ($item['url']);
            if($this->model->from($this->task[$taskId]['cate_table'])->eq('id',$item['id'])->update([
                'url'=>$new_url,
                'page_count'=>$page_count,
                //'from_id'=>$item['from_id'],
                'iscount'=>1,
            ])){
                echo '  更新成功: '.$new_url.' -----'.PHP_EOL;
            }
            else
                echo '  更新失败'.PHP_EOL;
        });
    }

   //内页url的获取：对每个分类列表页进行采集，从而获取内页的url和其他相关数据
    public function cate_page(){
        $taskId=0;
        $caijiRule=$this->getCaijiRules($this->task[$taskId]['rule_name'],'page','');
        $this->dieEcho( $caijiRule===false ,'规则名不正确'.PHP_EOL);
        //测试
        //$this->pageTest($caijiRule,'http://www.kebao5.com/forum.php?mod=forumdisplay&fid=77&page=1');

        $where=[['iscaiji','eq',0]];
        $count=$this->model->count(['from'=>$this->task[$taskId]['cate_table'],'where'=>$where]);
        $this->dieEcho( $count<=0 ,'没有需要处理的数据'.PHP_EOL);

        $this->doLoop($count,function ($perPage,$i) use ($where,$taskId){
            return $this->model->from($this->task[$taskId]['cate_table'])->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key)use ($caijiRule,$taskId){
            echo '---正在处理分类：'.$item['id'].','.$item['name'].'=>'.$item['url'].PHP_EOL;
            $caijiRule['url']=$item['url'];
            $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
            if(is_object($callback)) {
                $callback->start(['category_id'=>$item['id']]);
                $this->model->from($this->task[$taskId]['cate_table'])->eq('id',$item['id'])->update(['iscaiji'=>1]);
            }else{
                echo '回调页面[page]采集类失败'.PHP_EOL;
                exit();
            }
        });
    }


}
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


namespace shell\tools;
//use extend\Selector;
use shell\BaseShell;
//use Xparse\ElementFinder\ElementFinder;
class Test extends BaseShell
{
    protected $path=ROOT.'/shell/tools/test';
    protected $param;
    public function __construct($opt)
    {
        parent::__construct();
        $this->param=$opt;
    }

    public function start(){
        //echo date('Y-m-d H:i',1229353020);return ;
        if($this->param){
            switch ($this->param[0]){
                case 'min':
                    $caiji=new \core\caiji\normal\Dodo([]);
                    $caiji->start();
                    return ;
                case 'max':
                    $caiji=new \core\caiji\normal\Dodo([]);
                    $caiji->run2();
                    return ;
                case 'tool2':
                    $model=app('\core\Model');
                    $model->table='zuanke8';
                    while (true){
                        $data=$model->eq('iscaiji',1)->eq('isdone',0)->limit(50)->findAll(true);
                        if($data){
                            foreach ($data as $v){
                                echo $v['id'].'=>'.$v['title'].'-------------------'.PHP_EOL;
                                $in=[];
                                if($v['content']){
                                    $posion=strpos($v['content'],'{%|||%}');
                                    if($posion===false){
                                        $str=$v['content'];
                                    }else{
                                        $str=mb_strcut($v['content'],0,$posion);
                                    }
                                    list($time,$user)=explode('{%||%}',$str);
                                    if($user=='赚小客'){
                                        $in['isend']=1;
                                        $in['islaji']=1;
                                        $in['content']='';
                                    }
                                }
                                $in['isdone']=1;
                                $model->eq('id',$v['id'])->update($in);
                            }
                        }else{
                            break;
                        }
                    }
                    return;
                case 'tool':
                    $model=app('\core\Model');
                    $model->table='zuanke8';
                    while (true){
                        $data=$model->eq('iscaiji',1)->eq('isdone',0)->limit(30)->findAll(true);
                        if($data){
                            foreach ($data as $v){
                                echo $v['id'].'-------------------'.PHP_EOL;
                                $in=[];
                                //检测是否是需要权限
                                if(strpos($v['content'],'游客，本帖隐藏的内容需要积分')!==false){
                                    $in['content']='';
                                    $in['islaji']=1;
                                    $in['isend']=1;
                                    $in['login']=1;
                                }else{
                                    $in['content']=preg_replace('/\{:[^:\}]*:\}/','',str_replace('赚客吧','u惠吧',$v['content']));
                                }
                                $in['isdone']=1;
                                $model->eq('id',$v['id'])->update($in);
                            }
                        }else{
                            break;
                        }
                    }
                    return;
            }
        }
        echo '参数不正确'.PHP_EOL;
    }
    public function outPut($msg, $important)
    {
        echo $msg;
    }
   protected function run(){
       $model=app('\core\Model');
       $model->table='zuanke8';
       while (1){
           $data=$model->eq('isdone',0)->limit(30)->findAll(true);
           if($data){
                foreach ($data as $v){
                    $url='http://www.zuanke8.com/thread-'.$v['from_id'].'-1-1.html';
                    $model->eq('id',$v['id'])->update(['isdone'=>1,'url'=>$url]);
                    echo $v['id'].PHP_EOL;
                }
           }else{
               break;
           }
       }
   }

   protected function contentTest($url){
       $caijiR=$this->getCaijiRules('zuanke8.com','content');
       $caiji=\core\caiji\normal\Content::create($caijiR);
       try {
           $caiji->qureyTest($url);
       }catch(\ErrorException $e){
           dump($e->getMessage());
       }catch (\Exception $e){
           dump($e->getMessage());
       }catch (\Error $e){
           dump($e->getMessage());
       }
   }

    /** ------------------------------------------------------------------
     * 读取采集规则
     * @param string $name 采集规则名
     * @param string $type  种类：page/content/download/fabu
     * @param null|string $customOpt 自定义项 json格式字符串
     * @return array|bool
     *--------------------------------------------------------------------*/
    protected function getCaijiRules($name,$type,$customOpt=null){
        $type=strtolower($type);
        $caijiRule=\core\Conf::get($type,$name,null,'config/caiji/');
        if(!$caijiRule){
            return false;
        }
        if($customOpt){
            $item_options=json_decode($customOpt,true);
            if(isset($item_options[$type])){
                $caijiRule=array_merge($caijiRule,$item_options[$type]);
            }
            unset($item_options);
        }
        $options=\core\Conf::get('options',$name,null,'config/caiji/');
        if($options){
            $caijiRule=array_merge($caijiRule,$options);
        }
        unset($options);
        $caijiRule['name']=$name;
        return $caijiRule;
    }

}
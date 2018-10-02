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
 * 为表weixinqun的content字段 随机添加内容
 * ======================================*/


namespace shell\widget;
class Addcontent extends Base
{
    protected function _init()
    {
        parent::_init();
        $this->model=app('\app\weixinqun\model\Weixinqun');
        $this->prefix=\app\weixinqun\model\Weixinqun::$prefix;
    }
    //入口方法
    public function run(){
        //$start=(int)($this->param[1] ?? 0);
        $this->doLoop([
            //create_time > 1483200000
            'sql'=>'select * from '.$this->prefix.$this->model->table.' where caiji_isdone=0',
            'params'=>[]
        ],function($v){
            if(!$v['content']){
                $ret=$this->model->_sql('SELECT excerpt,id FROM `'.$this->prefix.'weixinqun` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.$this->prefix.'weixinqun`))) ORDER BY id LIMIT 4',[],false);
                if($ret){
                    $v['content']='';
                    foreach ($ret as $item){
                        $v['content'].='<p>'.$item['excerpt'].'</p>';
                    }
                    $this->model->eq('id',$v['id'])->update(['caiji_isdone'=>1,'content'=>$v['content']]);
                    echo 'success add content'.PHP_EOL;
                }else{
                    echo 'shui ji error'.PHP_EOL;
                }
            }else{
                $this->model->eq('id',$v['id'])->update(['caiji_isdone'=>1]);
                echo 'content yi you'.PHP_EOL;
            }
            return 0;
        },[
            'from'=>$this->model->table,
            'where'=>[['caiji_isdone','eq',0]]
        ]);

    }
}
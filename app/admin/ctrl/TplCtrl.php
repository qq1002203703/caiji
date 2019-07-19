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


namespace app\admin\ctrl;


use app\common\ctrl\AdminCtrl;
use app\common\ctrl\Func;

class TplCtrl extends AdminCtrl
{
    protected $opt;
    protected function _init()
    {
        parent::_init();
        $this->getTplSetting();
    }

    public function list(){
        $isMobile=(int)get('m','int',0);
        if($isMobile)
            $ret= scandir($this->opt['mobile_path']);
        else
            $ret= scandir($this->opt['view_path']);
        $this->_display('',[
            'title'=>'模板管理',
            'data'=>$ret,
            'currentTpl'=>$this->opt['current_tpl_name'],
            'isMobile'=>$isMobile
        ]);
    }

    public function edit(){
        $data['tpl']=get('tpl','','');
        $data['tpl_d']=$data['tpl']? $data['tpl'].'/' : '';
        $data['dir']=get('dir','','');
        $data['dir_d']=$data['dir'] ? $data['dir'].'/' :'';
        $data['filename']=get('name','','');
        $data['isMobile']=(int)get('m','int',0);
        if($data['isMobile'])
            $data['root']= $this->opt['mobile_path'];
        else
            $data['root']=$this->opt['view_path'];
        $data['path']=$data['root'].$data['tpl_d'].$data['dir_d'].$data['filename'];
        if(is_dir($data['path'])){
            $data['content']='请选择你需要的模板文件进行编辑';
            $data['list']=scandir($data['path']);
        }elseif(is_file($data['path'])){
            $data['content']=file_get_contents($data['path']);
            $data['list']=scandir(dirname($data['path']));
        }else{
            $data['content']='不是一个有效的文件或文件夹';
            $data['list']='';
        }
        $this->_display('',[
            'title'=>'编辑模板文件',
            'data'=>$data
        ]);
    }

    protected function getTplSetting(){
        $template=app('config')::get('template','config');
        $this->opt['open_mobile_tpl']=$template['open_mobile_tpl'];
        unset($template['open_mobile_tpl']);
        $this->opt['current_tpl_name']=app('config')::get('template','site');
        foreach ($template as $key => $item){
            $this->opt[$key]=realpath(ROOT .  '/' .$item);
            if( $this->opt[$key])
                $this->opt[$key]=str_replace('\\','/', $this->opt[$key]).'/';
        }

    }

    protected function do(){

    }

}
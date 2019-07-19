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
use core\Session;
use extend\Upload;
use core\Conf;
class UploadCtrl extends AdminCtrl
{
    //图片上传页
    public function img(){
        //已有图片查询
        $currentPage=get('page','int',1);
        $perPage=get('per','int',20);
        $model=app('\app\admin\model\File');
        $data=$model->eq('isimg',1)->order('id desc')->limit($perPage*($currentPage-1),$perPage)->findAll(true);
        $this->_assign([
            'title'=>'图片上传',
            'data'=>$data
        ]);
        $this->_display();
    }
    //上传验证页
    public function verify(){
        $model=app('\app\admin\model\File');
        $upload=new Upload([
            'token'=>Session::get('__token__'),
            'isRest'=>true,
            'thumb'=>$model->getThumbSetting(),
            'resize'=>$model->getImageResizeSetting()
        ]);
        $fileList=$upload->start('pic');
        if(is_string($fileList)){
            json(['status'=>1,'msg'=>$fileList]);
        }else{
            json(['status'=>0,'msg'=>'成功上传','data'=>$fileList]);
        }
    }
    //图片流加载
    public function flow(){
        $currentPage=get('page','int',1);
        $perPage=get('per','int',20);
        $model=app('\app\admin\model\File');
        $total=$model->count(['where'=>[['isimg','eq',1]]]);
        $data=($total >0) ? $model->eq('isimg',1)->order('id desc')->limit($perPage*$currentPage,$perPage)->findAll(true)  :[];
        json([
            'status'=>0,
            'data'=>$data,
            'pages'=>ceil($total/$perPage)
        ]);
    }
    //删除
    public function del(){
        $id=(int)get('id','int',0);
        if($id==0){
            json(['status'=>1,'msg'=>'id格式不符']);
            return;
        }
        $model=app('\app\admin\model\File');
        if($model->delOne($id)){
            json(['status'=>0,'msg'=>'成功删除']);
        }else{
            json(['status'=>2,'msg'=>$model->getError()]);
        }
    }

    /** ------------------------------------------------------------------
     * 与ueditor交互的统一入口
     *--------------------------------------------------------------------*/
    public function ueditor(){
        $action=get('action');
        $model=app('\app\admin\model\File');
        switch ($action){
            case 'image':
                $fileList=$this->getFiles('file',['isRest'=>true,'thumb'=>$model->getThumbSetting(),'resize'=>$model->getImageResizeSetting()]);
                if(is_array($fileList)){
                    $this->ueditorFileList($fileList);
                }else{
                    json(['code'=>1,'state'=>$fileList]);
                }
                return;
            case 'listimage':
                $this->ueditorListimage();
                return;
            case 'video':
                break;
            default:
                json(['code'=>2,'state'=>'action不正确']);
                return;
        }
    }

    /** -----------------------------------------------------------------
     * 获取上传的文件
     * @param string $fieldName
     * @param array $option 实例化upload类时的参数,具体有哪些参数请参看\extend\Upload类
     *          格式 ['token'=>Session::get('__token__'),'isRest'=>true]
     * @return  array|string 成功返回文件的数组形式的结果集，失败返回错误信息的字符串
     *--------------------------------------------------------------------*/
    protected function getFiles($fieldName,$option=[]){
        $upload=new Upload($option);
        return $upload->start($fieldName);
    }

    /** ------------------------------------------------------------------
     * 文件结果集格式化成ueditor需要的形式,前直接输出json格式的字符串
     * @param array $fileList  upload类返回的文件结果集
     * @param array $other 额外项
     *--------------------------------------------------------------------*/
    protected function ueditorFileList($fileList,$other=[]){
        if(isset($fileList[0]['savename'])){//多文件
            $ret=[];
            foreach ($fileList as $k =>$item){
               $ret[$k]=[
                   'url'=>$item['uri'],
                   'title'=>$item['title'],
                   'original'=>$item['savename'].'.'.$item['ext'],
                   'state'=> 'SUCCESS'
               ];
            }
            json(array_merge(['code'=>0,'state'=> 'SUCCESS','list'=>$ret],$other));
        }else{
            json(array_merge([
                'code'=>0,
                'url'=>$fileList['uri'],
                'title'=>$fileList['title'],
                'original'=>$fileList['savename'].'.'.$fileList['ext'],
                'state'=> 'SUCCESS'
            ],$other)) ;
        }
    }

    protected function ueditorListimage(){
        $currentPage=get('start','int',1);
        $perPage=get('size','int',20);
        $model=app('\app\admin\model\File');
        $total=$model->count(['where'=>[['isimg','eq',1]]]);
        $data=($total >0) ? $model->eq('isimg',1)->order('id desc')->limit($perPage*$currentPage,$perPage)->findAll(true)  :false;
        if($data){
            $this->ueditorFileList($data,['start'=>0, 'total'=>$total]);
        }else{
            json([
                'state'=>'没有结果',
                'list'=>[],
                'start'=>0,
                'total'=>$total,
            ]);
        }
    }
    public function manage(){
        $this->_display('',[
            'title'=>'图片管理'
        ]);
    }
}
<?php
namespace app\portal\model;
use app\admin\model\File;
use app\admin\model\Post;
use core\Conf;
use extend\Download;

class PortalPost extends Post
{
    public $table='portal_post';
    public $primaryKey = 'id';

    /**
     * 数据入库前过滤和处理
     * @param array $data
     * @param bool $isEdit 是否是编辑， true时表示是编辑 要进行的是更新；false时表示不是编辑，要进行的是插入
     * @return array
     */
    public function filter($data,$isEdit=false){
        //缩略图处理
        $this->thumb($data);
        if($isEdit)
            $data=$this->_filterDataE($data);
        else{
            $data=$this->_filterData($data);
        }

        $this->setDefultValue($data,$isEdit);
        $this->setCount($data);
        $data['files']=isset($data['files'])?$this->setJsonField($data['files'],function($fv){
            if(!isset($fv['url']) || checkIsEmpty($fv['url']))
                return true;
            return false;
        }):'';
        $data['more']=isset($data['more'])?$this->setJsonField($data['more'],function($fv){
            if(!isset($fv['name']) || !isset($fv['value']) || ( checkIsEmpty($fv['name'])) || checkIsEmpty($fv['value']))
                return true;
            return false;
        }):'';
        return $data;
    }

    /** ------------------------------------------------------------------
     * 缩略图处理
     * @param array $data
     * @return bool
     *--------------------------------------------------------------------*/
    public function thumb(&$data){
        if(isset($data['images_url'])){
            $url='';
            if(is_string($data['images_url'])){
                $ret=$this->thumbItem($data);
                $url=$data['thumb']=$ret['url'];
                $data['thumb_ids']=$ret['id'] ? :'';
            }elseif (is_array($data['images_url'])){
                $cout=count($data['images_url']);
                $download=new Download();
                $fileModel=app('\app\admin\model\File');
                $site_url=Conf::get('site_url','site');
                $reg='#^((?!https?://)|('.$site_url.')).*$#i';
                $ids=[];
                $url='';
                for ($i=0;$i<$cout;$i++){
                    $item=[
                        'images_url'=>$data['images_url'][$i],
                        'images_id'=>$data['images_id'][$i],
                    ];
                    if(isset($data['images_title'][$i]) &&$data['images_title'][$i]){
                        $item['images_title']=$data['images_title'][$i];
                    }
                    $ret=$this->thumbItem($item,$reg,$download,$fileModel);
                    if($url=='' && $ret['url']){
                        $url=$ret['url'];
                    }
                    if($ret['id']){
                        $ids[]=$ret['id'];
                    }
                }
                $data['thumb']=$url;
                $data['thumb_ids']=implode(',',$ids);
            }
            unset($data['images_url']);
            if(isset($data['images_id']))
                unset($data['images_id']);
            if(isset($data['images_title']))
                unset($data['images_title']);
            return $url !=='';
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 缩略图单项处理
     * @param $imgItem
     * @param null|string $reg
     * @param null|Download $download
     * @param null|\app\admin\model\File $fileModel
     * @return array
     *--------------------------------------------------------------------*/
    protected function thumbItem($imgItem,$reg=null,$download=null,$fileModel=null){
        if(isset($imgItem['images_id']) && $imgItem['images_id'] && !is_array($imgItem['images_id']) ){
            $url=$imgItem['images_url'];
            $id=(int)$imgItem['images_id'];
        }else{
            $id=0;
            $url='';
            $reg=$reg ? : '#^((?!https?://)|('.Conf::get('site_url','site').')).*$#i';
            if(preg_match($reg,$imgItem['images_url'])){//本服务器的图片
                $url=$imgItem['images_url'];
                if(isset($imgItem['images_id']) && $imgItem['images_id'])
                    $id=(int)$imgItem['images_id'];

            }else{ //网络图片
                $download=$download ? : new Download();
                $fileModel=$fileModel ? :app('\app\admin\model\File');
                //下载图片
                $file=$download->down($imgItem['images_url'],'{%Y%}/{%m%}/{%d%}/{%u%}',false);
                if(is_array($file)){
                    //入库
                    $res=$fileModel->add([
                        'uri'=>$file['fileUrl'],
                        'title'=>$imgItem['images_title'] ?? ''
                    ],true);
                    if($res){
                        $url=$res['uri'];
                        $id=$res['id'];
                    }
                }
            }
        }
        return ['url'=>$url, 'id'=>$id];
    }

    /** ------------------------------------------------------------------
     * localImg
     * @param $url
     * @param $id
     * @param File $model
     *--------------------------------------------------------------------*/
    protected function localImg(&$url, &$id,$title,$model){
        if($id){
            if($model->eq('id',$id)->find()===false){
                $ret=$model->add(['uri'=>$url, 'title'=>$title],false);
                if($ret===false){
                    $url='';
                    $id='';
                }else{

                }
            }
        }
    }


    /** ------------------------------------------------------------------
     * 默认值设置
     * @param array $data
     * @param bool $isEdit
     *--------------------------------------------------------------------*/
    public function setDefultValue(&$data,$isEdit){
        if(isset($data['pid']) && $data['pid']==='')
            $data['pid']=0;
        if(isset($data['status']) && $data['status']==='')
            $data['status']=1;
        if(isset($data['allow_comment']) && $data['allow_comment']==='')
            $data['allow_comment']=1;
        if(isset($data['coin']) && $data['coin']==='')
            $data['coin']=0;
        if(isset($data['money']) && $data['money']==='')
            $data['money']=0;
        $time=time();
        $data['create_time']=isset($data['create_time']) && $data['create_time'] ? strtotime($data['create_time']): $time;
        $data['published_time']=isset($data['published_time']) && $data['published_time']? strtotime($data['published_time']): $time;
        $data['update_time']=$time;
        if($isEdit){
            if(isset($data['uid']) && $data['uid']==='')
                $data['uid']=$_SESSION['uid'];
        }else{
            if(!isset($data['uid']) || $data['uid']==='')
                $data['uid']=$_SESSION['uid'];
        }
    }

    /**
     * 计数处理
     * @param $data
     */
    public function setCount(&$data){
        $setting=Conf::all('portal');
        if(! isset($data['views']) || !$data['views'])
            $data['views']=mt_rand(40,$setting['counts_views']);
        if(! isset($data['likes']) || !$data['likes'])
            $data['likes']=mt_rand(2,$setting['counts_likes']);
        if(! isset($data['downloads']) || !$data['downloads'])
            $data['downloads']=mt_rand(0,$setting['counts_downloads']);
    }

    /** ------------------------------------------------------------------
     * 设置带json格式字符串的字段的值，目前有文件和扩展项是支持json格式字符串
     * @param string|array $fieldValue 字段的值
     * @param  callable $checkFunc 判断函数,判断什么时候需要删除子段中的一个子项
     * @return string
     *--------------------------------------------------------------------*/
    public function setJsonField($fieldValue,$checkFunc){
        if(is_string($fieldValue)){
            return $fieldValue;
        }
        if(is_array($fieldValue)){
            $isDel=false; //是否发生过删除
            foreach ($fieldValue as $k =>$v){
                if(call_user_func($checkFunc,$v)){
                    if($isDel===false)
                        $isDel=true;
                    unset($fieldValue[$k]);
                }
            }
            if($fieldValue){
                return $isDel ? json_encode(array_values($fieldValue)) : json_encode($fieldValue) ;
            }
        }
        return '';
    }

    /** ------------------------------------------------------------------
     * 删除文档 支持单篇和批量
     * @param string|array|int $id
     * @return bool|string 成功返回true 失败返回错误信息
     *--------------------------------------------------------------------*/
    public function del($id,$type){
        if(!$id)
            return 'id不能为空';
        if(is_string($id)|| is_int($id)){
            $id=(string)$id;
            if(preg_match('/^(\d[\d,]*)*\d$/',$id)==0)
                return 'id格式不符';
            else
                $id=explode(',',$id);
        }
        $in=[];
        $undo=[];
        foreach ($id as $item){
            //查询是否有子文档
            $result=$this->eq('pid',$item)->find(null,true);
            if($result){
                $undo[]=$item;
            }else{
                $in[]=$item;
            }
        }
        if(!$in){
            return '全部存在子文档无法删除';
        }
        $ret=$this->in('id',$in)->delete();
        if($ret){
            //删除标签
            $this->from('tag_relation')->in('oid',$in)->eq('type',$type)->delete();
            if($undo){
                $undo=implode(',',$undo);
                $in=implode(',',$in);
                return '只成功删除了：'.$in.';但由于下面这些存在子文档无法删除：'.$undo;
            }else
               return true;
        }else{
            return '删除失败,可能是提交了多次删除，已经在上一次删除！';
        }
    }

}
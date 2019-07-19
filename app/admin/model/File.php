<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 文件模型：主要方法 delOne()、 add()
 * ======================================*/

namespace app\admin\model;
use core\Conf;
use core\Model;
use extend\Download;
use extend\ImageResize;

class File extends Model
{
    public $table='file';
    public $primaryKey='id';
    protected $error='';

    /** ------------------------------------------------------------------
     * 删除数据库中一个文件
     * @param int $id 文件在数据表中的id
     * @return bool
     *--------------------------------------------------------------------*/
    public function delOne($id){
        $data=$this->find($id,true);
        if(!$data){
            $this->error='不存在的id';
            return false;
        }
        //删除主文件
        @unlink(ROOT.$data['savepath']);
        //删除缩略图
        if($data['thumb']){
            $thumbs=explode(',',$data['thumb']);
            foreach ($thumbs as $thumb){
                @unlink(ROOT.$data['savepath'].'_'.$thumb.'.'.$data['ext']);
            }
        }
        //删除数据库数据
        if($this->delete($data) ==0){
            $this->error='数据库删除失败';
            return false;
        }
        return true;
    }

    public function getError(){
        return $this->error;
    }

    /** ------------------------------------------------------------------
     * 从mime信息判断文件是否是图片
     * @param string $mime
     * @return bool
     *--------------------------------------------------------------------*/
    public function checkIsImg($mime){
        return  strstr($mime, 'image') !== false;
    }

    /** ------------------------------------------------------------------
     * 获取文件的mime
     * @param string $file
     * @return string
     *--------------------------------------------------------------------*/
    public function getFileMime($file){
        $fi = new \finfo(FILEINFO_MIME_TYPE);
        return $fi->file($file);
    }

    /** ------------------------------------------------------------------
     * 图片入库前的过滤
     * @param string $imgFile
     * @param bool $isDel
     * @return bool
     *--------------------------------------------------------------------*/
    public function imgFilter($imgFile,$isDel){
        //文件是否存在
        if (empty($imgFile) || !is_file($imgFile)) {
            $this->error='图片不存在';
            return false;
        }
        //文件是否是图片
        if(!$this->checkIsImg($imgFile)){
            $this->error='不是图片';
            if($isDel){
                @unlink($imgFile);
            }
            return false;
        }
        return true;
    }

    /** ------------------------------------------------------------------
     * 重设图片大小
     * @param string $savepath 图片完整路径
     * @param bool $createThumb 是否需要生成缩略图
     * @return string 返回缩略图
     *--------------------------------------------------------------------*/
    public function resize($savepath,$createThumb=true,$module='portal'){
        $resize=new ImageResize();
        $thumb='';
        $imgSetting=$this->getImageResizeSetting($module);
        if($resize->checkImage($savepath)){
            $width=$imgSetting['w'] ?? 800;
            $height=$imgSetting['h'] ?? 800;
            $resize->quality_jpg=90;
            $resize->quality_png=9;
            try{
                $resize->add()->resizeToBestFit($width,$height)->save($savepath);
                $resize->destroy();
            }catch (\Exception $e){
                //echo $e->getMessage();
                return '';
            }
            //生成缩略图
            if($createThumb && ($thumbSetting=$this->getThumbSetting($module))){
                foreach ($thumbSetting as $item){
                    if($resize->checkImage($savepath)){
                        try{
                            $resize->add()-> crop($item['w'],$item['h'],true)->save($savepath.'_'.$item['w'].'x'.$item['h'].'.jpg',IMAGETYPE_JPEG);
                            $thumb.=$item['w'].'x'.$item['h'].',';
                            $resize->destroy();
                        }catch (\Exception $e){
                            //echo $e->getMessage();
                            continue;
                        }
                    }

                }
            }
        }
        return $thumb ? rtrim($thumb,',') : '';
    }

    /** ------------------------------------------------------------------
     * 添加文件到数据库
     * @param array $file 文件信息 必须 $file['uri']; 可选 $file['title']
     * @param bool $isDel 不符合图片预期时是否强制删除原文件 ,一般文件此项设置为false
     * @param string 读取的配置文件
     * @return bool|array
     *--------------------------------------------------------------------*/
    public function add($file,$isDel=false,$createThumb=true,$module='portal'){
        $data=$this->getFileInfo($file,$isDel,$createThumb,$module);
        if(!$data)
            return false;
        $data['savepath']=str_replace(str_replace('\\','/',ROOT),'',$data['savepath']);
        if($data['isimg'] && Conf::get('is_resize',$module,false)){
            $data['thumb']=$this->resize($data['savepath'],$createThumb,$module);
        }
        if($data['id']=$this->insert($data)){
            return $data;
        }
        $this->error='插入数据库失败';
        return false;
    }

	 /** ------------------------------------------------------------------
     * 获取文件信息
     * @param array $file 文件信息 必须 $file['uri']; 可选 $file['title']
     * @param bool $isDel 不符合图片预期时是否强制删除原文件 ,一般文件此项设置为false
     * @param bool $createThumb 是否建立缩略图
     * @param string 读取的配置文件
     * @return array
     *---------------------------------------------------------------------*/
    public function getFileInfo($file,$isDel=false,$createThumb=true,$module='portal'){
        if(!isset($file['uri'])|| !$file['uri']){
            $this->error='文件中没有uri信息';
            return [];
        }
        if(isset($file['savepath']) && $file['savepath'])
            $data['savepath']=$file['savepath'];
        else
            $data['savepath']=str_replace('\\','/',ROOT.'/public'.$file['uri']);
        if ( !is_file($data['savepath'])) {
            $this->error='文件不存在';
            return [];
        }
        $data['uri']=$file['uri'];
        $data['mime']=$this->getFileMime($data['savepath']);
        $data['isimg']=$this->checkIsImg($data['mime']) ? 1 :0;
        $data['thumb']='';
        if($data['isimg']){
            $data['ext']=$this->getFileExt($data['savepath'],$data['uri'],true,$isDel);
            if($data['ext'] ==='' && $isDel){
                $this->error='扩展名无法获取';
                return [];
            }
        }else{
            if($isDel){
                @unlink($data['savepath']);
                $this->error='文件不是图片';
                return [];
            } else
                $data['ext']=$this->getFileExt($data['savepath'],$data['uri'],false,false);
        }
        $data['title']=$file['title'] ?? '';
        $data['size']=round(filesize($data['savepath'])/1024,2);
        $data['md5']=md5_file($data['savepath']);
        if($data['md5']===false)
            $data['md5']='';
        $data['savename']=$this->getFileNameBody($data['savepath']);
        return $data;
    }

    /** ------------------------------------------------------------------
     * 获取文件扩展名
     * @param string $savepath 文件完整路径
     * @param string $uri 文件的uri
     * @param bool $isimg  是否是图片，直接从文件名解析不到扩展名时，图片会继续从mime中获取扩展名
     * @param bool $isDel 扩展名获取不到时 是否强制删除原文件
     * @return string
     *--------------------------------------------------------------------*/
    public function getFileExt(&$savepath,&$uri ,$isimg,$isDel){
        $r_offset = strrpos($savepath, '.');
        $ext=$r_offset ?  substr($savepath, $r_offset + 1) : '';
        if($isimg && $ext===''){//图片扩展名为空时继续从mine中获取
            $ext=ImageResize::getImagExtendName($savepath);
            if($ext==='' && $isDel){
                @unlink($savepath);
            }
            if($ext !=='' ){
                @rename($savepath, $savepath.$ext);
                $savepath.=$ext;
                $uri.=$ext;
            }
        }
        return $ext;
    }

    /** ------------------------------------------------------------------
     * 从文件路径中提取文件名（不带后缀）
     * @param string $savepath
     * @return string
     *--------------------------------------------------------------------*/
    public function getFileNameBody($savepath){
        $name=basename($savepath);
        $r_offset = strrpos($name, '.');
        return  $r_offset ? substr($name, 0,$r_offset) : $name;
    }

    /** ------------------------------------------------------------------
     * 读取配置中的缩略图设置项
     * @return array  格式如 [['w'=>150,'h'=>150],['w'=>400,'h'=>400]]
     *--------------------------------------------------------------------*/
    public function getThumbSetting($module='portal'){
        $thumb=Conf::get('thumb',$module);
        $ret=[];
        if($thumb){
            $arr=explode(',',$thumb);
            foreach ($arr as $i => $item){
                list($ret[$i]['w'],$ret[$i]['h'])=explode('x',$item);
            }
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 读取配置中的图片重设大小配置项
     * @return array 格式如 ['w'=>800,'h'=>800]
     *--------------------------------------------------------------------*/
    public function getImageResizeSetting($module='portal'){
        $image_resize=Conf::get('image_resize',$module);
        $ret=[];
        if($image_resize){
            list($ret['w'],$ret['h'])=explode('x',$image_resize);
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 添加远程图片
     * @param string $url 图片的网址
     * @param string $title 图片的标题
     * @param bool $isDel
     * @param bool $createThumb
     * @return string
     *---------------------------------------------------------------------*/
    public function addRemoteFile($url,$title='',$isDel=false,$createThumb=true){
        $site_url=Conf::get('site_url','site');
        $reg='#^((?!https?://)|('.$site_url.')).*$#i';
        if(preg_match($reg,$url)) //本地图片直接返回
            return $url;
        $file=$this->download($url);
        if(is_array($file)){
            $res=$this->add(['uri'=>$file['fileUrl'], 'title'=>$title],$isDel,$createThumb);
            if($res===false)
                return '';
            else
                return $res['uri'];
        }else{
            return '';
        }
    }

    /** ------------------------------------------------------------------
     * 下载远程图片
     * @param string $url 图片的网址
     * @return array|string
     * 返回数组时的格式：
     *  array(2) {
            ["fileUrl"] => string(47) "/uploads/images/2018/11/19/5bf21378dd32c1d5.jpg"
            ["savePath"] => string(53) "public/uploads/images/2018/11/19/5bf21378dd32c1d5.jpg"
        }
     *---------------------------------------------------------------------*/
    public function download($url){
        $download=New Download();
        //下载图片
        return $download->down($url,'{%Y%}/{%m%}/{%d%}/{%u%}',false);
    }

    /** ------------------------------------------------------------------
     * 缩略图处理
     * @param array $data
     * @return bool
     *--------------------------------------------------------------------*/
    static public function thumb(&$data,$mudel='portal'){
        if(isset($data['images_url'])){
            $url='';
            if(is_string($data['images_url'])){
                $ret=self::thumbItem($data,$mudel);
                $url=$data['thumb']=$ret['url'];
                $data['thumb_ids']=$ret['id'] ? :'';
            }elseif (is_array($data['images_url'])){
                $count=count($data['images_url']);
                $ids=[];
                $url='';
                for ($i=0;$i<$count;$i++){
                    $item=[
                        'images_url'=>$data['images_url'][$i],
                        'images_id'=>$data['images_id'][$i],
                    ];
                    if(isset($data['images_title'][$i]) &&$data['images_title'][$i]){
                        $item['images_title']=$data['images_title'][$i];
                    }
                    $ret=self::thumbItem($item,$mudel);
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
     * @param array $imgItem
     * @param string $module
     * @return array
     *--------------------------------------------------------------------*/
    static protected function thumbItem($imgItem,$module='portal'){
        if(isset($imgItem['images_id']) && $imgItem['images_id'] && !is_array($imgItem['images_id']) ){
            $url=$imgItem['images_url'];
            $id=(int)$imgItem['images_id'];
        }else{
            $id=0;
            $url='';
            if(preg_match('#^((?!https?://)|('.Conf::get('site_url','site').')).*$#i',$imgItem['images_url'])){//本服务器的图片
                $url=$imgItem['images_url'];
                if(isset($imgItem['images_id']) && $imgItem['images_id'])
                    $id=(int)$imgItem['images_id'];
            }else{ //网络图片
                //下载图片
                $file=(new Download())->down($imgItem['images_url'],'{%Y%}/{%m%}/{%d%}/{%u%}',false);
                if(is_array($file)){
                    //入库
                    $res=(new File())->add([
                        'uri'=>$file['fileUrl'],
                        'title'=>$imgItem['images_title'] ?? ''
                    ],true,true,$module);
                    if($res){
                        $url=$res['uri'];
                        $id=$res['id'];
                    }
                }
            }
        }
        return ['url'=>$url, 'id'=>$id];
    }
}
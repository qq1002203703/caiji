<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 文件上传类
 * ======================================*/

namespace extend;
use core\Conf;
use core\Caiji;
use extend\upload\UploadFile;

/**
 * Class Upload
 * @package extend
 * 属性：公开的属性有 $isRest、$resize、$token和$thumb，此外还可以通过方法 setPath()设置$path
 *      其他属性只能在初始化时传入
 * 方法：公开的方法有setPath()、start()
 */
class Upload
{
    /**
     * @var bool 是否对上传的图片进行额外处理（如改变大小，加水印，加文字等）
     */
    public $isRest=true;
    /**
     * @var array 重设图片时的最大宽度和高度
     */
    public $resize=['w'=>800,'h'=>800];
    /**
     * @var array 生成缩略图时的宽度和高度，二维数组，可以同时生成多种尺寸的缩略图
     */
    public $thumb=[['w'=>150,'h'=>150],['w'=>400,'h'=>400]];
    protected $host;
    protected $path;
    protected $allow=[
        'ext'=>['jpg','jpeg','png','gif'],
        'mime'=>'image/jpeg,image/gif,image/png',
        'size_max'=>1024*1024, //1M=1024*1024bye
        'size_min'=>1024, //1k=1024bye
    ];
    public $token=null;

    /**
     * 构造函数
     * @param array $opt
     *    string $opt['host'] 文件访问域名
     *    string $opt['path'] 同setPath()的path参数
     *     array $opt['allow']
     *     bool $opt['isRest']
     *     array $opt['thumb']
     *     array $opt['resize']
     *     string $opt['token']
     */
    public function __construct($opt=[]){
        $this->host=$opt['host'] ?? Conf::get('site_url','site');
        $this->setPath( $opt['path'] ?? '');
        if(isset($opt['allow']))
            $this->allow=$opt['allow'];
        if(isset($opt['isRest']))
            $this->isRest=(bool)$opt['isRest'];
        if(isset($opt['resize']))
            $this->resize=$opt['resize'];
        if(isset($opt['thumb']))
            $this->thumb=$opt['thumb'];
        if(isset($opt['token'])){
            $this->token=$opt['token'];
        }
    }

    /** ------------------------------------------------------------------
     * 设置保存文件夹的路径
     * @param string $path  相对于网站对外目录文件夹的相对路径  'uploads/images/%Y%/%m%/%d%'
     *--------------------------------------------------------------------*/
    public function setPath($path=''){
        $this->path=ROOT.'/public/'.($path ? $this->format(trim($path,'/')) : 'uploads/images/'.date('Y/m/d',time())).'/';
    }

    /** ------------------------------------------------------------------
     * setToken
     * @param string $token
     *--------------------------------------------------------------------*/
    /*public function setToken($token){
        $this->token=$token;
    }*/

    /** ------------------------------------------------------------------
     * 文件上传
     * @param string $filedName  上传文件时 文件在表单中的字段名
     * @return array|string
     *--------------------------------------------------------------------*/
    public function start($filedName='file')
    {
        $upload=new UploadFile($filedName);
        $upload->token($this->token);
        $filelist=$upload->save($this->path,$this->allow,$this->host);
        if(is_array($filelist)){
            # 返回数组，文件就上传成功了
            return $this->handler($filelist);
        }else{
            # 如果$filelist返回整数(int)就是发生错误了
            return $upload->getError($filelist);
        }
    }

    /** ------------------------------------------------------------------
     * 处理器，对上传结果中的每项文件进行处理
     * @param array $filelist
     * @return array
     *--------------------------------------------------------------------*/
    protected function handler($filelist){
        $ret=[];
        if(isset($filelist[0]['name'])){ //多文件
            foreach ($filelist as $k => $item){
                if ($this->isRest && $item['isimg']==1){ //是否要额外处理图片
                    $filelist[$k]=$this->resetImage($item);
                }
                //入库
                $ret[$k]=$this->addToDatabase($filelist[$k]);
            }
        }else{ //单文件
            if($this->isRest && $filelist['isimg'] ==1){//是否要额外处理图片
                $filelist=$this->resetImage($filelist);
            }
            //入库
            $ret=$this->addToDatabase($filelist);
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 重新设置图片宽高
     * @param $fileItem
     * @return mixed
     *--------------------------------------------------------------------*/
    protected function resetImage($fileItem){
        $resize=new ImageResize();
        $isdo=true;
        $fileItem['thumb']='';
        if($resize->checkImage($fileItem['savepath'])){
            $width=$this->resize['w'] ?? 800;
            $height=$this->resize['h'] ?? 800;
            $resize->quality_jpg=90;
            $resize->quality_png=9;
            try{
                $resize->add()->resizeToBestFit($width,$height)->save($fileItem['savepath']);
                $resize->destroy();
                if($this->thumb){
                    foreach ($this->thumb as $item){
                        if($resize->checkImage($fileItem['savepath'])){
                            try{
                                $resize->add()-> crop($item['w'],$item['h'],true)->save($fileItem['savepath'].'_'.$item['w'].'x'.$item['h'].'.jpg',IMAGETYPE_JPEG);
                                $fileItem['thumb'].=$item['w'].'x'.$item['h'].',';
                                $resize->destroy();
                            }catch (\Exception $exception){
                                continue;
                            }
                        }
                    }
                }
            }catch (\Exception $e){
                $isdo=false;
            }
        }else{
            $isdo=false;
        }
        //$resize->destroy();
        if($isdo){
            $fileItem['size']=round(filesize($fileItem['savepath'])/1024,2);
            $fileItem['md5']=md5_file($fileItem['savepath']);
            if($fileItem['thumb']){
                $fileItem['thumb']=rtrim($fileItem['thumb'],',');
            }
        }
       return $fileItem;
    }
    /** ------------------------------------------------------------------
     * 对路径进行格式化
     * @param string $str
     * @return string
     *---------------------------------------------------------------------*/
    protected function format($str){
        return Caiji::format($str);
    }

    protected function addToDatabase($fileItem){
        //return ['id'=>17,'uri'=>$fileItem['uri']];
        $model=app('\app\admin\model\File');
        $data=[
            'title'=>str_replace('.'.$fileItem['ext'],'',$fileItem['name']),
            'uri'=>$fileItem['uri'],
            'isimg'=>$fileItem['isimg'],
            'savepath'=>str_replace(str_replace('\\','/',ROOT),'',$fileItem['savepath']),
            'savename'=>$this->getFileNameBody($fileItem['savename']),
            'size'=>$fileItem['size'],
            'ext'=>$fileItem['ext'],
            'md5'=>$fileItem['md5'],
            'thumb'=>$fileItem['thumb'] ?? '',
            'mime'=>$fileItem['mime'],
        ];
        $data['id']=$model->insert($data);
        //unset($data['savepath']);
        return $data;
    }

    /**
     * 获取文件后缀名
     * @param  String  $file  文件名
     * @return String
     */
    private function getFileNameBody($file){
        $r_offset = strrpos($file, '.');
        return  $r_offset ? substr($file, 0,$r_offset) : $file;
    }

}

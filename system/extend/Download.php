<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 文件下载类，能智能合并相同项
 * ======================================*/

namespace extend;
use core\Caiji;
use core\caiji\normal\Page;
/**
 * Class Download
 * @package extend
 *
 */
class Download
{
    public $option=[
        'savePath'=>'public/uploads/images', //保存目录
        'fileFormat'=>'{%Y%}/{%m%}/{%d%}/{%id%}_{%u%}', //文件名格式
        'preUrl'=>'/uploads/images/',//文件url前置 如: 'http://www.baidu.com/'
        'curl'=>[
            'setting'=>[
                'login'=>false,
                //'match'=>'',
                'timeOut'=>[15,45],
                //'tryTimes'=>3
                'opt'=>[
                    //CURLOPT_COOKIE=>''
                ]
            ],
            'options'=>[
                'opt'=>[
                    //CURLOPT_REFERER=>'htttps//:www.baidu.com',
                ],
                //'cookieFile'=>'',
                //'proxy'=>[],
                'method'=>'get',
                //'header'=>[],
            ]
        ],
    ];
    protected $downloadData=[];
    protected $error='';
    /**
     * @var Curl
     */
    protected $curl;
    /** ------------------------------------------------------------------
     * 添加要下载的图片到 $this->downloadData中，同时把标签内容中的图片换成特殊的占位符
     * @param string $tag
     * @param string $content
     * @return string
     *--------------------------------------------------------------------*/
    public function addImg($tag,$content){
        $reg='#<img ([^<>]*?)src=([\'"]?)([^<>"\']*)\2([^>]*)>#i';
        $i=0;
        $content= preg_replace_callback($reg,function($match)use($tag,&$i){
            $ret='';
            if($match[3]){
                $i++;
                $this->downloadData[$tag.':'.$i]=$match[3];
                $ret='{%img'.$tag.':'.$i.'img%}';
            }
            return '<img '.$match[1].'src="'.$ret.'"'.$match[4].'>';
        },$content);
        return $content;
    }

    public function addThumb($tu){
        $this->downloadData['thumb']=$tu;

    }

    public function setOption($name,$value){
        if(isset($this->option[$name])){
            $this->option[$name]=$value;
        }
    }

    /** ------------------------------------------------------------------
     * 添加要下载的文件到 $this->downloadData中
     * @param string $tag
     * @param string $content
     *--------------------------------------------------------------------*/
    public function addFile($tag,$content){
        $arr=explode('{%|||%}',$content);
        $num=count($arr);
        if($num==1){
            $this->downloadData[$tag]=$content;
        }else{
            $i=1;
            foreach ($arr as $item){
                $this->downloadData[$tag.':'.$i]=$item;
                $i++;
            }
        }
    }

    /** ------------------------------------------------------------------
     * 所有信息整合
     * @param bool $isJson
     * @return string|array
     *---------------------------------------------------------------------*/
    public function all($isJson=true){
        if(empty($this->downloadData))
            return '';
        //1、分离重复的下载项
        $data_download=Helper::array_delet_repeat($this->downloadData);
        $this->downloadData=[];
        //2、合并
        $data_download=$this->merge($data_download);
        //3、不相同项完整信息整合：标签名{%@@@%}下载链接{%@@@%}其他有相同链接的标签
        $ret=[];
        foreach ($data_download['unique'] as $k => $v){
            $num=strrchr($k,':');
            if($num!==false){
                $tag=str_replace($num,'',$k);
                $num=ltrim($num,':');
            }else{
                $tag=$k;
                $num='';
            }
            if(is_array($v)){
                $true_url=$v[0];
                $source_url=$v[1];//'aaa:0{%|||%}aaa:1{%|||%}bbb'
            }else{
                $true_url=$v;
                $source_url='';
            }
            $ret[$tag][]=$num.'{%@@@%}'.$true_url.'{%@@@%}'.$source_url;
        }
        return $isJson ? json_encode($ret) : $ret;
    }

    /** ------------------------------------------------------------------
     * 清空下载数据
     *---------------------------------------------------------------------*/
    public function clear(){
        $this->downloadData=[];
    }
    /** ------------------------------------------------------------------
     * 合并下载项
     * @param array $down 已经分离了的下载项数据
     * @return array
     *--------------------------------------------------------------------*/
    protected function merge($down){
        foreach ($down['unique'] as $k1 =>$v1){
            $tag=[];
            foreach ($down['change'] as  $k2=>$v2){
                if($k1===$v2){
                    $tag[]=$k2;
                }
            }
            if($tag){
                $down['unique'][$k1]=[$v1,implode('{%|||%}',$tag)];
            }
        }
        return $down;
    }

    /** ------------------------------------------------------------------
     * 修复占位符为本地路径
     * @param string $table 表名
     * @param string $id 哪些id的数据，用 英文逗号 分隔多个id
     * @param string $field 储存信息的字段名
     *--------------------------------------------------------------------*/
    public function repair($table,$id,$field){
        $model=app('\core\Model');
        $model->table=$table;
        $id=explode(',',$id);
        foreach ($id as $item){
            $data=$model->eq('id',$item)->find(null,true);
            if(!$data || !$data[$field])
                continue;
            $info=json_decode($data[$field],true);
            if($info===false)
                continue;
            $update=[];
            foreach ($info as $tag => $value){
                foreach ($value as $v){
                    list($num,$url,$otherTags)=explode('{%@@@%}',$v);//0=>num 1=>url 2=>其他标签
                    $otherTags=$otherTags ? $tag.($num==='' ? :':'.$num).'{%|||%}'.$otherTags : $tag.($num==='' ? :':'.$num);
                    $data[$tag]=$this->replace($data[$tag],$url,$otherTags,$item);
                }
                //
                if($tag==='content'){
                    if(!isset($data['title'])){
                        $prefix=\core\Conf::get('prefix','database');
                        $data['title']=$model->_sqlField('title','select title from '.$prefix.$data['table_name'].' WHERE id=?',[$data['oid']],false);
                    }
                    $update[$tag]=Helper::addImgAlt($data[$tag],$data['title']);
                }else{
                    $update[$tag]=$data[$tag];
                }

            }
            $update[$field]='';
            $model->eq('id',$item)->update($update);
        }
    }
    /** ------------------------------------------------------------------
     * 替换占位符为下载后本地的网址
     * @param string $content 原带占位符的内容
     * @param string $url 原文件的下载网址
     * @param string $tags 1个或多个标签集合
     * @param int $id 数据id
     * @return string 返回替换后的内容
     *--------------------------------------------------------------------*/
    public function replace($content,$url,$tags,$id){
        $tags=explode('{%|||%}',$tags);
        foreach ($tags as $item){
            if(strpos($item,':')!==false){
                list(,$num)=explode(':',$item);
                $file=$this->getPath($url,$id,$num);
                $ret=$this->curl($url,$file);
                if($ret){
                    $content=str_replace('{%img'.$item.'img%}',$file['fileUrl'],$content);
                }
            }else{
                $file=$this->getPath($url,$id,'');
                $ret=$this->curl($url,$file);
                if($ret){
                    $content=$file['fileUrl'];
                }
            }
        }
        return $content;
    }

    /** ------------------------------------------------------------------
     * curl下载文件
     * @param string $url 文件的下载链接
     * @param array $file 文件保存的相关信息
     * @return bool 成功下载返回true 否则返回false
     *--------------------------------------------------------------------*/
    public function curl($url,&$file,$isRize=true){
        !$this->curl  && $this->curlInit();
        $this->option['curl']['options']['saveFile']=ROOT.'/'.$file['savePath'];
        $ret=$this->curl->add($url,[],$this->option['curl']['options']);
        if($ret===false) {
            $this->error='caiji bu dao,url:'.$url.', message<<<'.$this->curl->errorMsg.'>>>';
            return false;
        }
        //修改大小
        if($isRize)
            $this->changSize(ROOT.'/'.$file['savePath']);
        //没有后缀时重新获取后缀
        if(isset($file['__suffix__'])){
            if($file['__suffix__']==false){
                $suffix=$this->getSuffix($file['savePath']);
                if($suffix !==''){
                    rename(ROOT.'/'.$file['savePath'], ROOT.'/'.$file['savePath'].$suffix);
                    $file['fileUrl'].=$suffix;
                    $file['savePath'].=$suffix;
                }
            }
            unset($file['__suffix__']);
        }
        return $ret;
    }

    /** ------------------------------------------------------------------
     * 获取图片的后缀
     * @param string $file 完整路径的文件名
     * @return string
     *--------------------------------------------------------------------*/
    protected function getSuffix($file){
        return ImageResize::getImagExtendName(ROOT.'/'.$file);
    }

    /** ------------------------------------------------------------------
     * 获取文件路径：包括保存的完整路径和网址路径
     * @param string $url 原文件的网址
     * @param int $id 数据在表中的id
     * @param int|string $num
     * @return array
     *--------------------------------------------------------------------*/
    protected function getPath($url,$id,$num){
        //偿试解析url获取后缀
        $parUrl=parse_url($url);
        $suffix=strrchr(basename($parUrl['path']),'.');
        $data=[];
        $data['fileUrl']=Caiji::format($this->option['fileFormat'],$id,$num);
        $data['savePath']=$this->option['savePath'].'/'.$data['fileUrl'];
        if($suffix){
            $data['fileUrl'].=$suffix;
            $data['savePath'].=$suffix;
        }else{
            $data['__suffix__']=false;
        }
        if(isset($this->option['preUrl']) && $this->option['preUrl']){
            $data['fileUrl']=$this->option['preUrl'].$data['fileUrl'];
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * curl初始化
     *---------------------------------------------------------------------*/
    protected function curlInit(){
        $this->option['curl']= $this->option['curl'] ?? [];
        $this->option['curl']['setting']= $this->option['curl']['setting'] ?? [];
        $this->option['curl']['options']= $this->option['curl']['options'] ?? [];
        $this->curl=New Curl($this->option['curl']['setting']);
    }

    /** ------------------------------------------------------------------
     * 传递curl
     * @param Curl $curl
     *--------------------------------------------------------------------*/
    public function setCurl(&$curl){
        $this->curl=$curl;
    }

    protected function changSize($file){
        $image=app('\extend\ImageResize');
        if($image->checkImage($file)){
            $image->quality_jpg=90;
            $image->quality_png=8;
            $image->add()->resizeToBestFit(800,800)->save($file);
            //echo ' ----img resize success:'.$file.PHP_EOL;
        }/*else{
            //echo ' ----img resize fail:'.$file.' ;message: '.$image->getMsg().PHP_EOL;
        }*/
    }



    /** ------------------------------------------------------------------
     * 直接下载一个文件
     * @param string $url
     * @param string $fileFormat
     * @param bool $isRize
     * @return array|string  array时包含$data['fileUrl'] 和$data['savePath']
     *--------------------------------------------------------------------*/
    public function down($url,$fileFormat,$isRize=false){
        $this->option['fileFormat']=$fileFormat;
        $file=$this->getPath($url,0,'');
        $ret=$this->curl($url,$file,$isRize);
        return $ret? $file : $this->error;
    }

    public function dump(){
        var_dump($this->downloadData);
    }
}
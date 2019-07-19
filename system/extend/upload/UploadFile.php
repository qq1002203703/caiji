<?php
/**
 * 根据 PHP文件上传类 https://github.com/aileshe/Upload  修改而来的
 * 原作者Author: Dejan
 */
namespace extend\upload;
use extend\Helper;
class UploadFile{
    public $token = null;    # 上传验证口令(string)
    public $frm_name = null; # 文件表单name值(string)
    public $ext = null;      # 允许上传文件的后缀(一维的索引数组)
    public $size_max = null;     # 允许上传文件最大值(int)
    public $size_min=null;  # 允许上传文件最小值(int)
    public $mime = null;     # 允许上传文件的MIME(一维的索引数组)
    public $host = null;     # 文件访问域名(http://www.a.com)
    protected $error=[
        0=>'上传的文件为空或对应的文件表单字段不存在',
        1=>'上传失败',
        2=>'文件存储路径不合法',
        3=>'非法的文件格式，文件后缀或文件类型不对',
        4=>'文件太大超过了允许的最大值',
        5=>'文件太小小于了允许的最小值',
        6=>'Token令牌错误',
        7=>'保存文件的目录写入权限不足'
    ];
    /**
     * 文件接收入口 - 单文件、多文件上传
     * @param String $frm_name  文件在提交表单中的name值
     */
    public function __construct($frm_name = 'file'){
        $this->frm_name = $frm_name;
    }

    /**
     * 设置上传口令 token
     * @param  String  $token     验证口令
     * @return static
     */
    public function token($token = NULL){
        $this->token = $token;
        return $this;
    }

    /**
     * 过滤允许设置
     * @param  array   $allow   过滤允许规则 ['ext'=>'后缀名限制','size_max'=>193038] = ['ext'=>'png,jpg,gif','size_max'=>193038]
     * @return static
     */
    private function allow($allow){
        if($allow != NULL){
            # 文件格式过滤 - 文件后缀
            if(isset($allow['ext']) && !empty($allow['ext'])){
                if(is_array($allow['ext'])){
                    $this->ext = $allow['ext'];
                }else{
                    $this->ext = explode(',', $allow['ext']);
                }
            }

            # 文件格式过滤 - MIME
            if(isset($allow['mime']) && !empty($allow['mime'])){
                if(is_array($allow['mime'])){
                    $this->mime = $allow['mime'];
                }else{
                    $this->mime = explode(',', $allow['mime']);
                }
            }

            # 文件最大值过滤
            if(isset($allow['size_max']) && !empty($allow['size_max'])){
                $this->size_max = (int)$allow['size_max'];
            }

            # 文件最小值过滤
            if(isset($allow['size_min']) && !empty($allow['size_min'])){
                $this->size_min = (int)$allow['size_min'];
            }
        }
        return $this;
    }

    /**
     * 文件接收入口 - 单文件、多文件上传
     * @param  String  $storage  文件存储路径
     * @param  array   $allow    允许上传文件规则 ['ext'=>'后缀名限制','size_max'=>193038] = ['ext'=>'png,jpg,gif','size_max'=>193038]
     * @param  String  $host     文件访问域名
     * @return Int|array  [0上传提交空文件  1上传失败  2文件存储路径不合法  6验证token为空]
     */
    public function save($storage = NULL, $allow = NULL, $host = NULL){
        # token 验证
        if($this->token != NULL){
            if(!isset($_POST['__token__']) || $_POST['__token__'] != $this->token){
                return 6;
            }
        }
        //空文件
        if(empty($_FILES) || !isset($_FILES[$this->frm_name])){
            return 0;
        }
        # 存储路径合法判断
        if(empty($storage) || !is_string($storage)){
            return 2;
        }

        # 初始化过滤设置, 如果$allow为字符串型时自动设置 $host = $allow
        if(is_string($allow)){
            $host = $allow;
            $allow = NULL;
        }else{
            $this->allow($allow);
        }

        # 初始化文件访问域名
        if($host == NULL){
            $this->host = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
        }else{
            $this->host = preg_match('/^http[s]?\:\/\//i', $host)? $host : $_SERVER['REQUEST_SCHEME'].'://'.$host;
        }

        # 判断存储目录是否存在,无则自动创建
        if(!is_dir($storage)){
            if(@mkdir($storage,0777,true) ===false){ //没有写入权限
                return 7;
            }
            //chmod($storage,0777);
        }

        $save_path = realpath($storage); # 上传文件存储目录的绝对路径
        $filelist = array(); # 文件数组

        $files = $_FILES[$this->frm_name]; # 简化数组
        if(is_string($files['name'])){
            # 单文件上传
            $check_res = $this->file_check($files);
            if($check_res === 0){ # 0 校检没问题,
                $filelist[] = $files;
            }else{
                return $check_res;
            }
        }else{
            # 多文件上传
            //$file = array();
            foreach($files['name'] as $k=>$v){
                $file = array(
                    'name'    => $v,
                    'type'    => $files['type'][$k],
                    'tmp_name'=> $files['tmp_name'][$k],
                    'error'   => $files['error'][$k],
                    'size'    => $files['size'][$k]
                );

                $check_res = $this->file_check($file);
                if($check_res === 0){ # 0 校检没问题
                    $filelist[] = $file;
                }else{
                    return $check_res;
                }
            }
        }

        # 从临时空间里提取出文件到真实路径、文件信息补全
        $new_arr = array();
        if(count($filelist) === 1){
            # 单文件上传
            $ext = $this->get_file_ext($filelist[0]['name']); # 文件后缀名
            $fileName = $ext == '' ? $this->uuid() : $this->uuid().'.'.$ext;
            move_uploaded_file($filelist[0]['tmp_name'], $save_path.'/'.$fileName);

            # - 上传文件回调数据信息
            $new_arr['name']     = $filelist[0]['name']; # 文件上传时的原名称
            $new_arr['ext']      = $ext; # 文件后缀名
            $new_arr['mime']     = $filelist[0]['type']; # 文件MIME
            $new_arr['size']     = round($filelist[0]['size']/1024,2); # 文件大小(单位:k)
            $new_arr['savename'] = $fileName; # 文件保存在服务器上名称
            $new_arr['savepath'] = str_replace('\\', '/', $save_path.'/'.$fileName); # 文件存储绝对路径(包含文件名)
            $new_arr['url']      = str_replace($_SERVER['DOCUMENT_ROOT'], $this->host, $new_arr['savepath']); # 文件访问URL地址
            $new_arr['uri']      = str_replace($_SERVER['DOCUMENT_ROOT'], '', $new_arr['savepath']); # 文件访问URI相对地址
            $new_arr['md5']      = md5_file($new_arr['savepath']); # 文件MD5
            $new_arr['isimg'] = (@preg_match('#^image/.+$#i',$new_arr['mime']) >0) ? 1 : 0 ;
        }else{
            # 多文件上传
            foreach($filelist as $v){
                $ext = $this->get_file_ext($v['name']); # 文件后缀名
                $fileName = $ext == '' ? $this->uuid() : $this->uuid().'.'.$ext;
                move_uploaded_file($v['tmp_name'], $save_path.'/'.$fileName);
                # - 上传文件回调数据信息
                $savepath = str_replace('\\', '/', $save_path.'/'.$fileName);
                $new_arr[] = array(
                    'name'     => $v['name'] , # 文件上传时的原名称
                    'ext'      => $ext ,       # 文件后缀名
                    'mime'     => $v['type'] , # 文件MIME
                    'size'     => round($v['size']/1024,2) , # 文件大小(单位:k)
                    'savename' => $fileName ,  # 文件保存在服务器上名称
                    'savepath' => $savepath , # 文件存储绝对路径(包含文件名)
                    'url'      => str_replace($_SERVER['DOCUMENT_ROOT'], $this->host, $savepath) , # 文件访问URL地址
                    'uri'      => str_replace($_SERVER['DOCUMENT_ROOT'], '', $savepath) , # 文件访问URI地址
                    'md5'      => md5_file($savepath), # 文件MD5
                    'isimg' => (@preg_match('#^image/.+$#i',$v['type']) >0) ? 1 : 0 ,
                );
            }
        }
        return $new_arr;
    }

    /**
     * 获取文件后缀名
     * @param  String  $file  文件名
     * @return String
     */
    private function get_file_ext($file){
        $r_offset = strrpos($file, '.');
        if($r_offset){
            $ext = substr($file, $r_offset + 1);
        }else{
            $ext = '';
        }
        return $ext;
    }

    /**
     * 文件唯一名称生成
     * @param  Int  $length  随机字符长度, 默认16
     * @return String
     */
    private function uuid($length=16){
        return Helper::uuid($length);
    }
    /**
     * 文件校检
     * @param  array  $file  单个文件数组
     * @return Int  [0校检通过   1上传文件失败   3非法格式文件  4文件超过最大值，5文件小于最小值]
     */
    private function file_check($file){
        # 上传文件是否存在失败
        if($file['error'] !== 0){
            return 1;
        }
        # 上传文件是否存在不合法的格式文件
        if($this->ext != NULL || $this->mime != NULL){
            if(@!(in_array($this->get_file_ext($file['name']), $this->ext) || in_array($file['type'], $this->mime))){
                return 3;
            }
        }
        # 上传文件的大小不符合规定的大小
        if($this->size_max != NULL){
            if($file['size'] > $this->size_max){
                return 4;
            }
        }
        if($this->size_min !=null){
            if( $file['size'] < $this->size_min ){
                return 5;
            }
        }
        return 0;
    }

    /** ------------------------------------------------------------------
     * 获取对应错误码的提示信息
     * @param int $code
     * @return string
     *--------------------------------------------------------------------*/
    public function getError($code){
        return $this->error[$code] ?? '';
    }
    /** ------------------------------------------------------------------
     * 获取所有错误信息
     * @return array
     *--------------------------------------------------------------------*/
    public function getAllError(){
        return $this->error;
    }
}
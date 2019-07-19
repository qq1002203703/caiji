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
use extend\Curl;
use extend\Helper;
use shell\CaijiCommon;

class Douban extends CaijiCommon
{
    //原此的sitemap_index文件
    private $sitemap_index=ROOT.'/douban/'.'sitemap_index.xml';
    //下载回来的未解压的sitemap.gz所保存的路径
    private $sitemap_gz_path=ROOT.'/douban/source/';
    //有电影的sitemap保存的路径
    private $sitemap_movie_path=ROOT.'/douban/movie/';
    //豆瓣电影所在的数据表
    protected $table='caiji_douban';


    //第一步：从sitemap_index.xml中获取并下载所有的压缩的sitemap.gz文件
    public function downloadSitemap(){
        $file = fopen($this->sitemap_index, "rb");
        $curl=new Curl();
        $i=1;
        $false=[];
        while(! feof($file)) {
            $item=trim(fgets($file));
            $fileName=basename($item);
            echo $i.'=>'.$item.PHP_EOL;
            $ret=$curl->download($item,$this->sitemap_gz_path.$fileName);
            if($ret)
                echo '  true'.PHP_EOL;
            else
                $false[]=$item;
        }
        fclose($file);
        if($false){
            echo '失败的文件如下：'.PHP_EOL;
            foreach ($false as $item){
                echo $item.PHP_EOL;
            }
        }
    }
    //第二步：把第一步下载回来的手动解压
    //第三步：从这些解压后的文件中选出有电影的文件,并转移动到有电影的文件夹中
    public function getMovieSitemap(){
        for ($i=0;$i<=5115;$i++){
            $name='sitemap'.$i.'.xml';
            $file=$this->sitemap_gz_path.$name;
            if(is_file($file) && ($str=file_get_contents($file))){
                if(preg_match('#<loc>https://movie\.douban\.com/subject/\d+/?</loc>#i',$str)>0){
                    echo '含电影：'.$file.PHP_EOL;
                    if( rename($file,$this->sitemap_movie_path.$name))
                        echo '  成功:转移文件'.PHP_EOL;
                    else
                        echo '  失败：转移文件'.PHP_EOL;
                }else
                    echo '不含电影：'.$file.PHP_EOL;
            }
        }
    }
    //第四步：读取每一个有电影的sitemap,从中提取电影链接
    public function getUrl(){
        if ($handle = opendir($this->sitemap_movie_path)) {
            $i=0;
            while (false !== ($file = readdir($handle))) {
                if($file!=='.' && $file!=='..'){
                    $file=$this->sitemap_movie_path.trim($file);
                    echo $i.'=>'.$file.PHP_EOL;
                    if(is_file($file) && ($str=file_get_contents($file))){
                        preg_match_all('#<loc>(https://movie\.douban\.com/subject/(\d+)/?)</loc>#i',$str,$match);
                        if(isset($match[1])){
                            //入库
                            foreach ($match[1] as $k => $v){
                                echo '      '.$v.'=>';
                                if($this->model->from($this->table)->eq('from_id',$match[2][$k])->find(null,true)){
                                    echo '  数据库中已经存在'.PHP_EOL;
                                    continue;
                                }
                                if($this->model->from($this->table)->insert([
                                    'url'=>$v,
                                    'caiji_name'=>'douban',
                                    'from_id'=>$match[2][$k],
                                ])){
                                    echo '  入库成功'.PHP_EOL;
                                }else{
                                    echo '  入库失败'.PHP_EOL;
                                    echo '  '.$file.PHP_EOL;
                                    echo '  '.$this->model->getSql().PHP_EOL;
                                    exit();
                                }
                            }
                            $i++;
                        }
                    }
                }

            }
            closedir($handle);
        }

    }

    //直接从豆瓣电影所有分类中采集电影链接
    public function page(){
        $caijiRule=$this->getCaijiRules('douban','page','');
        $this->dieEcho($caijiRule===false,'规则名不正确'.PHP_EOL);
        $file = fopen(ROOT.'/douban/'.'tt.txt', "rb");
        $i=0;
        while(! feof($file)) {
            $item=trim(fgets($file));
            echo $i.'=>'.$item.PHP_EOL;
            $caijiRule['url']=$item.'&start={%0,0,51,20,0,0%}';
            $callback=Helper::callback($caijiRule['callback'].'::create',[$caijiRule]);
            if(is_object($callback)) {
                $callback->start();
            }else{
                echo '回调函数加载失败'.PHP_EOL;
            }
            $i++;
        }
        fclose($file);
    }
    public function content(){
        $this->caiji('douban','content'/*,['url'=>'http://checkip.amazonaws.com/']*/);
    }
    public function download(){
        $this->caiji('douban','download'/*,['url'=>'http://checkip.amazonaws.com/']*/);
    }

 /*---插件区--------------------------------------------------------------------------------------------------------*/

    //内容或代理插件：检测结果是否正确，不正确就表明ip被封锁
   static public function check_result($html){
       $pos=strpos($html,'检测到有异常请求从你的 IP 发出');
       return ($pos===false);
   }

    //内容插件：获取代理ip
    static public function get_proxy(){
        $model=app('\core\Model');
        do{
            $data=$model->from('proxy')->select('id,ip,port,type') -> eq('status',1)->order('id')->find(null,true);
            $ret=false;
            if($data)
                $ret=$model->from('proxy')->eq('id',$data['id'])->update(['status'=>2]);
        }while($data && $ret==false);
        return $data;
    }

    //内容或代理插件：检测代理是否可用
    static public function check_proxy($proxy){
        if(!isset($proxy['ip']) || !isset($proxy['port']) || !isset($proxy['type']))
            return false;
        $type=['http'=>CURLPROXY_HTTP,'socks5'=>CURLPROXY_SOCKS5];
        Helper::curl_request('http://www.uuhuihui.com/uploads/index.html',$status,[
            CURLOPT_TIMEOUT=>7,
            CURLOPT_CONNECTTIMEOUT=>3,
            CURLOPT_HEADER=>true,
            CURLOPT_NOBODY=>true,
            CURLOPT_PROXY=>$proxy['ip'],
            CURLOPT_PROXYPORT=>$proxy['port'],
            CURLOPT_PROXYTYPE=>$type[$proxy['type']],
        ]);
        return $status;
    }

    //下载插件：如果是默认点位图就去掉
    static public function check_img($data){
        preg_match('#(movie|tv)\_default#',$data['true_url']);
        if(preg_match('#(movie|tv)\_default#',$data['true_url'])>0)
            $data['true_url']='';
        return $data;
    }
/*==后期===========================================*/
    //更改文件路径
    public function chang_path(){
        $where=[['isdo','eq',0]];
        $table='caiji_douban_download';
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->doLoop($total,function ($perPage,$i)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table){
            echo '正在处理：id=>'.$item['id'].'-----------'.PHP_EOL;
            $data=[];
            $path='/uploads/images/video/'.get_path_from_id($item['cid']);
            //建立文件夹
            $dir = ROOT.'/public'.$path;
            if (!is_dir($dir)) {
                //致命错误：无法建立文件夹
                if(!mkdir($dir, 0755, true)){
                    echo '  失败：无法建立文件夹'.PHP_EOL;
                    exit();
                }
            }
            $path.='/'.basename($item['replace_path']);
            $data['replace_path']=$path;
            $data['save_path']='public'.$path;
            if(!is_file(ROOT.'/'.$item['save_path'])){
                echo '失败：文件不存在！'.PHP_EOL;
                $data['replace_path']='';
                $data['save_path']='';
            }else{
                //致命错误：无法重命名
                if(!rename(ROOT.'/'.$item['save_path'],ROOT.'/'.$data['save_path'])){
                    echo '  失败：文件重命名失败=>'.$item['id'].PHP_EOL;
                    exit();
                }
            }
            $data['isdo']=1;
            if($this->model->from($table)->eq('id',$item['id'])->update($data)){
                echo '  成功：更新了douban_download表=>'.$item['id'].PHP_EOL;
                if($this->model->from('caiji_douban')->eq('id',$item['cid'])->update([
                    'thumb'=>$data['replace_path'],
                    'isdownload'=>1
                ])){
                    echo '  成功：更新了douban表=>'.$item['cid'].PHP_EOL;
                }else
                    echo '  失败：更新不了douban表=>'.$item['cid'].PHP_EOL;
            } else
                echo '  失败：更新不了douban_download表=>'.$item['id'].PHP_EOL;
        });
    }

    public function del_repeat(){
        $where=[['isdo','eq',0]];
        $table='caiji_douban_download';
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->doLoop($total,function ($perPage,$i)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table){
            echo '正在处理：id=>'.$item['id'].'-----------'.PHP_EOL;
            //找出同一项
            $other=$this->model->from($table)->eq('cid',$item['cid'])->gt('id',$item['id'])->find(null,true);
            if($other){
                $isDo=false;
                $id=0;
                if($item['status']==1){
                    if($this->model->from($table)->eq('id',$other['id'])->delete()){
                        echo '  成功 : 删除(a)：id=>'.$other['id'].PHP_EOL;
                        $isDo=true;
                        $id=$item['id'];
                    } else{
                        echo '  失败 : 删除(a)：id=>'.$other['id'].PHP_EOL;
                    }
                }elseif ($other['status']==1){
                    if($this->model->from($table)->eq('id',$item['id'])->delete()){
                        echo '  成功 : 删除(b)：id=>'.$item['id'].PHP_EOL;
                        $isDo=true;
                        $id=$other['id'];
                    } else{
                        echo '  失败 : 删除(b)：id=>'.$item['id'].PHP_EOL;
                    }
                }else{
                    if($this->model->from($table)->eq('id',$other['id'])->delete()){
                        echo '  成功 : 删除(c)：id=>'.$other['id'].PHP_EOL;
                        $isDo=true;
                        $id=$item['id'];
                    } else{
                        echo '  失败 : 删除(c)：id=>'.$other['id'].PHP_EOL;
                    }
                }
                if($isDo){
                    if($this->model->from($table)->eq('id',$id)->update(['isdo'=>1]))
                        echo '  成功 : 更新：id=>'.$id.PHP_EOL;
                    else
                        echo '  失败 : 更新：id=>'.$id.PHP_EOL;
                }else{
                    echo '  失败 : 删除不成功'.$id.PHP_EOL;
                    exit();
                }
            }else{
                if($this->model->from($table)->eq('id',$item['id'])->update(['isdo'=>1]))
                    echo '  成功 : 更新(x)：id=>'.$item['id'].PHP_EOL;
                else{
                    echo '  失败 : 更新(x)：id=>'.$item['id'].PHP_EOL;
                    exit();
                }

            }
            //msleep(1500);
            //exit();
        });
    }
    //删除点位图
    public function del_kong(){
        $arr=['5d103a2b459fa8c8','5d103a4af16cba7a','5d103a6b4f8fabf6','5d103a6c65c5e440','5d103a6d8930bbd0','5d103a6ea7cf3972','5d103a29f149fbfa','5d103a319df02b06','5d103a422c8325bf','5d103a616a0db096','5d103a3983e8de93','5d103a4111558bac','5d103a6978189dd7',];
        foreach ($arr as $key=> $item){
            echo '开始处理key=>'.$key.',item=>'.$item.PHP_EOL;
            $data=$this->model->from('caiji_douban_download')->like('save_path','%'.$item.'%')->findAll(true);
            //echo $this->model->getSql().PHP_EOL;
            if(!$data){
                echo '  失败：不存在的 save_path'.PHP_EOL;
                exit();
            }
            if(count($data)>1){
                echo '  失败：有多个结果'.PHP_EOL;
                exit();
            }
            if($this->model->from('caiji_douban')->eq('id',$data[0]['cid'])->update(['thumb'=>''])){
                echo '  成功：更新douban表'.PHP_EOL;
                if(unlink(ROOT.'/'.$data[0]['save_path']))
                    echo '  成功：删除'.$data[0]['save_path'].PHP_EOL;
                else
                    echo '  失败：删除'.$data[0]['save_path'].PHP_EOL;
            }else{
                echo '  失败：更新douban表'.PHP_EOL;
            }
        }
    }

}
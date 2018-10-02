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
 *
 * ======================================*/


namespace app\admin\model;
use core\Model;
class Option extends Model
{
    /**
     * @var string 表名
     */
    public $table='option';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'name';
    protected $auto_incremen=false;

    /**
     * 检查名称在数据库中是否已经存在
     * @param  string $name
     * @return bool：存在返回false,不存在返回true
     */
    public function checkName($name){
        return ($this->reset()->eq('name',$name)  ->find()===false)?true:false;
    }

    public function update_option($data){
        foreach ($data as $k =>$v){
            $this->reset()->eq('name',$k)->update(['value'=>$v]);
        }
        $this->update_cache();
    }
    //添加数据
    public function add($data){
        $ret=$this->insert([
            'name'=>$data['name'],
            'status'=>$data['status']??1,
            'description'=>$data['description'] ?? '',
            'value'=>$data['value'] ?? ''
        ]);
        if($ret)
            $this->update_cache();
        return $ret;
    }

    /**------------------------------------------------------------------
     * 更新缓存
     *--------------------------------------------------------------------*/
    public function update_cache(){
        $result=$this->reset()->select('name,value')->eq('status',1)->findAll(true);
        $data=[];
        foreach ($result as $v){
            switch ($v['name']){
                case 'counts':
                case 'city':
                    $data[$v['name']]=json_decode($v['value'],true);
                    break;
                default:
                    $data[$v['name']]=$v['value'];
            }
        }
        $str="<?php \n return ".var_export($data,true).';';
        \core\lib\cache\File::write(ROOT.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'site.php',$str);
    }

    public function set_city($id,$type=0){
        $data=['id'=>$id,'typ'=>$type];
       return $this->eq('name','city')->update( ['value'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
    }
}
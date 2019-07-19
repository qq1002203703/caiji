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
use core\lib\cache\File;
class Option extends Model
{
    /**
     * @var string 表名
     */
    public $table='option';
    /**
     * @var string 主键名
     */
    public $primaryKey = 'id';
    public $type='site';

    public function setType($type){
        $this->type=$type;
        return $this;
    }

    /**
     * 检查名称在数据库中是否已经存在
     * @param  string $name
     * @return bool 存在返回false,不存在返回true
     */
    public function checkName($name){
        return ($this->reset()->eq('name',$name) ->eq('type',$this->type) ->find()===false)?true:false;
    }

    public function update_option($data){
        foreach ($data as $k =>$v){
            $this->reset()->eq('name',$k)->eq('type',$this->type)->update(['value'=>$v]);
        }
        $this->update_cache();
    }
    //添加数据
    public function add($data){
        $ret=$this->insert([
            'name'=>$data['name'],
            'type'=>$data['type'],
            'status'=>$data['status']??1,
            'description'=>$data['description'] ?? '',
            'json'=>$data['json'] ?? 0,
            'value'=>$data['value'] ?? '',
        ]);
        if($ret)
            $this->update_cache();
        return $ret;
    }

    /**------------------------------------------------------------------
     * 更新缓存
     *--------------------------------------------------------------------*/
    public function update_cache(){
        $result=$this->reset()->select('name,value,json')->eq('type',$this->type)->eq('status',1)->findAll(true);
        $data=[];
        foreach ($result as $item){
            $data[$item['name']]=$item['json'] ? json_decode($item['value'],true) : $item['value'];
        }
        unset($result);
        $str="<?php \n return ".var_export($data,true).';';
        File::write(ROOT.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$this->type.'.php',$str);
    }

    public function del($name){
        $ret=$this->eq('type',$this->type)->eq('name',$name)->delete();
        if($ret >0)
            $this->update_cache();
        return $ret;
    }

    public function set_city($id,$type=0){
        $data=['id'=>$id,'typ'=>$type];
       return $this->eq('type',$this->type)->eq('name','city')->update( ['value'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
    }
}
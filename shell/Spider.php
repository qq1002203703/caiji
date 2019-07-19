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


namespace shell;

use core\lib\cache\File;
use core\Conf;
use extend\HttpClient;
abstract class Spider extends BaseCommon
{
    protected $path='cache/shell/caiji/';
    protected $fileBodyName;
    protected $task=1;
    protected $stopFile;
    protected $taskName;
    //不需要发布字段
    public $fieldsFilter;
    /**
     * @var HttpClient
     */
    public $client;

    /** ------------------------------------------------------------------
     * init
     * @param array $write_data  第一次运行写入到进度文件的数据
     * [
        'current_id'=>0,
        'total'=>0,
        'current_page'=>1,
        'save_question'=>0,
        'next'=>'',
        'group_name'=>'',
        'is_end'=>0,
     * ]
     * @param string $func 本类下的一个函数名，额外运行加载
     *---------------------------------------------------------------------*/
    protected function init($write_data,$func=''){
        //停止文件处理
        $this->stopFile=ROOT.'/'.$this->path.'stop/'.$this->fileBodyName;
        if(is_file($this->stopFile.'.lock')){
            rename($this->stopFile.'.lock',$this->stopFile);
        }else{
            if(!is_file($this->stopFile))
                File::write($this->stopFile,'');
        }
        //进度保存文件建立
        $this->taskName=$this->fileBodyName.'_task_'.$this->task;
        if(!is_file(ROOT.'/'.$this->path.$this->taskName.'.php'))
            Conf::write($write_data,$this->taskName,$this->path);
        if($func){
            $this->$func();
        }
    }

    protected function checkStop(){
        return is_file($this->stopFile.'.lock');
    }

    protected function newClient($opt=[]){
        if(!$this->client){
            $this->client=new HttpClient($opt);
        }
    }

    protected function getFields($table){
        $dbName=Conf::get('database_name','database');
        $sql='select COLUMN_NAME as name from INFORMATION_SCHEMA.Columns where table_name=\''.$this->prefix.$table.'\' and table_schema=\''.$dbName.'\'';
        $result=$this->model->_sql($sql,[],false,false);
        if(!$result)
            return '';
        $ret=[];
        foreach ($result as $item){
            if(! in_array($item['name'],$this->fieldsFilter)){
                $ret[]=$item['name'];
            }
        }
        return implode(',',$ret);
    }

    /** ------------------------------------------------------------------
     * 设置过滤字段
     * 处理$this->option['fields']中的每一项，如果在$this->option['fieldsFilter']中存在就去掉，不存在的就加入
     *---------------------------------------------------------------------*/
    protected function setFields($fields,$default=null){
        if($default!==null)
            $this->fieldsFilter=$default;
        if($fields){
            $isUnset=false;
            foreach ($fields as $item){
                $key=array_search($item,$this->fieldsFilter,true);
                if($key===false){
                    $this->fieldsFilter[]=$item;
                }else{
                    unset($this->fieldsFilter[$key]);
                    if(!$isUnset)
                        $isUnset=true;
                }
            }
            if($isUnset)
                $this->fieldsFilter=array_values($this->fieldsFilter);
        }

    }
}
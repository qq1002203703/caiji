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


namespace app\bbs\model;

use app\admin\model\Category;
use core\Tree;

class BbsCategory extends Category
{
    public $type='bbs_normal';

    /**
     * 输出所有分类格式化为table样式的字符串
     * @return string
     */
    public function getTreeTable(){
        return $this->getTree(['id','name','description','list_order'],
            '<tr><td>%id%</td><td>%__repeat_content__%</td><td>%description%</td><td align="center"><a class="pure-button btn-success btn-xs edit" href="javascript:;" data="%id%">编辑</a> <a class="pure-button btn-warning btn-xs delete" href="javascript:;" data="%id%">删除</a></td></tr>');
    }

    /**
     * 输出所有分类格式化为select样式的字符串
     * @param int $select 选中第几项 默认选中第1项
     * @return string
     */
    public function getTreeSelect($select=1){
        return $this->getTree(['id','name'],'<option value="%id%"%select%>%__repeat_content__%</option>',['id'=>$select,'text'=>' selected="selected"']);
    }

    /**-------------------------------------------------------------
     * 除自己及子孙外，其他所有节点的树结构
     * @param int $id
     * @return string
     * --------------------------------------------------------------*/
    public function getTreeNotIn($id){
        $cat=$this->getById($id);
        $data=$this->notlike('path',$cat['path'].'%')->findAll(true);
        if($data){
            $tree= new Tree($data,['rootId' => 0, 'id' => 'id', 'parent' => 'pid']);
            return $tree->getTree(0,true,'name',['id','name'],'<option value="%id%"%select%>%__repeat_content__%</option>',['id'=>$cat['pid'],'text'=>' selected="selected"']);
        }
        return '';
    }

}
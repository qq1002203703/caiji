<?php
namespace app\video\model;
use core\Tree;
class Category extends \app\admin\model\Category
{
    public $type='video';

    /**
     * 分类格式化为select字符串
     * @param int|string $select 默认select项的id
     * @return string
     */
    public function getTreeSelect($select=1){
        return $this->getTree(['id','name'],'<option value="%id%"%select%>%__repeat_content__%</option>',['id'=>$select,'text'=>' selected="selected"']);
    }

    /**
     * 分类格式化为table的字符串
     * @return string
     */
    public function getTreeTable(){
        return $this->getTree(['id','name','seo_title','seo_keywords','seo_description','list_order'],
            '<tr id="c%id%">
                <td><input name="id[]" id="id_%id%" type="checkbox" value="%id%"></td>
                <td>%id%</td>
                <td><a post-id="%id%" id="post-id-%id%">%__repeat_content__%</a></td>
                <td>%seo_title%</td>
                <td>%seo_keywords%</td>
                <td>%seo_description%</td>
                <td>
                    <i class="layui-icon" style="font-size: 20px;"><a title="修改" id="edita_%id%">&#xe642;</a></i>
                    <i class="layui-icon" style="font-size: 20px;"><a title="删除" id="deletea_%id%" href="javascript:;" ac="%id%">&#xe640;</a></i>
                </td>
             </tr>');
    }

    /**
     * 除自己及子孙外，其他所有节点的树结构
     */
    public function getTreeNotIn($id){
        $cat=$this->getById($id);
        $data=$this->eq('type',$this->type)->notlike('path',$cat['path'].'%')->findAll(true);
        if($data){
            $tree= new Tree($data,['rootId' => 0, 'id' => 'id', 'parent' => 'pid']);
            return $tree->getTree(0,true,'name',['id','name'],'<option value="%id%"%select%>%__repeat_content__%</option>',['id'=>$cat['pid'],'text'=>' selected="selected"']);
        }
        return '';
    }

    /** ---------------------------------------------------------------------
     * 分类面包屑
     * @param int $id
     * @return string
     *-----------------------------------------------------------------------*/
    public function bread($id,$type){
        $tree=$this->newTree();
        $catNode=$tree->getNodeById($id);
        $nodeArray=$catNode->getAncestorsAndSelf();
        return $tree->getTreeAny(array_reverse($nodeArray),0,'name',['id','name','slug'],'<a href="'.url('@'.$type.'@','slug=%slug%').'">%name%</a>&gt;','');
    }
}
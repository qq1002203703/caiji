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


namespace app\admin\model;


use core\Model;
use extend\Helper;
class KeywordLink extends Model
{

    public $table='keyword_link';
    public $primaryKey='id';

    /** ------------------------------------------------------------------
     * 检测一个关键词在数据库是否不存在
     * @param string $keyword
     * @param int $id
     * @return bool  在数据库存在返回false,否则返回true
     *--------------------------------------------------------------------*/
    public function checkKeyword($keyword,$id=0){
            if($id) $this->ne('id',$id);
            $ret=$this->eq('keyword',$keyword) ->find();
            return ($ret===false)?true:false;
    }

    /** ------------------------------------------------------------------
     * 添加锚文本
     * @param array $data
     * @return bool|int
     *--------------------------------------------------------------------*/
    public function add($data){
            $data=$this->_filterData($data);
            $data['words']=mb_strlen($data['keyword']);
            return $this->insert($data);
    }
    /** ------------------------------------------------------------------
     * 修改锚文本
     * @param array $data
     * @return int
     *--------------------------------------------------------------------*/
    public function edit($data){
        $data=$this->_filterDataE($data);
        if(isset($data['keyword'])){
            $data['words']=mb_strlen($data['keyword']);
        }
        return $this->update($data);
    }

    /** ------------------------------------------------------------------
     * 删除一条或多条锚文本
     * @param int|string|array $id
     * @return bool|string
     *---------------------------------------------------------------------*/
    public function del($id){
       if(is_int($id)){
           $ret=$this->eq('id',$id)->delete();
           return $ret>0 ? true :'不存在的id';
       }

       if(is_string($id)){
           $ids=explode(',',$id);
           if(count($ids)==1){
               $ret=$this->eq('id',$id)->delete();
               return $ret>0 ? true :'不存在的id';
           }else{
               $id=$ids;
               unset($ids);
           }
       }
       if(is_array($id)){
           $ret=$this->in('id',$id)->delete();
           return $ret>0 ? true :'不存在的id';
       }
       return 'id格式不符';
    }

    /** ------------------------------------------------------------------
     * 循环读取整个数据表的链接，对原内容进行逐个添加锚文本
     * @param string $content 原内容
     * @param array $currentData 原内容的补充数据,如果不提供id或table将不更新原数据在数据库的值，补充数据包括下面四项：
     *     url:原数据的网址，默认为空字符串；
     *     id，原数据在表中的id,默认为0，
     *     table：原数据所在的数据表，默认为空字符；
     *     tagContent $contend在数据表中字段名，默认为'content'
     * @return int 总共替换的次数
     *---------------------------------------------------------------------*/
    public function doLoop(&$content,$currentData=[]){
        $perPage=50;
        $total=$this->count(['where'=>[['status','eq',1]]]);
        $count=0;
        $currentData['table']=$currentData['table'] ?? '';
        $currentData['id']=$currentData['id'] ?? 0;
        if($total >0){
            $num=ceil($total/$perPage);
            for($i=0;$i<$num;$i++){
                $data=$this->select('id,url,keyword')->eq('status',1)->limit($i*$perPage,$perPage)->order('weight desc,words desc,id desc')->findAll(true);
                if(!$data)
                    break;
                $count+=$this->replace($content,$data,$currentData);
            }
        }
        //更新内容页
        if($count >0 && $currentData['table'] && $currentData['id']){
            $tagContent=$currentData['tagContent'] ?? 'content';
            $this->from($currentData['table'])->eq('id',$currentData['id'])->update([$tagContent=>$content]);
        }
        return $count;
    }
    /** ------------------------------------------------------------------
     * 关键词替换
     * @param $content
     * @param $kwLinks
     * @param array $currentData
     * @return int
     *--------------------------------------------------------------------*/
    public function replace(&$content,$kwLinks,$currentData=[]){
        //标签过滤
        $i=-1;
        $ignore_match=[];
        $content=preg_replace_callback([
            '#<pre[^>]*>.*?</pre>#is', //pre标签
            '#<a[^>]*>.*?</a>#is', //a标签
            '#<[^<>]+>#'  //html标签内部
        ],function ($mat)use (&$i,&$ignore_replace,&$ignore_match){
            $i++;
            $ignore_replace[$i]=$mat[0];
            $ignore_match[$i]='{%ignore_place_'.$i.'%}';
            return $ignore_match[$i];
        },$content);
        $currentData['url']=$currentData['url'] ?? '';
        $count=0;
        foreach ($kwLinks as $link){
            //当前页面url跟关键词链接相同不作替换
            if($currentData['url'] && $currentData['url']==$link['url'])
                continue;
            //已经有内链跳过
            if($this->isExist($ignore_replace,$link['keyword']))
                continue;
            $i++;
            $replace='{%ignore_place_'.$i.'%}';
            $content=Helper::str_replace_once($link['keyword'],$replace,$content,$isDo);
            if($isDo){
                $ignore_replace[$i]='<a href="'.$link['url'].'">'.$link['keyword'].'</a>';
                $ignore_match[$i]=$replace;
                $count++;
                //更新数据库
                if(isset($currentData['id']) && isset($currentData['table']) && $currentData['id'] && $currentData['table']){
                    if($this->from('keyword_link_map')->insert([
                        'lid'=>$link['id'],
                        'oid'=>$currentData['id'],
                        'table_name'=>$currentData['table']
                    ]))
                        $this->setField('num',1,[['id','eq',$link['id']]]);
                }
            }else
                $i--;
        }
        if($i>-1){
            $content=str_replace($ignore_match,$ignore_replace,$content);
        }
        return $count;
    }

    /** ------------------------------------------------------------------
     * 检测关键词是否已经存在锚文本
     * @param array $ignores 过滤的标签集合
     * @param string $keyword 关键词
     * @return bool
     *--------------------------------------------------------------------*/
    protected function isExist($ignores,$keyword){
        if($ignores===null)
            return false;
        $safeKeyword=preg_quote($keyword,'#');
        foreach ($ignores as $ignore){
            if(@preg_match('#<a [^>]+>'.$safeKeyword.'</a>#i',$ignore) >0)
                return true;
        }
        return false;
    }
}
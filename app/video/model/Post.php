<?php
namespace app\video\model;
use app\common\model\Func;
use core\Conf;
use core\Model;
use extend\Download;

class Post extends Model
{
    public $table='video';
    public $primaryKey = 'id';
    public $extendData=[
        'playType'=>[
            'm3u8'=>'m3u8云播',
            'xfplay'=>'先锋影音',
            'baidupan'=>'百度云盘',
            'xigua'=>'西瓜影音',
            'xunlei'=>'迅雷下载',
        ],
        'videoType'=>['电影','电视']
    ];
    /**
     * 添加内容
     * @param array $data: 已经验证的数据
     * @return bool|int:成功返回插入的id，否则返回false
     */
    public function add($data){
        $tags=$data['tags'] ?? '';
        $data=$this->filter($data);
        $id=$this->insert($data);
        if($id >0){
            $tagModel=app('\app\admin\model\Tag');
            //标签
            $tagModel->addTagMap($tags,$id,'video');
            //导演
            if(isset($data['director']) && $data['director'])
                $tagModel->addTagMap(explode(',',$data['director']),$id,'video_director',false,1);
            //演员
            if(isset($data['actor']) && $data['actor'])
                $tagModel->addTagMap(explode(',',$data['actor']),$id,'video_actor',false,1);
            //制片人
            if(isset($data['producer']) && $data['producer'])
                $tagModel->addTagMap(explode(',',$data['producer']),$id,'video_producer',false,1);
            unset($tagModel);
            //关键词自动添加锚文本
            if(Conf::get('keyword_link','video')==1){
                $model=app('\app\admin\model\KeywordLink');
                $model->doLoop($data['content'],[
                    'id'=>$id,
                    'url'=>url('@video@',['id'=>$id]),
                    'table'=>$this->table,
                    'tagContent'=>'content',
                ]);
                unset($model);
            }
        }
        return $id;
    }
    /**
     * 编辑内容
     * @param array $data
     * @return bool|int
     */
    public function edit($data){
        if(isset($data['tags'])) //不要tags
            unset($data['tags']);
        $data=$this->filter($data,true);
        $primaryKey=$data[$this->primaryKey];
        unset($data[$this->primaryKey]);
        return $this->eq($this->primaryKey,$primaryKey)->update($data);
    }



    /** ------------------------------------------------------------------
     * 三表联合搜索帖子：当前table、user和category三个表
     * @param array $where：当前表字段不用加表前缀，另外两个表的字段如果有重复必须加上表名，如id  [['user.id','eq','xxxx']]
     * @param string|int|array $limit
     * @param string $order 当前表字段不用加表前缀，另外两个表的字段如果有重复必须加上表名
     * @param bool $single 是否只输出一篇
     * @param string $select
     * @return array|bool
     *--------------------------------------------------------------------*/
    public function search($where=[],$limit='0,10',$order='id desc',$single=false,$select=''){
        if(!$select)
            $select=$this->table.'.*,category.name as category_name,category.slug as category_slug,category.thumb as category_thumb';
        else
            $select.=',category.name as category_name,category.slug as category_slug,category.thumb as category_thumb';
        $sql='SELECT '.$select.' FROM `'.self::$prefix.$this->table.'` as '.$this->table.'   left join '.self::$prefix.'category as category on '.$this->table.'.category_id=category.id';
        $where=Func::whereAddTable($where,$this->table);
        $order=Func::orderAddTable($order,$this->table);
        $this->_where($where)->_limit($limit)->order($order);
        $sql.=$this->_buildSql(['where','order','limit']);
        $param=$this->params;
        $this->reset(false);
        return $this->_sql($sql,$param,false,$single);
    }

    /** ------------------------------------------------------------------
     * 获取一个内容
     * @param int|string $primaryKeyValue  主键值
     * @return bool|array
     *--------------------------------------------------------------------*/
    public function getOne($primaryKeyValue){
        $result= $this->search([[$this->primaryKey,'eq',$primaryKeyValue]],1,'',true);
        if(isset($result['thumb_ids']) && $result['thumb_ids']){//图集
            $result['thumb_ids']=$this->_sql('select id,uri from '.self::$prefix.'file where id in ('.$result['thumb_ids'].')',[],false);
        }
        return $result;
    }

    /** ------------------------------------------------------------------
     * 随机帖子获取
     *--------------------------------------------------------------------*/
    public function ramdom(){

    }

    /** ------------------------------------------------------------------
     * 查询标签
     * @param int $oid
     * @return bool|array
     *---------------------------------------------------------------------*/
    public function tags($id,$type){
        $data=$this->_sql('select t.name,t.id from '.self::$prefix.'tag_relation as r left join '.self::$prefix.'tag as t on r.tid=t.id where r.oid=? and r.type=?',[$id,$type],false,false);
        return $data? $data :'';
    }

    public function tagsHtml($id,$type,$router='',$format='<a href="{%url%}">{%name%}</a>'){
        $data=$this->tags($id,$type);
        if($data){
            $ret='';
            if(!$router)
                $router='@tags@';
            foreach ($data as $v){
                $ret.=str_replace(['{%url%}','{%name%}'],[url($router,['name'=>urlencode($v['name'])]),$v['name']],$format);
            }
            return $ret;
        }
        return '';
    }

    /**
     * 数据入库前过滤和处理
     * @param array $data
     * @param bool $isEdit 是否是编辑， true时表示是编辑 要进行的是更新；false时表示不是编辑，要进行的是插入
     * @return array
     */
    public function filter($data,$isEdit=false){
        //缩略图处理
        $this->thumb($data);
        if($isEdit){
            $data=$this->_filterDataE($data);
            unset($data['director'],$data['producer'],$data['actor']);
        }else{
            $data=$this->_filterData($data);
            $this->people($data);
        }
        $this->setDefaultValue($data);
        $this->setCount($data);
        return $data;
    }

    /** ------------------------------------------------------------------
     * people 人物处理
     * @param array $data
     *---------------------------------------------------------------------*/
    public function people(&$data){
        if(isset($data['director']) && $data['director'])
            $data['director']=str_replace('，',',',$data['director']);
        if(isset($data['producer']) && $data['producer'])
            $data['producer']=str_replace('，',',',$data['producer']);
        if(isset($data['actor']) && $data['actor'])
            $data['actor']=str_replace('，',',',$data['actor']);
    }

    /** ------------------------------------------------------------------
     * 缩略图处理
     * @param array $data
     * @return bool
     *--------------------------------------------------------------------*/
    public function thumb(&$data){
        if(isset($data['images_url'])){
            $url='';
            if(is_string($data['images_url'])){
                $ret=$this->thumbItem($data);
                $url=$data['thumb']=$ret['url'];
                $data['thumb_ids']=$ret['id'] ? :'';
            }elseif (is_array($data['images_url'])){
                $cout=count($data['images_url']);
                $download=new Download();
                $fileModel=app('\app\admin\model\File');
                $site_url=Conf::get('site_url','site');
                $reg='#^((?!https?://)|('.$site_url.')).*$#i';
                $ids=[];
                $url='';
                for ($i=0;$i<$cout;$i++){
                    $item=[
                        'images_url'=>$data['images_url'][$i],
                        'images_id'=>$data['images_id'][$i],
                    ];
                    if(isset($data['images_title'][$i]) &&$data['images_title'][$i]){
                        $item['images_title']=$data['images_title'][$i];
                    }
                    $ret=$this->thumbItem($item,$reg,$download,$fileModel);
                    if($url=='' && $ret['url']){
                        $url=$ret['url'];
                    }
                    if($ret['id']){
                        $ids[]=$ret['id'];
                    }
                }
                $data['thumb']=$url;
                $data['thumb_ids']=implode(',',$ids);
            }
            unset($data['images_url']);
            if(isset($data['images_id']))
                unset($data['images_id']);
            if(isset($data['images_title']))
                unset($data['images_title']);
            return $url !=='';
        }
        return false;
    }

    /** ------------------------------------------------------------------
     * 缩略图单项处理
     * @param $imgItem
     * @param null|string $reg
     * @param null|Download $download
     * @param null|\app\admin\model\File $fileModel
     * @return array
     *--------------------------------------------------------------------*/
    protected function thumbItem($imgItem,$reg=null,$download=null,$fileModel=null){
        if(isset($imgItem['images_id']) && $imgItem['images_id'] && !is_array($imgItem['images_id']) ){
            $url=$imgItem['images_url'];
            $id=(int)$imgItem['images_id'];
        }else{
            $id=0;
            $url='';
            $reg=$reg ? : '#^((?!https?://)|('.Conf::get('site_url','site').')).*$#i';
            if(preg_match($reg,$imgItem['images_url'])){//本服务器的图片
                $url=$imgItem['images_url'];
                if(isset($imgItem['images_id']) && $imgItem['images_id'])
                    $id=(int)$imgItem['images_id'];

            }else{ //网络图片
                $download=$download ? : new Download();
                $fileModel=$fileModel ? :app('\app\admin\model\File');
                //下载图片
                $file=$download->down($imgItem['images_url'],'{%Y%}/{%m%}/{%d%}/{%u%}',false);
                if(is_array($file)){
                    //入库
                    $res=$fileModel->add([
                        'uri'=>$file['fileUrl'],
                        'title'=>$imgItem['images_title'] ?? ''
                    ],true,true,'video');
                    if($res){
                        $url=$res['uri'];
                        $id=$res['id'];
                    }
                }
            }
        }
        return ['url'=>$url, 'id'=>$id];
    }



    /** ------------------------------------------------------------------
     * 默认值设置
     * @param array $data
     * @param bool $isEdit
     *--------------------------------------------------------------------*/
    public function setDefaultValue(&$data){
        if(isset($data['status']) && $data['status']==='')
            $data['status']=1;
        if(isset($data['allow_comment']) && $data['allow_comment']==='')
            $data['allow_comment']=1;
        if(isset($data['coin']) && $data['coin']==='')
            $data['coin']=0;
        if(isset($data['money']) && $data['money']==='')
            $data['money']=0;
        if(isset($data['score']) && $data['score']==='')
            $data['score']=0;
        $time=time();
        $data['create_time']=isset($data['create_time']) && $data['create_time'] ? strtotime($data['create_time']): $time;
        $data['published_time']=isset($data['published_time']) && $data['published_time']? strtotime($data['published_time']): $time;
        $data['update_time']=$time;
        $data['date_published']=isset($data['date_published']) && $data['date_published'] ? strtotime($data['date_published']) : 0;
    }

    /**
     * 计数处理
     * @param $data
     */
    public function setCount(&$data){
        $setting=Conf::all('video');
        if(! isset($data['views']) || !$data['views'])
            $data['views']=mt_rand(40,$setting['counts_views']);
        if(! isset($data['likes']) || !$data['likes'])
            $data['likes']=mt_rand(2,$setting['counts_likes']);
    }



    /** ------------------------------------------------------------------
     * 删除文档 支持单篇和批量
     * @param string|array|int $id
     * @return bool|string 成功返回true 失败返回错误信息
     *--------------------------------------------------------------------*/
    public function del($id){
        if(!$id)
            return 'id不能为空';
        if(is_string($id)|| is_int($id)){
            $id=(string)$id;
            if(preg_match('/^(\d[\d,]*)*\d$/',$id)==0)
                return 'id格式不符';
            else
                $id=explode(',',$id);
        }
        $ret=$this->in('id',$id)->delete();
        if($ret){
            //删除标签
            $this->from('tag_relation')->in('oid',$id)->eq('type','video')->delete();
            return true;
        }else{
            return '删除失败,可能是提交了多次删除，已经在上一次删除！';
        }
    }

}
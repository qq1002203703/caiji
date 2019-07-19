<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 内容采集后入库
 * ======================================*/

namespace shell\caiji\plugin;
use extend\Helper;

class Save
{
    /** ------------------------------------------------------------------
     * id为1的采集任务的数据入库方法->用于内容采集后的入库
     * @param array $data
     * @return string
     *--------------------------------------------------------------------*/
    static public function task_1($data){
        /**默认参数设置-----------------------------------*/
        $data['uid']=1;
        $data['create_time']=self::get_date($data['create_time']);
        $data['published_time']= $data['create_time'];
        $data['update_time']=$data['create_time'];
        $id=$data['id'];
        unset($data['id']);
        /**--------------------------------------------------*/
        $model=app('\app\weixinqun\model\Weixinqun');
        //添加标签
        if($data['tags']){
            $model->addTagsMap($data['tags'],$id);
        }
        unset($data['tags']);
        if(!isset($data['content']) || !$data['content']){
            $data['content']=self::getRandomContent($model,\app\weixinqun\model\Weixinqun::$prefix);
        }
        if($data['thumb'] || $data['qrcode'] || $data['qun_qrcode'])
            $data['have_img']=1;
        else
            $data['have_img']=0;
        $data['category_id']=$model->getCategoryId($data['category_id'],1);
        $data['city_id']=$model->getCityId($data['city_id']);
        $ret=$model->eq('id',$id)->update($data);
        if($ret > 0){
            return '发布成功';
        }else
            return '没有更新';
    }
    static public function task_2($data){
        $data['uid']=1;
        $data['create_time']=self::get_date('');
        $data['published_time']= $data['create_time'];
        $data['update_time']=$data['create_time'];
        $id=$data['id'];
        unset($data['id']);
        /*----------------------------*/
        if($data['thumb'] )
            $data['have_img']=1;
        else
            $data['have_img']=0;
        $data['qun_qrcode']=$data['gongzhonghao'];
        unset($data['gongzhonghao']);
        $model=app('\app\weixinqun\model\Weixinqun');
        //$data['category_id']=$model->getCategoryId($data['category_id'],1,'gzh');
        $data['category_id']=$model->getCategoryId('科技',1,'gzh');
        //一会删除
        $data['type']=3;
        self::checkMaxLength($data['content']);
        $ret=$model->eq('id',$id)->update($data);
        if($ret > 0){
            return '发布成功';
        }else
            return '没有更新';
    }

    /** ------------------------------------------------------------------
     * 获取在此随机天数前的日期
     * @param $str_time
     * @return int
     *---------------------------------------------------------------------*/
    static protected function get_date($str_time){
        $time=strtotime($str_time);
        if($time===false){
            return strtotime('-'.mt_rand(3,30).' days');
        }else{
            return $time-mt_rand(3600*24*3,3600*24*25);
        }
    }

    /** ------------------------------------------------------------------
     * 获取随机内容
     * @param \core\Model $model
     * @param string $prefix
     * @return string
     *---------------------------------------------------------------------*/
    static public function getRandomContent($model,$prefix){
        $ret=$model->_sql('SELECT excerpt,id FROM `'.$prefix.'weixinqun` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.$prefix.'weixinqun`))) ORDER BY id LIMIT 4',[],false);
        $content='';
        if($ret){
            foreach ($ret as $item){
                $content.='<p>'.$item['excerpt'].'</p>';
            }
        }
        return $content;
    }

    /** ------------------------------------------------------------------
     * 检测字符串长度有没有超过Mysql text类型最大的长度65535，如果超过就截取
     * @param  string $str
     * @return string
     *--------------------------------------------------------------------*/
    static public function checkMaxLength(&$str,$max=65535){
        $length=strlen($str);
        if($length >$max ){
            $str=mb_strcut($str,0,$max-435);//为保证后面回补p标签，又造成字符串过长，这里特意截少一点
            $str=preg_replace([
                '/\r?\n/',
                '#<p[^>]*?>((?!(</p))[\s\S])*?(?=(<p>|$))#i'
            ],[
                    ' ',
                '$0</p>'
            ],$str);
        }
    }

    static public function checkMaxLength2(&$str,$max=16777215){
        $length=strlen($str);
        while ($length >$max){
            $posion=strrpos($str,'{%|||%}');
            if($posion!==false){
                $str=mb_strcut($str,0,$posion);
            }else{
                self::checkMaxLength($str,$max);
            }
            $length=strlen($str);
        }
    }

    static public function zuanke8($data){
        //这里的$data['content']不可能为空
        //if($data['content']){
            $data['comments_num']=(int)$data['comments_num'];
            $posion=strpos($data['content'],'{%|||%}');
            if($posion===false){
                $str=$data['content'];
            }else{
                $str=mb_strcut($data['content'],0,$posion);
            }
            list($time,$user)=explode('{%||%}',$str);
            $data['create_time']=strtotime($time) ? : time();
            if(strpos($str,'游客，本帖隐藏的内容需要积分')!==false){
                $data['isend']=1;
                $data['islaji']=1;
                $data['content']='';
                $data['login']=1;
            }else{
                if($user=='赚小客'){
                    $data['isend']=1;
                    $data['islaji']=1;
                    $data['content']='';
                }elseif ((time() - $data['create_time']) > 3600*24*7){
                    $data['isend']=1;
                    if($data['comments_num'] < 3){
                        $data['islaji']=1;
                        $data['content']='';
                    }
                }
            }
            self::checkMaxLength2($data['content']);
      /*  }else{
            $data['comments_num']=0;
            $data['update_time']=time();
            $data['times']=$data['times']+1;
        }*/
        $id=$data['id'];
        unset($data['id']);
        $model=new \core\Model();
        $model->table='zuanke8';
        $model->eq('id',$id)->update($data);
    }

    public static function hacpai($data){
        if($data['content']=='' || mb_strlen(strip_tags($data['content'])) <500 ){
            $data['islaji']=1;
            $data['content']='';
        }else{
            self::checkMaxLength2($data['content']);
        }
        $data['isend']=1;
        $id=$data['id'];
        unset($data['id']);
        $model=new \core\Model();
        $model->table='caiji_hacpai';
        $model->eq('id',$id)->update($data);
    }

    //豆瓣 内容采集 入库
    static public function douban_content($data){
        $model=app('\core\Model');
        $id=$data['id'];
        unset($data['id']);
        //隐藏的页面，需要登陆才可以看
        if($data['title']==='页面不存在' || $data['title']==='条目不存在'){
            $data['is_hide']=1;
        }else
            $data['is_hide']=0;
        //电影还是剧集
        if($data['type']!=='')
            $data['type']=1;
        else
            $data['type']=0;
        if(isset($data['content'])){
            self::checkMaxLength($data['content']);
        }
        $model->from('caiji_douban')->eq('id',$id)->update($data);
    }

}
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

use extend\Helper;
use extend\HttpClient;
use extend\Selector;
use extend\translate\Qq;
use extend\translate\Split;
use shell\CaijiCommon;

class Boxcom extends CaijiCommon
{
    /**
     * @var HttpClient
     */
    public $client;
    public function page(){
        $url='https://community.box.com/t5/Archive-Forum/bd-p/ArchiveForums/page/';
        $url2='https://community.box.com/t5/Archive-Forum/bd-p/ArchiveForums';
        $table='caiji_boxcom';
        $this->newClient();
        for ($i=1;$i<178;$i++){
            echo '开始采集第'.$i.'页'.PHP_EOL;
            //1、访问
            if($i==1){
                $res=$this->client->http($url2);
            }else
                $res=$this->client->http($url.$i);
            //检测是不是false
            if($res===false) {
                echo ' 结果为false' . PHP_EOL;
                return -1;//结果为false
            }
            //检测是不是404
            if($this->client->getHttpCode()!==200){
                echo  ' 404错误'.PHP_EOL;
                return -2;//404错误
            }
            //dump($res);
            //结果筛选
            $data=$this->pageSelector($res);
            //判断筛选结果
            if(!$data){
                dump($res);
                echo  ' 筛选出错'.PHP_EOL;
                return -3; //筛选出错
            }
            unset($res);
            //入库
            if($this->pageSave($data,$table)>0){
                echo  ' 保存出错'.PHP_EOL;
                return -4; //保存出错
            }
            //break;
            msleep(2000,300);
        }
        return 0;
    }


    protected function newClient($opt=[]){
        if(!$this->client){
            $this->client=new HttpClient($opt);
        }
    }

    /** ------------------------------------------------------------------
     * 页面采集保存
     * @param array $data
     * @param string $table
     * @return int 出错的次数  没出错为0
     *---------------------------------------------------------------------*/
    protected function pageSave($data,$table){
        $count=0;
        foreach ($data as $item){
            $url_md5=md5($item);
            if($this->model->from($table)->eq('url_md5',$url_md5)->find(null,true))
                continue;
            if(!$this->model->from($table)->insert([
                'url'=>$item,
                'url_md5'=>$url_md5,
            ]))
                $count++;
        }
        return $count;
    }

    protected function pageSelector(&$html){
        return Selector::find($html,'regex,multi','#<h2 itemprop="name" class="message-subject">\s*<span class="lia-message-unread">\s*<a class="page-link lia-link-navigation lia-custom-event" id="link\_\d+" href="(?P<url>[^"]+)">#','url','<div id="messageList" class="MessageList lia-component-forums-widget-message-list lia-forum-message-list lia-component-message-list">{%|||%}<ul class="lia-paging-full">');
    }

    public function content(){
        $table='caiji_boxcom';
        $where=[['iscaiji','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $this->newClient();
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table){
            //采集开始
            echo '开始采集: id=>'.$item['id'].',url=>'.$item['url'].'   ------------'. PHP_EOL;
            //1、访问
            $res=$this->client->http('https://community.box.com'.$item['url']);
            //检测是不是false
            if($res===false) {
                echo ' 结果为false' . PHP_EOL;
                exit();//结果为false
            }
            //检测是不是404
            if($this->client->getHttpCode()!==200){
                echo  ' 404错误'.PHP_EOL;
                exit();//404错误
            }
            //dump($res);
            //结果筛选
            $data=$this->contentSelector($res);
            //判断筛选结果
            if(!$data){
                echo  ' 筛选出错'.PHP_EOL;
                exit();//筛选出错
            }
            unset($res);
            //入库
            if(!$this->contentSave($data,$item['id'],$table)){
                echo $this->model->getSql().PHP_EOL;
                echo  ' 保存出错'.PHP_EOL;
                exit(); //保存出错
            }
            msleep(800,200);
        });
    }

    /** ------------------------------------------------------------------
     * 内容筛选器
     * @param string $html
     * @return array|bool
     *---------------------------------------------------------------------*/
    protected function contentSelector(&$html){
        $title=str_replace(' - Box','',Helper::strCut($html,'","subject":"','"',false));
        //dump($title);
        if(!$title)
            return false;
        //$content=Helper::strCut($html,'<div class="lia-message-body-content">','</div>',false);
        $content=Selector::find($html,'regex,multi','#<div class="lia-message-body-content">(?P<content>[\s\S]+?)</div>#','content','<div class="linear-message-list message-list">{%|||%}<div class="lia-menu-bar lia-menu-bar-bottom bottom-block">');
        //过滤
        //dump($content);
        //exit();
        if($content){
            $content=implode('{%|||%}',$this->contentFilter($content));
        }
        return [
            'title'=>$title,
            'content'=>$content,
        ];
    }

    /** ------------------------------------------------------------------
     * 内容过滤器
     * @param array $contents
     * @return array
     *---------------------------------------------------------------------*/
    protected function contentFilter($contents){
        foreach ($contents as  $k=>$item){
            $contents[$k]=strip_tags(preg_replace([
                '#<div style="width:100%; max-height:48px; overflow:hidden;"[\s\S]+#',
                '#<br/>#i',
                '#<p [^>]*>#i',
                '#(<br>){2,}#i',
                '#<p>\s*(<br>|&nbsp;)*\s*</p>#i',
                '#\s{2,}#',
                '#<(/?)P>#'
            ],[
                '',
                '<br>',
                '<p>',
                '<br>',
                '',
                ' ',
                '<$1p>'
            ],$item),'<p><br>');
        }
        return $contents;
    }

    protected function contentSave($data,$id,$table){
        $data['iscaiji']=1;
        return $this->model->from($table)->eq('id',$id)->update($data);
    }

    public function fanyi(){
        $table='caiji_boxcom';
        $where=[['iscaiji','eq',1],['isdone','eq',0]];
        $total=$this->model->count([
            'from'=>$table,
            'where'=>$where
        ]);
        $fanyi=New Qq();
        if(!$fanyi->sign()) {
            echo '  翻译器初始化失败！';
            return;
        }
        $this->doLoop($total,function ($perPage)use ($table,$where){
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        },function ($item)use ($table,$fanyi){
            echo '  开始处理：id=>'.$item['id'].'-------------'.PHP_EOL;
            //dump($item);
            $update=['isdone'=>1];
            if($item['content']){
                $res=$fanyi->translate($item['title'].'{%|||%}'.$item['content']);
                if($res===$fanyi){
                    exit($fanyi->error);
                }
                file_put_contents(ROOT.'/1.txt',$res);
                $pos=stripos($res,'{%|||%}');
                if($pos===$fanyi)
                    exit('翻译返回的内容没有{%|||%}');
                $update['title']=substr($res,0,$pos);
                $update['content']=substr($res,$pos+7);
            }
            if($this->model->from($table)->eq('id',$item['id'])->update($update))
                echo '  成功：更新id=>'.$item['id'].'-------------'.PHP_EOL;
            else{
                echo '  失败：更新id=>'.$item['id'].'-------------'.PHP_EOL;
                $this->model->from($table)->eq('id',$item['id'])->update(['isdone'=>1,'isfabu'=>1]);
                //exit();
            }

            msleep(2000,500);
            //exit();
        });
    }

    public function fabu()
    {
        $this->newClient();
        $table = 'caiji_boxcom';
        $where = [['isfabu', 'eq', 0], ['iscaiji', 'eq', 1]];
        $total = $this->model->count([
            'from' => $table,
            'where' => $where
        ]);
        $this->doLoop($total, function ($perPage) use ($table, $where) {
            return $this->model->from($table)->_where($where)->limit($perPage)->findAll(true);
        }, function ($item) use ($table) {
            echo '正在发布：id=>' . $item['id'] . '---------------' . PHP_EOL;
            if(!$item['content']|| !$item['title']){
                $this->model->from($table)->eq('id', $item['id'])->update(['isfabu' => 1]);
                return;
            }
            $ret = $this->client->http('http://www.qilinyue2016.com/portal/fabu/table?pwd=Djidksl$$EER4ds58cmO', 'post', [
                'url_md5' => $item['url_md5'],
                'content' => $this->filter($item['content']),
                'title' => $this->filter($item['title']),
                'table' => 'boxcom',
            ]);
            if (!$ret) {
                exit('接口连接失败' . PHP_EOL);
            }
            if ($ret === '发布成功') {
                $this->model->from($table)->eq('id', $item['id'])->update(['isfabu' => 1]);
                echo '发布成功';
            } else{
                exit('发布失败：' . $ret);
            }

            //exit();
        });
    }

    public function filter($str){
        return preg_replace([
            '#Box\.com#i',
            '/(https?)：/i',
            '#Hi@[0-9a-z_]+\s*(。|，|！)*#i',
            '#&lt(;|；)iframe.+?/iframe&gt(;|；)#i',
            '/box Support/i',
            '/box sync/i',
            '/box drive/i',
            '/box/i',
            '#<p>\s*</p>#',
        ],[
            'qilinyue2016.com',
            '$1:',
            '',
            '',
            '麒麟平台Support',
            '麒麟系统sync',
            '麒麟平台drive',
            '麒麟',
            '',
        ],$str);
    }
}
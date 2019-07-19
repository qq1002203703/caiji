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
use shell\CaijiCommon;

class Cainiaotaobao extends CaijiCommon
{
    protected $task = [
        0 => [
            'cate_table' => 'caiji_cainiaotaobao',
            'rule_name' => 'cainiaotaobao',
        ]
    ];

    //内页url的获取：对每个分类列表页进行采集，从而获取内页的url和其他相关数据
    public function cate_page()
    {
        $taskId = 0;
        $caijiRule = $this->getCaijiRules($this->task[$taskId]['rule_name'], 'page', '');
        $this->dieEcho($caijiRule === false, '规则名不正确' . PHP_EOL);
        //测试
        //$this->pageTest($caijiRule,'https://yizhanzhuanye.bbs.taobao.com/list.html?spm=a210m.7902376.0.0.359c3598PmsVh4&page=1');
        //return ;
        $caijiRule['url'] = 'https://yizhanzhuanye.bbs.taobao.com/list.html?spm=a210m.7902376.0.0.359c3598PmsVh4&page={%0,0,36,1,0,0%}';
        $callback = Helper::callback($caijiRule['callback'] . '::create', [$caijiRule]);
        if (is_object($callback)) {
            $callback->start();
        } else {
            echo '回调页面[page]采集类失败' . PHP_EOL;
            exit();
        }
    }

    public function content()
    {
        $this->caiji('cainiaotaobao', 'content');
    }

    public function fabu()
    {
        $taskId = 0;
        $where = [['iscaiji', 'eq', 1], ['isfabu', 'eq', 0]];
        $total = $this->model->count([
            'from' => $this->task[$taskId]['cate_table'],
            'where' => $where,
        ]);
        $this->dieEcho(($total < 1), '采集失败' . PHP_EOL);
        $this->doLoop($total, function ($perPage, $i) use ($where, $taskId) {
            return $this->model->from($this->task[$taskId]['cate_table'])->_where($where)->limit($perPage)->findAll(true);
        }, function ($item, $key) use ($taskId) {
            echo '---正在处理分类：' . $item['id'] . '---------------------' . PHP_EOL;
            $ret = $item['title'] . "\n" . trim($item['content']);
            $str = trim($item['content']);
            if ($item['reply']) {
                $replys = explode('{%|||%}', $item['reply']);
                foreach ($replys as $reply) {
                    list($username, $reply_content) = explode('{%||%}', $reply);
                    $ret .= $username . ' : ' . trim($reply_content) . "\n";
                    $str .= trim($reply_content);
                }
            }
            if (mb_strlen($str) < 250) {
                echo '   失败：内容过短不输出' . PHP_EOL;
                $this->model->from($this->task[$taskId]['cate_table'])->eq('id', $item['id'])->update([
                    'isfabu' => 1
                ]);
                return;
            }
            dump($item['title']);
            if (file_put_contents('E:\cainiaotaobao\\' . $item['id'] . '.'  . '.txt', $ret) === false) {
                echo '   失败：无法写入文件' . PHP_EOL;
                exit();
            } else {
                echo '   成功：写入文件' . PHP_EOL;
                $this->model->from($this->task[$taskId]['cate_table'])->eq('id', $item['id'])->update([
                    'isfabu' => 1
                ]);
            }
        });

    }

    public function dodo()
    {
        $taskId = 0;
        $where = [['iscaiji', 'eq', 1], ['isdone', 'eq', 0]];
        $total = $this->model->count([
            'from' => $this->task[$taskId]['cate_table'],
            'where' => $where,
        ]);
        $this->dieEcho(($total < 1), '采集失败' . PHP_EOL);
        $this->doLoop($total, function ($perPage, $i) use ($where, $taskId) {
            return $this->model->from($this->task[$taskId]['cate_table'])->_where($where)->limit($perPage)->findAll(true);
        }, function ($item, $key) use ($taskId) {
            $item['content'] = trim(strip_tags($item['content'],'<p><br>'));
            if($this->model->from($this->task[$taskId]['cate_table'])->eq('id', $item['id'])->update([
                'isdone' => 1,
                'content'=>$item['content']
            ]))
                echo '成功=>update了！'.PHP_EOL;
            else
                echo '失败=>update了！'.PHP_EOL;
            //msleep(5000);
        });
    }


}

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


namespace extend\translate;

use extend\Helper;
use extend\HttpClient;
class Qq
{
    public $url='https://fanyi.qq.com/api/translate';
    public $cookie=[];
    public $client;
    protected $form_data=[];
    public $error='';
    public function __construct()
    {
        $this->client=New HttpClient(['opt'=>[
            CURLOPT_REFERER=>'https://fanyi.qq.com/',
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
            ]]);
        $this->client->curlTimeInterval=1500;
        //$this->client->setCookie()
        /*$this->cookie=[
            '8507d3409e6fad23_gr_session_id_219244e6-5a26-419e-9d66-99ba11a94c81'=>'false',
            '8507d3409e6fad23_gr_session_id'=>'219244e6-5a26-419e-9d66-99ba11a94c81',
        ];*/
    }

    public function translate($text,$to='zh-CHS',$from='auto',$needle='.|,',$isFilter=true){
        if ($isFilter)
            return $this->filter($this->translateLoop($text,$to,$from,$needle));
        return $this->translateLoop($text,$to,$from,$needle);
    }

    protected function filter($str){
        return str_replace([
            '{%|%}',
            '{%|。%}'
        ],'{%|||%}',$str);
    }

    protected function translateLoop($text,$to,$from,$needle='.|,'){
        $text=Split::do($text,$needle,5000);
        if($text===false){
            $this->error='字符串无法分割成数组';
            return false;
        }
        $result='';
        $form_data=[
            'source'=>$from,
            'target'=>$to,
            'qtv'=>$this->form_data['qtv'],
            'qtk'=>$this->form_data['qtk'],
        ];
        foreach ($text as $i=>$item){
            $form_data['sourceText']=$item;
            $form_data['sessionUuid']='translate_uuid'.round(microtime(true)/1000);
            $res=$this->client->http($this->url,'post',$form_data);
            if(!$res){
                $this->error='$i='.$i.',HttpClient返回结果为false';
                return false;
            }
            $res=json_decode($res,true);
            if(isset($res['translate']['records']) && $res['translate']['records'])
                $result.=$this->getResult($res['translate']['records']);
            else{
                $this->error='$i='.$i.',HttpClient返回结果格式不正确';
                return false;
            }
        }
        return $result;
    }

    protected function getResult($data){
        $str='';
        foreach ($data as $item){
            $str.=$item['targetText'];
        }
        return $str;
    }

    /** ------------------------------------------------------------------
     * sign
     * @return bool
     *---------------------------------------------------------------------*/
    public function sign(){
        $url='https://fanyi.qq.com/';
       /* $this->client->setOptions([
            CURLOPT_HEADER=>true,
        ]);*/
        $res=$this->client->http($url,'get');
        if(!$res)
            return false;
        $this->form_data['qtv']=Helper::strCut($res,'var qtv = "','"',false);
        if(!$this->form_data['qtv'])
            return false;
        $this->form_data['qtk']=Helper::strCut($res,'var qtk = "','"',false);
        if(!$this->form_data['qtk'])
            return false;
        //dump($this->form_data);
        return true;
    }
}
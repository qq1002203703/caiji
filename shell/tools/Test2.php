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

namespace shell\tools;

use shell\BaseShell;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
class Test2 extends BaseShell
{
    protected $path=ROOT.'/shell/tools/test2';
    public function start(){
        //echo  md5('https://market.m.taobao.com/apps/market/toutiao/portal.html?spm=a215s.7406091/A.0.0&utparam=%7B%22ranger_buckets_native%22%3A%225555%22%7D&wh_weex=true&feed_ids=209279139599,209243727260,209172416033,209283491190&scm=2019.1.2.189&from=5&columnId=0&_wx_appbar=true');
        echo time();
        exit();
        $html='sss<a href="aa.com" title="aa">lldldl</a>bb<a href="bb.com" title="bb">lldldl</a>aaaa';
        $b=\extend\Selector::regexMulti2($html,'/<a href="(?P<url>[^"]+)" title="(?P<title>[^"]+)">/','url,title','/sss(?P<cut>[\s\S]+?)aaaa$/');
        dump($b);
        dump(\extend\Selector::getError());
    }
    public function outPut($msg, $important){
        echo $msg;
    }
    function base64_image_content($base64_image_content){
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $new_file=$this->path.'/';
            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0755);
            }
            $new_file = $new_file.time().".{$type}";
            dump($result[1]);
            $en=base64_decode(str_replace($result[1], '', $base64_image_content));
            dump($en);
            if (file_put_contents($new_file,$en )){
                return $new_file;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function page(){
        $host = 'http://localhost:4444/wd/hub';
        try{
            $capabilities = DesiredCapabilities::phantomjs();
            $driver = RemoteWebDriver::create($host, $capabilities, 15000);
            $driver->get('https://market.m.taobao.com/apps/market/content/index.html?contentId=208889360499');
            $driver->manage()->addCookie([]);
        }catch (\Exception $e){
            echo  $e->getMessage();
        }
    }

    public function getContent(){
        $host = 'http://localhost:4444/wd/hub';
        try{
            $capabilities = DesiredCapabilities::phantomjs();
            $driver = RemoteWebDriver::create($host, $capabilities, 15000);
            $driver->get('https://market.m.taobao.com/apps/market/content/index.html?contentId=208889360499');
            $driver->wait(50,500)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('_oid_ifr_')));
            $eles=$driver->findElements(WebDriverBy::tagName('img'));
            $img=[];
            foreach ($eles as $k=> $ele){
                if(strpos($ele->getAttribute('src'),'data:image/')!==false)
                    $img[]=$k;
            }
            $this->scroll($driver);
            $eles=$driver->findElements(WebDriverBy::tagName('img'));
            $imgResult=[];
            foreach ($eles as $k=> $ele){
                if(in_array($k,$img)){
                    $src=$ele->getAttribute('src');
                    if(strpos($src,'data:image/')===false){
                        $imgResult[]=$src;
                        echo '<img scr="'.$src.'">'.PHP_EOL;
                    }
                }
            }
            //dump($driver->getPageSource());
            $driver->close();
            //$driver = RemoteWebDriver::create($host, $capabilities, 5000);
        }catch (\Exception $e){
            echo  $e->getMessage();
        }
    }

    /** ------------------------------------------------------------------
     * scroll
     * @param \Facebook\WebDriver\Remote\RemoteWebDriver $driver
     *---------------------------------------------------------------------*/
    public function scroll($driver){
        $height=$driver->executeScript('if(document.compatMode == "BackCompat") return window.document.body.scrollHeight;else return Math.max(window.document.documentElement.scrollHeight,window.document.documentElement.clientHeight);');
        $i=0;
        $y=40;
        while ($i*$y <$height){
            $driver->executeScript('window.scrollTo(0,'.$y*($i+1) .')');
            $i++;
            usleep(50000);
        }
    }

}
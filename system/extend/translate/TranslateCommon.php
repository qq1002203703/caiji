<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 翻译公共类
 * ======================================*/

namespace extend\translate;

abstract class TranslateCommon
{
    /** ------------------------------------------------------------------
     * 翻译
     * @param string $text 原内容
     * @param string $to 目标语种
     * @param string $from 原语种
     * @return string 翻译后的内容
     *---------------------------------------------------------------------*/
    abstract public function translate($text,$to,$from);

}
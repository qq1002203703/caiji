<?php header("Content-type:text/xml");echo '<?xml version="1.0" encoding="utf-8"?>';?>

<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>
    <channel>
        <title><?=$site_name?></title>
        <atom:link href="<?=$site_url?>/feed" type="application/rss+xml" />
        <link><?=$site_url?>/</link>
        <description><?=$site_description?></description>
        <lastBuildDate><?=date(' D, d M Y H:i:s O',$lastBuildDate);?></lastBuildDate>
        <language>zh-CN</language>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <image>
            <url><?=$site_url?>/static/fly/images/logo.png</url>
            <title><?=$site_name?></title>
            <link><?=$site_url?>/</link>
        </image>
        <?php if($data):foreach ($data as $item):$url=url('@'.$item['type'].'@',['id'=>$item['id']]);?>
        <item>
            <title><?=$item['title'];?></title>
            <link><?=$url;?></link>
            <comments><?=$url;?>#flyReply</comments>
            <pubDate><?=date(' D, d M Y H:i:s O',$item['create_time']);?></pubDate>
            <dc:creator><![CDATA[<?=$item['username'];?>]]></dc:creator>
            <category><![CDATA[<?=$item['category_name'];?>]]></category>
            <description><![CDATA[<?=\extend\Helper::text_cut($item['content'],100);?>]]></description>
            <content:encoded><![CDATA[<?=$item['content'];?><p><a href="<?=$site_url?>"><?=$site_name?></a> |<a href="<?=$url;?>">查看原文</a> |<a href="<?=$url;?>#comment-list"><?=$item['comments_num']?>条评论</a></p>]]></content:encoded>
            <wfw:commentRss><?=$site_url.'/'.$item['type'].'/'.$item['id'].'/feed.html';?></wfw:commentRss>
            <slash:comments><?=$item['comments_num'];?></slash:comments>
        </item>
     <?php endforeach; endif;?>
    </channel>
</rss>
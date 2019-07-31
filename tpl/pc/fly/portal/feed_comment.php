<?php header("Content-type:text/xml");echo '<?xml version="1.0" encoding="utf-8"?>';?>

<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>
    <?php $url=url('@'.$data['type'].'@',['id'=>$data['id']]);?>
    <channel>
        <title><![CDATA[<?=$data['title']?>[评论]]]></title>
        <atom:link href="<?=$site_url.'/'.$data['type'].'/'.$data['id'].'/feed.html';?>" type="application/rss+xml" />
        <link><?=$url;?></link>
        <description><?=\extend\Helper::text_cut($data['content'],120);?></description>
        <lastBuildDate><?=date(' D, d M Y H:i:s O',$data['create_time']);?></lastBuildDate>
        <language>zh-CN</language>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <?php if($comments):foreach ($comments as $item):?>
            <item>
                <title><![CDATA[作者：<?=$item['username'];?>]]></title>
                <link><?=$url;?>#comment-<?=$item['id'];?></link>
                <dc:creator><![CDATA[<?=$item['username'];?>]]></dc:creator>
                <pubDate><?=date(' D, d M Y H:i:s O',$item['create_time']);?></pubDate>
                <description><![CDATA[<?=\extend\Helper::text_cut($item['content'],120);?>]]></description>
                <content:encoded><![CDATA[<a href="<?=url('@member@',['uid'=>$item['uid']]);?>"><?=$item['username'];?></a> say: <?=$item['content'];?>]]></content:encoded>
            </item>
        <?php endforeach; endif;?>
    </channel>
</rss>
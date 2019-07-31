{%extend@common/main%}
{%block@title%}
<title><?=$site_title?></title>
<meta name="keywords" content="<?=$site_keywords?>">
<meta name="description" content="<?=$site_description?>">
<link rel="canonical" href="<?=$site_url?>">
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="htt://<?=$mobile_domain?>/feed/portal">
{%end%}

{%block@article%}
<div class="yang-content mb3 mt4 normal color3 pl3 pr3">
    {%include@tmp/ad_article%}
    {%include@tmp/index_content%}
</div>
{%include@tmp/index_list%}
{%end%}
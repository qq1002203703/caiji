{%extend@common/main%}

{%block@title%}
<title><?=$site_title?></title>
<meta name="keywords" content="<?=$site_keywords?>">
<meta name="description" content="<?=$site_description?>">
<link rel="canonical" href="<?=$site_url?>">
{%end%}

{%block@article%}
<div class="yang-content mb3 mt4 normal color3 pl3 pr3">
    {%include@tmp/index_content_video%}
</div>
{%include@tmp/index_list_video%}
{%end%}
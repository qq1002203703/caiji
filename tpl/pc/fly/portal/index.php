{%extend@common/base_portal%}
{%block@title%}
<title><?=$site_title?></title>
<meta name="keywords" content="<?=$site_keywords?>">
<meta name="description" content="<?=$site_description?>">
<meta name="mobile-agent" content="format=html5;url=http://<?=$mobile_domain?>/">
<link rel="alternate" media="only screen and(max-width: 750px)" href="http://<?=$mobile_domain?>/">
{%end%}
{%block@article%}
<div class="layui-container">
    <div class="fly-panel">
        <div class="detail-box">
            <div class="detail-body photos">
               {%include@tmp/index_content%}
            </div>
        </div>
        {%include@tmp/index_list%}
    </div>
</div>
{%end%}
{%block@javascript%}
{%end%}
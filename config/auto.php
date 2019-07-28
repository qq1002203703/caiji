<?php
return [
    //['name'=>'学校发布','class'=>'\shell\caiji\Xuexiao','method'=>'start','class_param'=>['fabu'],'method_param'=>''],
    ['name'=>'bilibili发布','class'=>'\shell\ctrl\Portal','method'=>'start','class_param'=>['crontab','-t','bilibili','-n','b6wang.com'],'method_param'=>''],
    ['name'=>'sitemap生成','class'=>'\shell\tools\Sitemap','method'=>'start','class_param'=>['create','-a'],'method_param'=>''],
];
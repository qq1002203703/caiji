<?php
return [
    //['name'=>'学校发布','class'=>'\shell\caiji\Xuexiao','method'=>'start','class_param'=>['fabu'],'method_param'=>''],
    ['name'=>'Box发布','class'=>'\shell\ctrl\Portal','method'=>'start','class_param'=>['qilin_crontab'],'method_param'=>''],
    ['name'=>'sitemap生成','class'=>'\shell\tools\Sitemap','method'=>'start','class_param'=>['create','-a'],'method_param'=>''],
];
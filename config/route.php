<?php
router()
	->error(405,'show_error')
	->error(406,'show_error')
	->get('article','article/:id:d.html','portal/post/article','.html') //文章
    ->get('article_list','alist/:slug','portal/post/article_list') //文章分类
    ->get('article_all','article/all','portal/post/article_all') //文章所有分类

    ->get('goods','goods/:id:d.html','portal/post/goods','.html') //商品内页
    ->get('goods_list','goods_list/:slug','portal/post/goods_list') //商品分类
    ->get('goods_list','goods/all','portal/post/goods_all') //商品所有分类

    ->get('group','group/:id:d.html','portal/post/group','.html') //小组内页
    ->get('group_list','glist/:slug','portal/post/group_list') //小组分类
    ->get('group_all','group/all','portal/post/group_all') //小组所有分类

    ->get('tag','topic/:slug','portal/post/tag')//标签
    ->get('tag_all','topic-all/','portal/post/tag_all')
    //->get('bbs_post','bbs/post/:id:d.html','bbs/post/show','.html')
    //->get('bbs_show','ask/:id:d.html','bbs/post/show2','.html')
    //->get('bbs_list','bbs/channel/:id:d','bbs/post/channel')
   // ->get('tag_bbs_normal','bbs/topic/:slug','bbs/post/tag_caiji')
    ->get('video','video/:id:d','video/index/details','') //视频
    ->get('video_list','vlist/:slug','video/index/list') //视频分类
    ->get('video_play','video/:vid:d/:id:d-:pid:d.html','video/index/play','.html')

    ->get('member','user/:uid:d','portal/member/center','')//用户中心
    ->get('member_article','user/:uid:d/article','portal/member/article','')
    ->get('member_comment','user/:uid:d/comment','portal/member/comment','')
    //->get('weixinqun','weixinqun/:id:d','weixinqun/index/weixinqun')
    //->get('gzh','gzh/:id:d','weixinqun/index/gongzhonghao')
    //->get('tags','tags/:name','weixinqun/index/tags')
    //->get('fenlei','fenlei/:id:d','weixinqun/index/fenlei')
    //->get('diqu','diqu/:id:d','weixinqun/index/diqu')
	->run();
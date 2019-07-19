{%extend@common/menu_setting%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="edit"><?=$title?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-form layui-tab-item layui-show layui-clear">
                <div>
                    <form method="post" action="<?=url('admin/api_other/tpl_edit')?>" onsubmit="return false;">
                        <input type="hidden" name="type" value="pc">
                        <div class="content-form">
                            <div class="content-left" style="padding-bottom:0px;height:60px;">
                                <div class="fly-msg pos">
                                    <a href="<?=url('admin/tpl/edit')?>?tpl=<?=$data['tpl']?>">当前模板:<?=$data['tpl']?></a>
                                    &nbsp;/&nbsp;<a href="<?=url('admin/tpl/edit')?>?tpl=<?=$data['tpl']?>&dir=<?=$data['dir']?>&m=<?=$data['isMobile']?>"><?=$data['dir_d']?></a>
                                    &nbsp;<?=$data['filename']?>
                                    <div style="float:right;">
                                        <a href='/' class='tpl-button' target='_blank'>预览</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tpl-content-form">
                            <div class="tpl-content-right">
                                <div class="tpl-tree" style="margin-left:5px;">
                                    <ul class="layui-nav layui-nav-tree" style="width: 100%;">
                                        <li class="layui-nav-item layui-nav-itemed">
                                            <a href="javascript:;">模板文件</a>
                                            <dl class="layui-nav-child">
                                                <?php
                                                function getUrlQuery($name,&$data){

                                                    $path=$data['root'].$data['tpl_d'].$data['dir_d'].$name;
                                                    $class=($name==$data['filename'])? ' class="layui-this" ':'';
                                                    if(is_dir($path)){
                                                        $dir=$data['dir'] ? $data['dir'].'/'.$name : $name;
                                                        return '<a'.$class.' href="'.url('admin/tpl/edit').'?tpl='.$data['tpl'].'&dir='.$dir.'&m='.$data['isMobile'].'"><i class="layui-icon" style="font-size: 22px">&#xe60d;</i> '.$name.'</a>';
                                                    } else
                                                        return  '<a'.$class.' href="'.url('admin/tpl/edit').'?tpl='.$data['tpl'].'&dir='.$data['dir'].'&name='.$name.'&m='.$data['isMobile'].'"><i class="layui-icon" style="font-size: 22px">&#xe655;</i> '.$name.'</a>';
                                                }
                                                ?>
                                                <?php if($data['list']): foreach ($data['list'] as $item): if($item==='.' || $item==='..') continue; ?>
                                                <dd>
                                                   <?=getUrlQuery($item,$data)?>
                                                </dd>
                                                <?php endforeach;endif; ?>
                                            </dl>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tpl-content-left" style="border: 1px solid #ccc;">
                                <div style="border-bottom: 1px solid #ccc;">
                                    <textarea id="content" name="content" style="width: 100%;min-height: 700px"><?=$data['content']?></textarea>
                                    <script>
                                    </script>
                                    <input type="hidden" name="save_path" value="<?=$data['path']?>">
                                </div>
                                <div style="padding: 10px 20px;">
                                    <button class="layui-btn" lay-filter="ajax" lay-submit alert="1">确认更新</button>
                                    <a class="layui-btn layui-btn-primary" href="javascript:;" id="go-back-btn">取消</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{%end%}
{%block@javascript%}
<script type="text/javascript">
    layui.use(['layer','fly'],function () {
        var $ = layui.jquery;

    })
</script>
{%end%}

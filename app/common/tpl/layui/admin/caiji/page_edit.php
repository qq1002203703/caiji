{%extend@common/menu_main%}
{%block@main%}
<div class="fly-panel fly-panel-user" pad20>
    <div class="layui-tab layui-tab-brief" lay-filter="user">
        <ul class="layui-tab-title" id="LAY_mine">
            <li class="layui-this" lay-id="list"><?=$title?></li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <form class="layui-form" method="post" action="<?=url('api/caiji_admin/page_edit')?>" id="form-main">
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称 <span class="red">*</span></label>
                        <div class="layui-input-inline">

                            <input class="layui-input" type="text" name="name" value="<?=$data['name']?>" lay-verify="required" required>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">目标网址<span class="red">*</span></label>
                        <div class="layui-input-block">
                            <textarea class="layui-textarea" type="text" name="url" lay-verify="required" required><?=str_replace('{%|||%}',"\n",$data['url']);?></textarea>
                            <div class="layui-input-mid layui-word-aux">
                                支持多个，每行一个，格式：http://www.xxx.com/archiver/?fid-15.html&page={%0,1,40236,1,1,0%}<br>
                                {%%}里面的参数代表的意思：<br>
                                第1项：取值为0，1或2，0表示公差，1表示公比，2表示字母<br>
                                第2项：开始页数<br>
                                第3项：总页数<br>
                                第4项：步进（公差或公比数）<br>
                                第5项：值是0或1，表示是否不倒转，倒转后会从后面的页数开始(不填时，默认为0倒转)<br>
                                第6项：值是0或1，表示是否补零(不填时，默认为0不补)<br>
                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select id="status" name="status">
                                <option value="1"<?=echo_select($data['status'],1)?>>启用</option>
                                <option value="0"<?=echo_select($data['status'],0)?>>禁用</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">额外参数</label>
                        <div class="layui-input-block">
                            <textarea class="layui-textarea" type="text" name="options"><?=$data['options']?></textarea>
                            <div class="layui-input-mid layui-word-aux">格式为json字符串，如：{"page":[{"isLoop":false}]}</div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">种类</label>
                        <div class="layui-input-inline">
                            <select id="type" name="type">
                                <option value="0"<?=echo_select($data['type'],0)?>>无限</option>
                                <option value="1"<?=echo_select($data['type'],1)?>>其他</option>`
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <input type="hidden" name="id" value="<?=$data['id']?>">
                            <input type="button" class="layui-btn" lay-filter="ajax" lay-submit value="马上提交">
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
        var navSidebar="caiji-list";
        //var currentData={id:0,type:""};
    </script>
{%end%}

{%block@javascript%}

{%end%}
{%extend@common/portal%}
{%block@title%}
  <title><?=$title?>_<?=$site_title?></title>
{%end%}

{%block@middle%}
                <!--div class="row">
                    <div class="col-12">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title">Examples</h4>
                            <p class="text-muted m-b-15 font-13">
                                Use <code>&lt;i class="icon-user-female"&gt;&lt;/i&gt;</code>.
                            </p-->

							      

            <div class="card">
                <div class="card-block">

                    <div class="account-box">

                        <div class="card-box p-5">
                            <h2 class="text-center pb-4">
								<?=$title?>
                            </h2>

                            <form class="form-horizontal" action="#">

                                <div class="form-group row m-b-20">
                                    <div class="col-12">
                                        <label for="username">用户名</label>
                                        <input class="form-control" type="text" name="username" datatype="*4-20" placeholder="">
										<span class="validform_checktip"></span>
                                    </div>
                                </div>

                                <div class="form-group row m-b-20">
                                    <div class="col-12">
                                        <label for="emailaddress">Email</label>
                                        <input class="form-control" type="email" name="email" datatype="e" placeholder="">
										<span class="validform_checktip"></span>
                                    </div>
                                </div>

                                <div class="form-group row m-b-20">
                                    <div class="col-12">
                                        <label for="password">密码</label>
                                        <input class="form-control" type="password" datatype="*6-20" name="password" placeholder="输入密码">
										<span class="validform_checktip"></span>
                                    </div>
                                </div>
								<div class="form-group row m-b-20">
                                    <div class="col-12">
                                        <label for="password">重复密码</label>
                                        <input class="form-control" type="password" datatype="*" recheck="password" name="repassword" placeholder="再输入一次密码">
										<span class="validform_checktip"></span>
                                    </div>
                                </div>

                                <div class="form-group row m-b-20">
                                    <div class="col-12">

                                        <div class="checkbox checkbox-custom">
                                            <input id="remember" type="checkbox" checked="">
                                            <label for="remember">
                                                接受用户协议 <a href="#" class="text-custom">《用户须知》</a>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group row text-center m-t-10">
                                    <div class="col-12">
                                        <button class="btn btn-block btn-custom waves-effect waves-light" type="submit">提交注册</button>
                                    </div>
                                </div>

                            </form>

                            <div class="row m-t-50">
                                <div class="col-sm-12 text-center">
                                    <p class="text-muted">已经有账号？ <a href="<?=url('portal/index/login')?>" class="text-dark m-l-5"><b>登陆</b></a></p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
{%end%}

{%block@java%}
<script src="/static/lib/validform/validform_v5.3.2_min.js"  charset="UTF-8"></script>
<script type="text/javascript" charset="UTF-8">
	$(function(){
		$(".form-horizontal").Validform({
			tiptype:function(msg,o,cssctl){
				//msg：提示信息;
				//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
				//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
				if(!o.obj.is("form")){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
					var objtip=o.obj.siblings(".validform_checktip");
					cssctl(objtip,o.type);
					objtip.text(msg);
				}
			}
		});
	});
</script>
{%end%}
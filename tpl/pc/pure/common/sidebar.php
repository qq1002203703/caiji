			<div class="pure-u-1-4" id="sidebar">
				<div class="sidebar-box">
					<div class="sidebar-box-title">行业微信群：</div>
					<ul class="sidebar-box-list">
						<?php $model=app('\app\weixinqun\model\Weixinqun');$qun=$model->getCategory(1000,'weixinqun');foreach ($qun as $item) : ?>
						<li class="sidebar-box-item"><a href="<?=url('@fenlei@',['id'=>$item['id']])?>"><?=$item['name']?></a></li>
						<?php endforeach;?>
					</ul>
				</div>
			</div><!--//sidebar-->
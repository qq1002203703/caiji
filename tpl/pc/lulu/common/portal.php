{%content@common/base%}
<main id="main">
  <div class="container">
    <div class="container-fluid">
      <div id="path">
        <ul>
			<li><a href="#">首页</a></li>
			<li><a href="#">PHP</a></li>
			<li><a href="#">Jquery</a></li>
			<li><a href="#">弹出提示</a></li>
			<li>请细说明页</li>
        </ul>
      </div>
      <div class="pure-g">
        <div class="pure-u-1 pure-u-md-3-4">
          <article id="article">
			{%block@article%}
          </article>
        </div>
        <div class="pure-u-1 pure-u-md-1-4">
          <aside id="sidebar">
			<?php $this->insert('common/sidebar');?>
          </aside>
        </div>
      </div>
    </div><!--//container-fluid-->
  </div><!--//container-->
</main>
<?php $this->layout('layout', [
    'site_name' => $site_name,
    'is_login'=>$is_login
]) ?>

<?php $this->start('header') ?>
  <title><?=$this->e($site_title) ?></title>
  <meta name="keywords" content="<?=$this->e($site_keywords) ?>">
  <meta name="description" content="<?=$this->e($site_description) ?>">
<?php $this->stop() ?>

<?php $this->start('middle')?>

	<div class="post">
  <h2><?=$this->e($site_name) ?></h2>
  <p>E上使用滤镜的方案对效率有多大影响？这东西有统计数据嘛？如果只是消耗CPU。</p>
<p>E上使用滤镜的方案对效率有8888多大影响？这东西有统计数据嘛？如果只是消耗CPU。</p>
<p>E上使用滤镜的方案对效率有多大影响？这东西有统计数据嘛？如果只是消耗CPU。</p>
<p>E上使用滤镜的方案对效率有多大影响？这东西有统计数据嘛？如果只是消耗CPU。</p>
<p>E上使用滤镜的方案对效率有多大影响？这东西有统计数据嘛？如果只是消耗CPU。</p>
 </div><!--end post-->
 <?php $this->insert('huandeng'); ?>
<?php $this->stop() ?>

<?php $this->start('java') ?>
<script type="text/javascript">
</script>
<?php $this->stop() ?>

<?php $this->layout('layout', [
    'site_name' => $site_name,
    'is_login'=>$is_login
]) ?>
<?php $this->start('header') ?>
<title><?=$this->e($title) ?></title>
<?php $this->stop() ?>

<?php $this->start('middle')?>

    <div class="post">
        <h2><?=$this->e($title)?></h2>
        <div class="post-left">
            <div class="post-left-con">
                <p><strong>用户名：<?=$username?></strong></p>
                <p>角色1：<?=$user_account['GameID1']?></p>
                <p>角色2：<?=$user_account['GameID2']?></p>
                <p>角色2：<?=$user_account['GameID3']?></p>
                <p>角色4：<?=$user_account['GameID4']?></p>
                <p>角色5：<?=$user_account['GameID5']?></p>
            </div>
        </div>
        <div class="post-right">
            <div class="post-left-con">
                <p>&nbsp;</p>
                <p>呢称：<?=$user_memb['memb_name']?></p>
                <p>身份证：<?=$user_memb['sno__numb']?></p>
                <p>邮箱：<?=$user_memb['mail_addr']?></p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
            </div>
        </div>


    </div><!--end post-->
<?php $this->insert('huandeng'); ?>
<?php $this->stop() ?>

<?php $this->start('java') ?>
    <script type="text/javascript">
    </script>
<?php $this->stop() ?>
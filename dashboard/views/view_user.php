<?php
require '../includes/interface.php';
?>
<?php $title = "View user" ?>
<?php ob_start(); ?>
<div class="col-md-12">
    <div class="panel panel-info">
        <div class="panel-heading">
            <span>List of users</span>
        </div>
        <div class="panel-body">
            <?php echo $user->status; ?>
            <?php $user->userList(NULL); ?>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include '../layout/layout_main.php'; ?>
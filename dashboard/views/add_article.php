<?php
require '../includes/interface.php';
?>
<?php $title = $main->header($_REQUEST['article']); ?>
<?php ob_start(); ?>
<div class="col-md-8">
    <?php
    //form building
    $main->formBuilder($_REQUEST['article'],"add");
    echo $main->status;
    ?>
    <span id="notification"></span>
</div>    
<?php $content = ob_get_clean(); ?>
<?php include '../layout/layout_main.php'; ?>
<?php
require '../includes/interface.php';
 ?>
<?php $title='View subject' ?>
<?php ob_start(); ?>
<div class="col-md-12">
    <?php
    //form building    
    $subject->getList();
    echo $main->status;
    ?>
</div>  
<?php $content=ob_get_clean(); ?>
<?php include '../layout/layout_main.php'; ?>
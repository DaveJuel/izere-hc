<?php
require '../includes/interface.php';
 ?>
<?php $title=$main->header($_REQUEST['article']);  ?>
<?php ob_start(); ?>
<div class="col-md-12">
    <?php
    //form building    
    $content->getList($_REQUEST['article']);
    echo $main->status;
    ?>
</div>  
<?php $content=ob_get_clean(); ?>
<?php include '../layout/layout_main.php'; ?>
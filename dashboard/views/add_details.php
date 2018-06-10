<?php
require '../includes/interface.php';
?>
<?php $title = $subjectTitle=str_replace("_"," ",$main->header($_REQUEST['article']));?>
<?php ob_start();?>
<?php
$formType = $subject->getSubjectType($_REQUEST['article']);
if ($formType == "container") {?>
<div class="container-fluid">
    <div class="row">
        <ul id="tabs" class="nav nav-tabs nav-justified" data-tabs="tabs">
            <li class="active">
            <a href="#<?php echo $main->header($_REQUEST['article']) ?>" data-toggle="tab">add <?php echo $title ?></a>
            </li>
        <?php $main->tabBuilder($main->header($_REQUEST['article']));?>
        </ul>
        <div class="tab-content">
        <div class="tab-pane active" id="<?php echo $main->header($_REQUEST['article']); ?>">
        <?php
         $main->formBuilder($_REQUEST['article'], "add");
            echo $main->status;
            ?>
        </div>
        <?php
        $tabList = $subject->getChildSubjectList($main->header($_REQUEST['article']));
        for ($counter = 0; null !== $tabList&&$counter<count($tabList); $counter++) {
            $btClass="";
            if($counter==0)$btClass="active";
        ?>
        <div class="tab-pane" id="<?php echo $tabList[$counter]['title']; ?>">
        <?php
        //form building
            $main->formBuilder($tabList[$counter]['id'], "add");
            echo $main->status;
            ?>
        </div>
        <?php }?>
        </div>
    </div>
</div>
<?php
} else {?>
<div class="col-md-8">
    <?php
//form building
    $main->formBuilder($_REQUEST['article'], "add");
    echo $main->status;
    ?>    
</div>
<?php }?>
<?php $content = ob_get_clean();?>
<?php include '../layout/layout_main.php';?>
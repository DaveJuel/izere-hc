<?php require '../includes/interface.php'; ?>
<?php $title = 'Notifications' ?>
 <?php ob_start() ?>
 <div class="row m-t-md"> 
     <div class="col-md-2">
        <ul class="list-unstyled mailbox-nav">
            <li class="active">
                <a href="messages.php">
                    <i class="fa fa-inbox"></i>Notifications 
                    <?php if ($notification->count > 0) {?> 
                        <span class="badge badge-success pull-right">
                            <?php echo $notification->count; ?>
                        </span>
                    <?php } ?>
                </a>
            </li>
        </ul>
    </div>
    <?php $notification->receive(); ?>
</div><!-- Row -->

 <?php $content = ob_get_clean() ?>
 <?php include '../layout/layout_main.php' ?>
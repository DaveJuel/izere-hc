<?php require '../includes/interface.php'; ?>
<?php $title = 'Messages' ?>
<?php ob_start() ?>
<div class="row m-t-md">
<!--    <div class="col-md-12">
        <div class="row mailbox-header">
                        <div class="col-md-2">
                            <a href="compose.html" class="btn btn-success btn-block">Compose</a>
                        </div>
            <div class="col-md-6">
                <h2>Received</h2>
            </div>
                        <div class="col-md-4">
                            <form action="#" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control input-search" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-success" type="button"><i class="fa fa-search"></i></button>
                                    </span>
                                </div> Input Group 
                            </form>
                        </div>
        </div>
    </div>-->
    <div class="col-md-2">
        <ul class="list-unstyled mailbox-nav">
            <li class="active">
                <a href="messages.php">
                    <i class="fa fa-inbox"></i>Received 
                    <?php if ($message->count > 0) {
                        ?> 
                        <span class="badge badge-success pull-right">
                            <?php echo $message->count; ?>
                        </span>
                    <?php } ?>
                </a>
            </li>
            <li class="active">
                <a href="sms.php">
                    <i class="fa fa-inbox"></i>SMS 
                    <?php if ($message->count > 0) {
                        ?> 
                        <span class="badge badge-success pull-right">
                            <?php echo $message->count; ?>
                        </span>
                    <?php } ?>
                </a>
            </li>
<!--            <li><a href="#"><i class="fa fa-sign-out"></i>Sent</a></li>
            <li><a href="#"><i class="fa fa-file-text-o"></i>Draft</a></li>
            <li><a href="#"><i class="fa fa-exclamation-circle"></i>Spam</a></li>
            <li><a href="#"><i class="fa fa-trash"></i>Trash</a></li>-->
        </ul>
    </div>
    <?php $message->receive(); ?>
</div><!-- Row -->

<?php $content = ob_get_clean() ?>
<?php include '../layout/layout_main.php' ?>
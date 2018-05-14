<?php require '../includes/interface.php'; ?>
<?php $title = 'Read' ?>
<?php
ob_start();
$content = $_REQUEST['content'];
if ($content == "message") {
    $message->read($_REQUEST['ref']);
    $title = $message->sender;
    $date = $message->createdOn;
    $body = $message->message;
    $link = "messages.php";
    $linkHeader = "Messages";
    $counted = $message->count;
} else if ($content == "notification") {
    $notification->read($_REQUEST['ref']);
    $title = $notification->title;
    $date = $notification->createdOn;
    $body = $notification->content;
    $link = "notifications.php";
    $linkHeader = "Notifications";
    $counted = $notification->count;
}
?>
<div class="row m-t-md"> 
    <div class="col-md-2">
        <ul class="list-unstyled mailbox-nav">
            <li class="active">
                <a href="<?php echo $link ?>">
                    <i class="fa fa-inbox"></i><?php echo $linkHeader ?>
                    <?php if ($counted > 0) {
                        ?> 
                        <span class="badge badge-success pull-right">
                            <?php echo $counted; ?>
                        </span>
                    <?php } ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="col-md-10">
        <div class="mailbox-content">
            <div class="message-header">
                <h3><?php echo $title; ?></h3>
                <p class="message-date"><?php echo $date; ?></p>
            </div>
            <div class="message-content">
                <div class="slimscroll chat" style="overflow: hidden; width: auto; height: 100%;">
                    <div class="chat-item chat-item-left">                       
                        <div class="chat-message">
                            <?php echo $body; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($content == "message") { ?>
                <div class="message-options pull-right">
                    <a href="compose.html" class="btn btn-default"><i class="fa fa-reply m-r-xs"></i>Reply</a> 
                    <a href="#" class="btn btn-default"><i class="fa fa-arrow-right m-r-xs"></i>Forward</a> 
                    <a href="#" class="btn btn-default"><i class="fa fa-print m-r-xs"></i>Print</a> 
                    <a href="#" class="btn btn-default"><i class="fa fa-exclamation-circle m-r-xs"></i>Spam</a> 
                    <a href="#" class="btn btn-default"><i class="fa fa-trash m-r-xs"></i>Delete</a> 
                </div>
            <?php } ?>
        </div>
    </div>

</div><!-- Row -->
<?php $content = ob_get_clean() ?>
<?php include '../layout/layout_main.php' ?>
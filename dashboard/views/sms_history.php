<?php require '../includes/interface.php'; ?>
<?php $title = "Send SMS"?>
<?php ob_start(); ?>
<!-- End: Topbar -->
<section class="row">
	<!-- create new order panel -->
	 <div class="col-md-2">
        <ul class="list-unstyled mailbox-nav">
            <li class="active">
                <a href="sms.php">
                   Send SMS
                </a>
            </li>
            <li class="active">
                <a href="sms_history.php">
                    View history
                </a>
            </li>
        </ul>
    </div>
	<div class="col-md-10">
		<!-- Form Wizard -->
		<div class="admin-form theme-primary">
			<div class="panel mb25 mt5">				
				<div class="panel-body p20 pb10">
                            <?php $smsKey->history($_SESSION['user_id'], "site"); ?>
                </div>
			</div>
		</div>
	</div>
</section>
<?php $content = ob_get_clean(); ?>
<?php include '../layout/layout_main.php'; ?>


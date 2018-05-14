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
								<form id="admin-form" method="post"
									action="<?php echo $_SERVER['PHP_SELF']; ?>">
									<div class="panel-body">
										<div class="section-divider mb40 mt20">
											<span></span>
										</div>
										<!-- .section-divider -->
										<div class="col-md-6">
											<div class="form-group">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-user"></i></span>
													<input type="text" name="send_sms_recipient"
														id="send_sms_recipient" class="form-control"
														placeholder="Receiver" required>
												</div>
												<!-- end section -->
											</div>
											<!-- end .section row section -->
											<div class="form-group">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
													<input type="text" name="send_sms_subject" id="subject"
														onblur="uploadList();" class="form-control"
														placeholder="Subject" required>
												</div>
											</div>
											
											<div class="form-group">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-pencil"></i></span>
													<textarea class="form-control" id="send_sms_message" name="send_sms_message"
														placeholder="Type your message"></textarea>
												</div>
											</div>
											<!-- end section -->
										</div>
										<div class="col-md-6">
											<div class="col-md-12 span3 tiny ">												
												<div class="pricing-table-features">
													<p>
														<strong>Sent: </strong> <?php echo $smsKey->counter("sent", $_SESSION['user_id']); ?></p>
													<p>
														<strong>Success: </strong> <?php echo $smsKey->counter("success", $_SESSION['user_id']); ?></p>
													<p>
														<strong>Failed: </strong><?php echo $smsKey->counter("failed", $_SESSION['user_id']); ?></p>
												</div>
											</div>
										</div>
									</div>
									<!-- end .form-body section -->
									<div class="panel-footer text-left">
										<button class="btn btn-info" id="btn_send_sms" name="action"
											value="Send">
											Send <span class="fa fa-send"></span>
										</button>
										<span id="send_sms_file_status"><?php $smsKey->status ?></span>
										<span id="send_sms_file_progress"></span>
									</div>
									<!-- end .form-footer section -->
								</form>
				</div>
			</div>
		</div>
	</div>
</section>
<?php $content = ob_get_clean(); ?>
<?php include '../layout/layout_main.php'; ?>


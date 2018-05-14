<?php
require '../includes/interface.php';
$user->getUserDetails($_SESSION['user_id']);
 ?>
<?php $title="Profile" ?>
<?php ob_start(); ?>
<div class="container-fluid">
        <div class="row">
            <ul id="tabs" class="nav nav-tabs nav-justified" data-tabs="tabs">
                <li class="active">
                    <a href="#profile_settings" data-toggle="tab"> Profile</a>
                </li>
                <li>
                    <a href="#security_settings" data-toggle="tab"> Advanced</a>
                </li>
            </ul>
            <!-- /.col-lg-12 -->
            <div class="tab-content">
                <div class="tab-pane active" id="profile_settings">
                        <form accept-charset="utf-8" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">First Name</span>
                                    <input type="text" name="add_user_fname" id="reg-fname"  required class="form-control" value="<?php echo $user->fname ?>" placeholder="First name">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Last Name</span>
                                    <input type="text" name="add_user_lname" id="reg-lname" required class="form-control" value="<?php echo $user->lname ?>" placeholder="Family name">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Email</span>
                                    <input type="email" name="add_user_email" id="reg-email" required class="form-control" value="<?php echo $user->email ?>" placeholder="Enter your email">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Tel</span>
                                    <input type="text" name="add_user_phone" id="reg-phone" class="form-control" value="<?php echo $user->phone; ?>" placeholder="+25078888888">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">Address</span>
                                    <input type="text" name="add_user_address " id="reg-address" class="form-control" value="<?php echo $user->address; ?>" placeholder="">
                                </div>
                            </div>                            
                            <p>
                                <input type="submit" id="btn-reg-save" name="action" class="btn btn-info" value="Edit"/>
                                <input type="hidden" name="caller" value="site"> 
                            </p>
                            <span id="reg_user_status"><?php echo $user->status; ?></span>
                        </form>
                </div>
                <div class="tab-pane" id="security_settings">                  
                    <form class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">                                    
                        <div class="form-group">
                            <label for="inputUsername" class="col-sm-2 control-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" disabled name="username" required id="inputUsername" value="<?php echo $_SESSION['username'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-sm-2 control-label">New password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="new_password" required id="inputPassword" placeholder="password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputCPassword" class="col-sm-2 control-label">Confirm password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="confirm_password" required id="inputCPassword" placeholder="password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" class="btn btn-success" name="action" value="Change password">
                                <p class="text-box"><?php echo $user->status; ?></p>
                            </div>                            
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
<?php $content=ob_get_clean();?>
<?php include '../layout/layout_main.php'; ?>
<?php include '../includes/interface.php'; ?>
<?php $title = 'Register' ?>
<?php ob_start() ?> 
<div class="row">
    <div class="col-md-3 center">
        <div class="login-box">
            <a href="login.php" class="logo-name text-lg text-center"><?php echo $main->appName; ?></a> 
            <p class="text-center m-t-md">Create account</p>
            <form class="m-t-md" method="post" id="register-form">   
            <div class="form-group">
                <input type="text" name="register_fname" class="form-control" placeholder="First name" required>
            </div>
            <div class="form-group">
                <input type="text" name="register_lname" class="form-control" placeholder="Last name" required>
            </div>    
            <div class="form-group">
                <input type="text" name="register_email" class="form-control" placeholder="email" required>
            </div>
            <div class="form-group">
                <input type="password" name="register_password" class="form-control" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
            </div>   
                <label>
                    <input type="checkbox"> Agree the terms and policy
                </label>
                <input type="submit" class="btn btn-success btn-block m-t-xs" value="Sign up">
                <p class="text-center m-t-xs text-sm" id="notification"></p>    
                <p class="text-center m-t-xs text-sm">Already have an account?</p>
                <a href="login.php" class="btn btn-default btn-block m-t-xs">Login</a>
            </form>
        </div>
    </div>
</div><!-- Row -->
<?php $content = ob_get_clean() ?>
<?php include '../layout/layout_login.php' ?>
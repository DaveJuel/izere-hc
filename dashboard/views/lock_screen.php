<?php require '../includes/interface.php'; ?>
<?php $title = 'Locked' ?>
<?php ob_start() ?>
<div class="user-box m-t-lg row">
<!--    <div class="col-md-12 m-b-md">
        <img src="../images/noimage-team.png" class="img-circle m-t-xxs" alt="">
    </div>-->
    <div class="col-md-12">
        <p class="lead no-m text-center">Welcome Back, <?php echo $_SESSION['username']; ?></p>
        <p class="text-sm text-center">Enter password to unlock</p>
        <form class="form-inline text-center" method="post" id="unlock-form">
            <div class="input-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <div class="input-group-btn">
                    <input type="submit" class="btn btn-success" name="action" value="Unlock">                    
                </div><!-- /btn-group -->
            </div><!-- /input-group -->
            <p class="text-center" id="notification"></p>
        </form>
    </div>
</div>
<?php $content = ob_get_clean() ?>
<?php include '../layout/layout_login.php' ?>
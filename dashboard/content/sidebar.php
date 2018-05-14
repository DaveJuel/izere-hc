<div class="page-sidebar-inner slimscroll">
    <div class="sidebar-header">
        <div class="sidebar-profile">
            <a href="javascript:void(0);" id="profile-menu-link">
                <div class="sidebar-profile-image">
                    <img src="../images/noimage-team.png" class="img-circle img-responsive" alt="">
                </div>
                <div class="sidebar-profile-details">
                    <span><?php echo $_SESSION['username']; ?><br><small><?php echo $user->getUserType($_SESSION['user_id']); ?></small></span>
                </div>
            </a>
        </div>
    </div>
    <ul class="menu accordion-menu">
        <li class="droplink"><a href="#" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-plus"></span><p>Add new</p><span class="arrow"></span></a>
            <ul class="sub-menu">
                <?php if ($user->getUserType($_SESSION['user_id'])== "administrator")  { ?>
                    <li>
                        <a href="add_user.php">User</a>
                    </li>
                <?php } ?>
                <?php $main->makeLinks("add"); ?>
            </ul>
        </li>
        <li class="droplink"><a href="#" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-eye-open"></span><p>View</p><span class="arrow"></span></a>
            <ul class="sub-menu">
                <li>
                    <a href="view_user.php">User</a>
                </li>
                <?php $main->makeLinks("view"); ?>
            </ul>
        </li>
        <?php if ($user->getUserType($_SESSION['user_id']) == 'administrator') { ?>
            <li class="droplink"><a href="#" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-pushpin"></span><p>Subject</p><span class="arrow"></span></a>
                <ul class="sub-menu">
                    <li>
                        <a href="add_content.php">Add new</a>
                    </li>
                    <li>
                        <a href="view_content.php">View existing</a>
                    </li>
                </ul>
            </li>
        <?php } ?>	
        <li><a href="profile.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-user"></span><p>Profile</p></a></li>
    </ul>
</div>
<div class="navbar">
    <div class="navbar-inner">
        <div class="sidebar-pusher">
            <a href="javascript:void(0);" class="waves-effect waves-button waves-classic push-sidebar">
                <i class="fa fa-bars"></i>
            </a>
        </div>
        <div class="logo-box">
            <a href="home.php" class="logo-text"><span><?php echo $main->appName; ?></span></a>
        </div><!-- Logo Box -->
        <div class="search-button">
            <a href="javascript:void(0);" class="waves-effect waves-button waves-classic show-search"><i class="fa fa-search"></i></a>
        </div>
        <div class="topmenu-outer">
            <div class="top-menu">
                <ul class="nav navbar-nav navbar-left">
                    <li>		
                        <a href="javascript:void(0);" class="waves-effect waves-button waves-classic sidebar-toggle"><i class="fa fa-bars"></i></a>
                    </li>
                    <li>
                        <a href="#cd-nav" class="waves-effect waves-button waves-classic cd-nav-trigger"><i class="fa fa-diamond"></i></a>
                    </li>
                    <li>		
                        <a href="javascript:void(0);" class="waves-effect waves-button waves-classic toggle-fullscreen"><i class="fa fa-expand"></i></a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic" data-toggle="dropdown">
                            <i class="fa fa-cogs"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-md dropdown-list theme-settings" role="menu">
                            <li class="li-group">
                                <ul class="list-unstyled">
                                    <li class="no-link" role="presentation">
                                        Fixed Header 
                                        <div class="ios-switch pull-right switch-md">
                                            <input type="checkbox" class="js-switch pull-right fixed-header-check" checked>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="li-group">
                                <ul class="list-unstyled">
                                    <li class="no-link" role="presentation">
                                        Fixed Sidebar 
                                        <div class="ios-switch pull-right switch-md">
                                            <input type="checkbox" class="js-switch pull-right fixed-sidebar-check">
                                        </div>
                                    </li>
                                    <li class="no-link" role="presentation">
                                        Horizontal bar 
                                        <div class="ios-switch pull-right switch-md">
                                            <input type="checkbox" class="js-switch pull-right horizontal-bar-check">
                                        </div>
                                    </li>
                                    <li class="no-link" role="presentation">
                                        Toggle Sidebar 
                                        <div class="ios-switch pull-right switch-md">
                                            <input type="checkbox" class="js-switch pull-right toggle-sidebar-check">
                                        </div>
                                    </li>
                                    <li class="no-link" role="presentation">
                                        Compact Menu 
                                        <div class="ios-switch pull-right switch-md">
                                            <input type="checkbox" class="js-switch pull-right compact-menu-check">
                                        </div>
                                    </li>
                                    <li class="no-link" role="presentation">
                                        Hover Menu 
                                        <div class="ios-switch pull-right switch-md">
                                            <input type="checkbox" class="js-switch pull-right hover-menu-check">
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="li-group">
                                <ul class="list-unstyled">
                                    <li class="no-link" role="presentation">
                                        Boxed Layout 
                                        <div class="ios-switch pull-right switch-md">
                                            <input type="checkbox" class="js-switch pull-right boxed-layout-check">
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="li-group">
                                <ul class="list-unstyled">
                                    <li class="no-link" role="presentation">
                                        Choose Theme Color
                                        <div class="color-switcher">
                                            <a class="colorbox color-blue" href="?theme=blue" title="Blue Theme" data-css="blue"></a>
                                            <a class="colorbox color-green" href="?theme=green" title="Green Theme" data-css="green"></a>
                                            <a class="colorbox color-red" href="?theme=red" title="Red Theme" data-css="red"></a>
                                            <a class="colorbox color-white" href="?theme=white" title="White Theme" data-css="white"></a>
                                            <a class="colorbox color-purple" href="?theme=purple" title="purple Theme" data-css="purple"></a>
                                            <a class="colorbox color-dark" href="?theme=dark" title="Dark Theme" data-css="dark"></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="no-link"><button class="btn btn-default reset-options">Reset Options</button></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic" data-toggle="dropdown"><i class="fa fa-envelope"></i>
                            <?php if ($message->count > 0) {
                                ?>
                                <span class="badge badge-success pull-right"><?php echo $message->count; ?></span><?php }
                            ?>
                        </a>
                        <ul class="dropdown-menu title-caret dropdown-lg" role="menu">
                            <li><p class="drop-title"><?php echo $message->head; ?></p></li>
                            <li class="dropdown-menu-list slimscroll messages">
                                <ul class="list-unstyled">
                                    <?php $message->alert(); ?>
                                </ul>
                            </li>
                            <?php if ($message->count > 0) { ?>
                                <li class="drop-all"><a href="messages.php" class="text-center">All Messages</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic" data-toggle="dropdown"><i class="fa fa-bell"></i>
                            <?php if ($notification->count > 0) {
                                ?>
                                <span class="badge badge-success pull-right"><?php echo $notification->count; ?></span><?php }
                            ?>
                        </a>

                        <ul class="dropdown-menu title-caret dropdown-lg" role="menu">
                            <li><p class="drop-title"><?php echo $notification->head; ?></p></li>
                            <li class="dropdown-menu-list slimscroll tasks">
                                <ul class="list-unstyled">
                                    <?php $notification->alert(); ?>
                                </ul>
                            </li>
                            <?php if ($notification->count > 0) { ?>
                                <li class="drop-all"><a href="notifications.php" class="text-center">See all</a></li>
                            <?php } ?>                           
                        </ul>                        
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic" data-toggle="dropdown">
                            <span class="user-name"><?php echo $_SESSION['username']; ?><i class="fa fa-angle-down"></i></span>
                            <img class="img-circle avatar" src="../images/noimage-team.png" width="40" height="40" alt="">
                        </a>
                        <ul class="dropdown-menu dropdown-list" role="menu">
                            <li role="presentation"><a href="profile.php"><i class="fa fa-user"></i>Profile</a></li>                           
                            <li role="presentation" class="divider"></li>
                            <li role="presentation"><a href="lock_screen.php"><i class="fa fa-lock"></i>Lock screen</a></li>
                            <li role="presentation"><a href="logout.php"><i class="fa fa-sign-out m-r-xs"></i>Log out</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="logout.php" class="log-out waves-effect waves-button waves-classic">
                            <span><i class="fa fa-sign-out m-r-xs"></i>Log out</span>
                        </a>
                    </li>                   
                </ul><!-- Nav -->
            </div><!-- Top Menu -->
        </div>
    </div>
</div>
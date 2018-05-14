<div class="menu-wrap">
    <nav class="profile-menu">
        <div class="profile"><img src="../images/noimage-team.png" width="60" alt="David Green"/><span><?php echo $_SESSION['username']; ?></span></div>
        <div class="profile-menu-list">
            <a href="home.php"><i class="fa fa-home"></i><span>Home</span></a>
            <a href="notifications.php"><i class="fa fa-bell"></i><span>Notifications</span></a>
            <a href="messages.php"><i class="fa fa-envelope"></i><span>Messages</span></a>
            <a href="sms.php"><i class="fa fa-envelope"></i><span>SMS</span></a> 
        </div>
    </nav>
    <button class="close-button" id="close-button">Close Menu</button>
</div>
<form class="search-form" action="#" method="GET">
    <div class="input-group">
        <input type="text" name="search" class="form-control search-input" placeholder="Search...">
        <span class="input-group-btn">
            <button class="btn btn-default close-search waves-effect waves-button waves-classic" type="button"><i class="fa fa-times"></i></button>
        </span>
    </div><!-- Input Group -->
</form>
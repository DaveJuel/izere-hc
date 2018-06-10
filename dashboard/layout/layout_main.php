<?php
//login checking
$login_status = $user->checkLogin();
if ($login_status != true) {
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html>
    <head>

        <!-- Title -->
        <title><?php echo $main->appName . " | " . $title ?></title>

        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <meta name="description" content="PHP customizable dashboard" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="David NIWEWE" />

        <!-- Styles -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <script src="../plugins/jquery/jquery-2.1.4.min.js"></script>
        <link href="../plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>
        <link href="../plugins/uniform/css/uniform.default.min.css" rel="stylesheet"/>
        <link href="../plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/line-icons/simple-line-icons.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/offcanvasmenueffects/css/menu_cornerbox.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/waves/waves.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/3d-bold-navigation/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/slidepushmenus/css/component.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/metrojs/MetroJs.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css"/>

        <!-- Dashboard Styles -->
        <link href="../css/modern.min.css" rel="stylesheet" type="text/css"/>
        <link href="../css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css"/>
        <link href="../css/custom.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/datatables/css/jquery.datatables_themeroller.css" rel="stylesheet" type="text/css"/>
        <script src="../plugins/3d-bold-navigation/js/modernizr.js"></script>
        <script src="../plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>

        <!--Home Styles-->

        <script>
            $(document).ready(function () {
                $('.details-table-view').DataTable();
            });
        </script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body class="page-header-fixed">
        <?php
include "../content/modular.php";
?>
        <div class="overlay"></div>
        <?php include '../content/chat.php';?>
        <?php include '../content/header.php';?>
        <!-- Search Form -->
        <main class="page-content content-wrap">
            <?php include '../content/navbar.php';?>
            <!-- Navbar -->
            <div class="page-sidebar sidebar">
                <?php include '../content/sidebar.php';?><!-- Page Sidebar Inner -->
            </div><!-- Page Sidebar -->
            <div class="page-inner">
                <div class="page-title">
                    <h3><?php echo $title ?></h3>
                    <div class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li><a href="home.php">Dashboard</a></li>
                            <li class="active"><?php echo $title ?> <span id="notification"></span></li>
                        </ol>
                    </div>
                </div>
                <div id="main-wrapper">
                    <?php echo $content ?>
                </div>
                <!-- Main Wrapper -->
                <?php include '../content/footer.php';?>
            </div><!-- Page Inner -->
        </main><!-- Page Content -->
        <?php include '../content/navbar_ext.php';?>
        <div class="cd-overlay"></div>
        <!-- Javascripts -->
        <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="../plugins/pace-master/pace.min.js"></script>
        <script src="../plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../plugins/switchery/switchery.min.js"></script>
        <script src="../plugins/uniform/jquery.uniform.min.js"></script>
        <script src="../plugins/offcanvasmenueffects/js/classie.js"></script>
        <script src="../plugins/offcanvasmenueffects/js/main.js"></script>
        <script src="../plugins/waves/waves.min.js"></script>
        <script src="../plugins/3d-bold-navigation/js/main.js"></script>
        <script src="../plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="../plugins/jquery-counterup/jquery.counterup.min.js"></script>
        <script src="../plugins/toastr/toastr.min.js"></script>
        <script src="../plugins/flot/jquery.flot.min.js"></script>
        <script src="../plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../plugins/curvedlines/curvedLines.js"></script>
        <script src="../plugins/metrojs/MetroJs.min.js"></script>
        <script src="../js/modern.min.js"></script>
        <script src="../js/pages/dashboard.js"></script>
        <script src="../js/main.js"></script>
        <script src="../js/pages/table-data.js"></script>
        <script src="../plugins/datatables/js/jquery.datatables.min.js"></script>        
        <script src="../js/interface.js"></script>       
        <script type="text/javascript" src="../../js/tawkChat.js"></script>
    </body>
</html>
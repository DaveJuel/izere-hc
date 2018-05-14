<?php
//login checking
//$login_status = $user -> checkLogin();
//if ($login_status != TRUE) {
//header("location:login.php");
//}
?>
<!--THIS IS THE MAIN LAYOUT OF THE APPLICATION -->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title ?></title>
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <meta name="description" content="Teacher's pot" />
        <meta name="keywords" content="teacher,money,saving" />
        <meta name="author" content="echobrain" />
        <!-- Styles -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <link href="../plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>
        <link href="../plugins/uniform/css/uniform.default.min.css" rel="stylesheet"/>
        <link href="../plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/line-icons/simple-line-icons.css" rel="stylesheet" type="text/css"/>	
        <link href="../plugins/offcanvasmenueffects/css/menu_cornerbox.css" rel="stylesheet" type="text/css"/>	
        <link href="../plugins/waves/waves.min.css" rel="stylesheet" type="text/css"/>	
        <link href="../plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/3d-bold-navigation/css/style.css" rel="stylesheet" type="text/css"/>	

        <!-- Theme Styles -->
        <link href="../css/modern.min.css" rel="stylesheet" type="text/css"/>
        <link href="../css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css"/>
        <link href="../css/custom.css" rel="stylesheet" type="text/css"/>

        <script src="../plugins/3d-bold-navigation/js/modernizr.js"></script>
        <script src="../plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="page-login">
        <main class="page-content">
            <div class="page-inner">
                <div id="main-wrapper">
                    <div class="row">
                        <div class="col-md-3 center">
                            <div class="login-box">
                                <a href="login.php" class="logo-name text-lg text-center">ECHOPOT</a>
                                <?php echo $content; ?>
                                <p class="text-center m-t-xs text-sm"><?php echo date("Y") ?> &copy; ECHOPOT by EchoBrain LTD.</p>
                            </div>
                        </div>
                    </div><!-- Row -->
                </div><!-- Main Wrapper -->
            </div><!-- Page Inner -->
        </main><!-- Page Content -->
        <!-- Javascripts -->
        <script src="../plugins/jquery/jquery-2.1.4.min.js"></script>
        <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="../plugins/pace-master/pace.min.js"></script>
        <script src="../plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../plugins/switchery/switchery.min.js"></script>
        <script src="../plugins/uniform/jquery.uniform.min.js"></script>
        <script src="../plugins/offcanvasmenueffects/js/classie.js"></script>
        <script src="../plugins/waves/waves.min.js"></script>
        <script src="../js/modern.min.js"></script>
        
    </body>
</html>
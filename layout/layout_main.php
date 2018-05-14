<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">

        <!-- ========== SEO ========== -->
        <title><?php echo $main->appName . " | " . $title ?></title>
        <meta content="" name="description">
        <meta content="" name="keywords">
        <meta content="" name="author">

        <!-- ========== FAVICON ========== -->
        <link rel="apple-touch-icon-precomposed" href="images/favicon-apple.png" />
        <link rel="icon" href="../images/favicon.png">

        <!-- ========== STYLESHEETS ========== --> 
        <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="../revolution/css/layers.css" rel="stylesheet" type="text/css" />
        <link href="../revolution/css/settings.css" rel="stylesheet" type="text/css" />
        <link href="../revolution/css/navigation.css" rel="stylesheet" type="text/css" />
        <link href="../css/bootstrap-select.min.css" rel="stylesheet" type="text/css">
        <link href="../css/animate.min.css" rel="stylesheet" type="text/css">
        <link href="../css/famfamfam-flags.css" rel="stylesheet" type="text/css">
        <link href="../css/magnific-popup.css" rel="stylesheet" type="text/css">
        <link href="../css/owl.carousel.min.css" rel="stylesheet" type="text/css">
        <link href="../css/style.css" rel="stylesheet" type="text/css">
        <link href="../css/responsive.css" rel="stylesheet" type="text/css">
        
        <style>
            .pac-container {
                z-index: 100000;
            }
        </style>
        <!-- ========== ICON FONTS ========== -->
        <link href="../fonts/font-awesome.min.css" rel="stylesheet">
        <link href="../fonts/flaticon.css" rel="stylesheet">

        <!-- ========== GOOGLE FONTS ========== -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900%7cRaleway:400,500,600,700" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <!-- ========== PRELOADER ========== -->
        <div id="loading">
            <div class="inner">
                <div class="loading_effect">
                    <div class="object" id="object_one"></div>
                    <div class="object" id="object_two"></div>
                    <div class="object" id="object_three"></div>
                </div>
            </div>
        </div>

        <div class="wrapper">
            <?php include '../components/header.php'; ?>
            <?php include '../components/modular.php'; ?>
            <?php echo $content; ?>
            <?php include '../components/footer.php'; ?>
        </div>

        <!-- ========== BACK TO TOP ========== -->
        <div id="back_to_top">
            <i class="fa fa-angle-up" aria-hidden="true"></i>
        </div>

        <!-- ========== NOTIFICATION ========== -->
        <div id="notification"></div>

        <!-- ========== JAVASCRIPT ========== -->
        <script type="text/javascript" src="../js/custom.js"></script>
        <script type="text/javascript" src="../js/jquery.min.js"></script>        
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARYImebnuXTLS7DslhfUVx3yJXtM-rpgk&libraries=places&callback=initAutocomplete" async defer></script>
        <script type="text/javascript" src="../js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="../js/jquery.smoothState.js"></script>
        <script type="text/javascript" src="../js/moment.min.js"></script>
        <script type="text/javascript" src="../js/morphext.min.js"></script>
        <script type="text/javascript" src="../js/wow.min.js"></script>
        <script type="text/javascript" src="../js/jquery.easing.min.js"></script>
        <script type="text/javascript" src="../js/owl.carousel.min.js"></script>
        <script type="text/javascript" src="../js/owl.carousel.thumbs.min.js"></script>
        <script type="text/javascript" src="../js/jquery.magnific-popup.min.js"></script>
        <script type="text/javascript" src="../js/jPushMenu.js"></script>
        <script type="text/javascript" src="../js/isotope.pkgd.min.js"></script>
        <script type="text/javascript" src="../js/countUp.min.js"></script>
        <script type="text/javascript" src="../js/jquery.countdown.min.js"></script>
        <script type="text/javascript" src="../js/main.js"></script>
        <script type="text/javascript" src="../js/tawkChat.js"></script>
        <!-- ========== REVOLUTION SLIDER ========== -->
        <script type="text/javascript" src="../revolution/js/jquery.themepunch.tools.min.js"></script>
        <script type="text/javascript" src="../revolution/js/jquery.themepunch.revolution.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.actions.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.carousel.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.kenburn.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.layeranimation.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.migration.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.navigation.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.parallax.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.slideanims.min.js"></script>
        <script type="text/javascript" src="../revolution/js/extensions/revolution.extension.video.min.js"></script>

    </body>
</html>
<!DOCTYPE html>
<html>
    <head>

        <!-- Title -->
        <title><?php echo $title; ?></title>

        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <meta name="description" content="Modern Landing Page" />
        <meta name="keywords" content="landing" />
        <meta name="author" content="Steelcoders" />

        <!-- Styles -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Raleway:500,400,300' rel='stylesheet' type='text/css'>
        <link href="../plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>
        <link href="../plugins/uniform/css/uniform.default.min.css" rel="stylesheet"/>
        <link href="../plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="../plugins/animate/animate.css" rel="stylesheet" type="text/css">
        <link href="../plugins/tabstylesinspiration/css/tabs.css" rel="stylesheet" type="text/css">
        <link href="../plugins/tabstylesinspiration/css/tabstyles.css" rel="stylesheet" type="text/css">	
        <link href="../plugins/pricing-tables/css/style.css" rel="stylesheet" type="text/css">
        <link href="../css/landing.css" rel="stylesheet" type="text/css"/>

        <script src="../plugins/pricing-tables/js/modernizr.js"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body data-spy="scroll" data-target="#header">
        <?php include '../components/navbar.php'; ?>
        <?php echo $content; ?>
        <?php include '../components/contact.php'; ?> 
        <?php include '../components/footer.php' ?>
        <!-- Javascripts -->
        <script src="../plugins/jquery/jquery-2.1.4.min.js"></script>
        <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="../plugins/pace-master/pace.min.js"></script>
        <script src="../plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../plugins/uniform/jquery.uniform.min.js"></script>
        <script src="../plugins/wow/wow.min.js"></script>
        <script src="../plugins/tabstylesinspiration/js/cbpfwtabs.js"></script>
        <script src="../plugins/pricing-tables/js/main.js"></script>
        <script src="../js/landing.js"></script>

    </body>
</html>

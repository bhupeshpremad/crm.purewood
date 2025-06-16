<?php  
    include_once __DIR__ . '/../../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap 4 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom fonts for this template-->
    <link href="<?php echo BASE_URL; ?>assets/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo BASE_URL; ?>assets/css/sb-admin-2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo BASE_URL; ?>assets/css/sb-admin-2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet" type="text/css">

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

    <!-- Use font-display: swap to avoid slow network font loading warning -->
    <style>
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 400;
            src: url('https://fonts.gstatic.com/s/nunito/v31/XRXV3I6Li01BKofINeaB.woff2') format('woff2');
            font-display: swap;
        }
    </style>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    <!-- Use CDN for FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" crossorigin="anonymous" />

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Commented local JS and CSS vendor links -->
    <!--
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/jquery.easing.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/sb-admin-2.min.js"></script>
    -->

    <!-- Added CDN JS and CSS links -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <!-- Removed multiple Chart.js imports causing errors -->
    <!-- Replaced with single Chart.js CDN link without module import errors -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>


    
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

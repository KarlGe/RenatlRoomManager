<?php 
    include("../snippets/phpInit.php");
?>
<!DOCTYPE html>
<html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Romsjekk</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, height=device-height,  initial-scale=1.0, user-scalable=no;user-scalable=0;"/>
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="../css/offline.css">
        <!--<script src="../js/vendor/modernizr-2.6.2.min.js"></script>-->
    </head>
    <body>
    <?php echo "<script>var lockImgArray = " . json_encode($roomCheckDB->GetLockImgArray()) . ';</script>'; ?>

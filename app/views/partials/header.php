<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<meta name="author" content="Marco Visscher">
		<meta name="description" content="Een simpele App, voor het bij houden van collecties/verzamelingen.">
		<title>Collectie Tracker <?=$version?></title>
		<link rel="icon" type="image/x-icon" href="images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel='stylesheet' type='text/css' href='css/pop-in-templ.css'>
		<script src="js/main.js"></script>

		<?php switch ( $_SERVER["REQUEST_URI"] ) :
			case "/": ?>
				<script src='js/land-pag-ext.js'></script>
				<link rel='stylesheet' type='text/css' href='css/main-landp-templ.css'>
			<?php if ( isset( $device ) && $device === "desktop" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/desk-landp-templ.css'>
			<?php elseif ( isset( $device ) && $device === "mobile" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/mob-landp-templ.css'>
			<?php elseif ( isset( $device ) && $device === "tablet" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/ipad-landp-templ.css'>
			<?php endif; break;
			case "/gebruik": 
				if( !isset( $_SESSION["user"]["id"] ) ) { header( "location:https://{$_SERVER['SERVER_NAME']}/" ); } ?>
				<script src='js/gebr-pag-ext.js'></script>
				<link rel='stylesheet' type='text/css' href='css/main-gebr-templ.css'>
			<?php if ( isset( $device ) && $device === "desktop" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/desk-gebr-templ.css'>
			<?php elseif ( isset( $device ) && $device === "mobile" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/mob-gebr-templ.css'>
			<?php elseif ( isset( $device ) && $device === "tablet" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/ipad-gebr-templ.css'>
			<?php endif; break;
			case "/beheer":
			 	if ( !isset( $_SESSION["user"]["id"] ) && isset( $_SESSION["user"]["admin"] ) ) { header("location:https://{$_SERVER['SERVER_NAME']}/"); } ?>
				<script src='js/beheer-pag-ext.js'></script>
				<link rel='stylesheet' type='text/css' href='css/main-beheer-templ.css'>
			<?php if ( isset( $device ) && $device === "desktop" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/desk-beheer-templ.css'>
			<?php elseif ( isset( $device ) && $device === "mobile" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/mob-beheer-templ.css'>
			<?php elseif ( isset( $device ) && $device === "tablet" ) : ?>
				<link rel='stylesheet' type='text/css' href='css/ipad-beheer-templ.css'>
			<?php endif; break; ?>

			<?php case "/test2":
				if( !isset( $_SESSION["user"]["id"] ) ) { header( "location:https://{$_SERVER['SERVER_NAME']}/" ); } ?>
				<script src="js/html5-qrcode.min.js"></script>
			<?php endswitch; ?>

		<?php if( isset( $_SESSION["header"] ) && isset( $_SESSION["header"]["error"] ) ) :
				foreach( $_SESSION["header"]["error"] as $key => $value ) : ?>
				<script> localStorage.setItem( "<?=$key?>", "<?=$value?>" ); </script>
		<?php endforeach; unset( $_SESSION["header"]["error"] ); elseif ( isset( $_SESSION["header"] ) && isset( $_SESSION["header"]["feedB"] ) ) :
				foreach( $_SESSION["header"]["feedB"] as $key => $value ) : ?>
				<script> localStorage.setItem( "<?=$key?>", "<?=$value?>" ); </script>
		<?php endforeach; unset( $_SESSION["header"]["feedB"] ); elseif ( isset( $_SESSION["header"] ) && isset( $_SESSION["header"]["broSto"] ) ) :
				foreach ( $_SESSION["header"]["broSto"] as $key => $value ) : ?>
				<script> localStorage.setItem("<?=$key?>", "<?=$value?>"); </script>
		<?php endforeach; unset( $_SESSION["header"]["broSto"] ); endif; ?>

	</head>
	<body>
		<noscript> You need to enable JavaScript to run this app. </noscript>
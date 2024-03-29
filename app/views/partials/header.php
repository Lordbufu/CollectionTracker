<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<meta name="author" content="Marco Visscher">
		<meta name="description" content="Een simpele App, voor het bij houden van collecties/verzamelingen.">
		<title>Collectie Tracker <?=$version?></title>
		<link rel="icon" type="image/x-icon" href="images/favicon.ico">
		<!-- Default style and scripts, that should always be loaded first -->
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel='stylesheet' type='text/css' href='css/pop-in-templ.css'>
		<script src="js/main.js"></script>
		<?php
			// assign specific scripts and css based on the uri and device type
			if($_SERVER['REQUEST_URI'] === "/") {
				echo("<script src='js/land-pag-ext.js'></script>");
				echo("<link rel='stylesheet' type='text/css' href='css/main-landp-templ.css'>");

				// Check device and give required css changes
				if(isset($device)) {
					if($device === 'desktop') { echo("<link rel='stylesheet' type='text/css' href='css/desk-landp-templ.css'>"); }
					if($device === 'mobile') { echo("<link rel='stylesheet' type='text/css' href='css/mob-landp-templ.css'>"); }
					if($device === 'tablet') { echo("<link rel='stylesheet' type='text/css' href='css/ipad-landp-templ.css'>");}
				}
			// assign specific scripts and css based on the uri and device type
			} else if($_SERVER['REQUEST_URI'] === "/gebruik") {
				echo("<script src='js/gebr-pag-ext.js'></script>");
				echo("<link rel='stylesheet' type='text/css' href='css/main-gebr-templ.css'>");

				// Check device and give required css changes
				if(isset($device)) {
					if($device === 'desktop') { echo("<link rel='stylesheet' type='text/css' href='css/desk-gebr-templ.css'>"); }
					if($device === 'mobile') { echo("<link rel='stylesheet' type='text/css' href='css/mob-gebr-templ.css'>"); }
					if($device === 'tablet') { echo("<link rel='stylesheet' type='text/css' href='css/ipad-gebr-templ.css'>"); }
				}
			// assign specific scripts and css based on the uri and device type
			} else if($_SERVER['REQUEST_URI'] === "/beheer") {
				echo("<script src='js/beheer-pag-ext.js'></script>");
				echo("<link rel='stylesheet' type='text/css' href='css/main-beheer-templ.css'>");

				// Check device and give required css changes
				if(isset($device)) {
					if($device === 'desktop') { echo("<link rel='stylesheet' type='text/css' href='css/desk-beheer-templ.css'>"); }
					if($device === 'mobile') { echo("<link rel='stylesheet' type='text/css' href='css/mob-beheer-templ.css'>"); }
					if($device === 'tablet') { echo("<link rel='stylesheet' type='text/css' href='css/ipad-beheer-templ.css'>"); }
				}
			}

			// Script injectie voor browser local & sessionStorage, en JS redirects.
			if(isset($header)) {
				foreach($header as $key => $value) {
					echo $value;
				}
			}
		?>
	</head>

	<body>

		<noscript> You need to enable JavaScript to run this app. </noscript>
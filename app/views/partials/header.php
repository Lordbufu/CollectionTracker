<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<meta name="author" content="Marco Visscher">
		<meta name="description" content="Een simpele App, voor het bij houden van collecties/verzamelingen.">
		<title>Collectie Tracker v1.1</title>
		<link rel="icon" type="image/x-icon" href="images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/style.css" media="all">
		<link rel='stylesheet' type='text/css' href='css/pop-in-templ.css'>
		<!-- Extentie style for desktop gebruiker (vanaf 1080p) -->
		<link rel="stylesheet" type="text/css" href="css/desktop-queries.css" media="(min-width: 67.5em)">
		<!-- Extentie style for i-pad gebruiker (vanaf 600x1024) -->
		<link rel="stylesheet" type="text/css" href="css/ipad-queries.css" media="(min-width: 37.5em)">
		<!-- Extentie style for mobile gebruiker (vanaf 320x480) -->
		<link rel="stylesheet" type="text/css" href="css/mobile-queries.css" media="(min-width: 20em)">
		
		<script src="js/html5-qrcode.min.js"></script>
		<script src="js/main.js"></script>

		<?php
			if($_SERVER['REQUEST_URI'] === "/") {													// Scripts & CSS voor de landings pagina
				echo("<script src='js/land-pag-ext.js'></script>");
				echo("<link rel='stylesheet' type='text/css' href='css/landp-templ.css'>");
			} else if($_SERVER['REQUEST_URI'] === "/gebruik") {										// Scripts & CSS voor de gebruik pagina
				echo("<script src='js/gebr-pag-ext.js'></script>");
				echo("<link rel='stylesheet' type='text/css' href='css/gebrp-templ.css'>");
			} else if($_SERVER['REQUEST_URI'] === "/beheer") {										// Scripts & CSS voor de beheer pagina
				echo("<script src='js/beheer-pag-ext.js'></script>");
				echo("<link rel='stylesheet' type='text/css' href='css/beheer-templ.css'>");
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
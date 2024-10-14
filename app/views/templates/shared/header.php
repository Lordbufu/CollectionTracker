		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<meta name="author" content="Marco Visscher">
		<meta name="description" content="Een simpele App, voor het bij houden van collecties/verzamelingen.">
		<title>Collectie Tracker <?=$version?></title>
		<link rel="icon" type="image/x-icon" href="images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel='stylesheet' type='text/css' href='css/modal-style.css'>
		<script src="js/main.js"></script>
		<script src="js/html5-qrcode.min.js"></script>

	<?php switch ( $_SERVER["REQUEST_URI"] ) :

			case "/": ?>
				<script src="js/land-pag-ext.js"></script>
				<link rel="stylesheet" type="text/css" href="css/landing.css">
			<?php if ( isset( $device ) && $device === "mobile" ) : ?>
				<link rel="stylesheet" type="text/css" href="css/landing-mobile.css" >
				<script src="js/mobile-specific.js"></script>
			<?php elseif ( isset( $device ) && $device === "tablet" ) : ?>
				<link rel="stylesheet" type="text/css" href="css/landing-ipad.css" >
			<?php endif; break;

			case "/gebruik":
				if( !isset( $_SESSION["user"]["id"] ) ) { header( "location:https://{$_SERVER['SERVER_NAME']}/" ); } ?>
				<script src="js/gebr-pag-ext.js"></script>
				<link rel="stylesheet" type="text/css" href="css/gebruik.css" >
			<?php if ( isset( $device ) && $device === "mobile" ) : ?>
				<link rel="stylesheet" type="text/css" href="css/gebruik-mobile.css" >
			<?php elseif ( isset( $device ) && $device === "tablet" ) : ?>
				<link rel="stylesheet" type="text/css" href="css/gebruik-ipad.css" >
			<?php else : ?>
				<script src="js/static-elements.js"></script>
			<?php endif; break;

			case "/beheer":
			 	if ( !isset( $_SESSION["user"]["id"] ) && isset( $_SESSION["user"]["admin"] ) ) { header( "location:https://{$_SERVER['SERVER_NAME']}/" ); } ?>
				<script src="js/beheer-pag-ext.js"></script>
				<link rel="stylesheet" type="text/css" href="css/beheer.css" >
			<?php if ( isset( $device ) && $device === "mobile" ) : ?>
				<link rel="stylesheet" type="text/css" href="css/beheer-mobile.css" >
				<script src="js/mobile-specific.js"></script>
			<?php elseif ( isset( $device ) && $device === "tablet" ) : ?>
				<link rel="stylesheet" type="text/css" href="css/beheer-ipad.css" >
			<?php else : ?>
				<script src="js/static-elements.js"></script>
			<?php endif; break; endswitch; ?>

		<?php if( isset( $_SESSION["header"] ) && isset( $_SESSION["header"]["error"] ) ) :
				foreach( $_SESSION["header"]["error"] as $key => $value ) : ?>
				<script> localStorage.setItem( "<?= $key ?>", "<?= $value ?>" ); </script>
		<?php endforeach; unset( $_SESSION["header"]["error"] ); endif;
			if ( isset( $_SESSION["header"] ) && isset( $_SESSION["header"]["feedB"] ) ) :
				foreach( $_SESSION["header"]["feedB"] as $key => $value ) : ?>
				<script> localStorage.setItem( "<?= $key ?>", "<?= $value ?>" ); </script>
		<?php endforeach; unset( $_SESSION["header"]["feedB"] ); endif;
			if ( isset( $_SESSION["header"] ) && isset( $_SESSION["header"]["broSto"] ) ) :
				foreach ( $_SESSION["header"]["broSto"] as $key => $value ) : ?>
					<script> localStorage.setItem("<?= $key ?>", "<?= $value ?>"); </script>
		<?php endforeach; unset( $_SESSION["header"]["broSto"] ); endif; ?>
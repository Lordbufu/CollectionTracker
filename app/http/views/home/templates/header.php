		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<meta name="author" content="Marco Visscher">
		<meta name="description" content="Een simpele App, voor het bij houden van collecties/verzamelingen.">
		<title>Collectie Tracker <?=$version?></title>
		<link rel="icon" type="image/x-icon" href="images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel='stylesheet' type='text/css' href='css/modal-style.css'>
		<script src="js/main.js"></script>

<?php	// Add the barcode scanning script, only when the option is triggered.
if(isset($_SESSION['_flash']['tags']['pop-in'])) :
	if($_SESSION['_flash']['tags']['pop-in'] === 'bScan') : ?>
		<script src="js/html5-qrcode.min.js"></script>
<?php endif; endif; ?>

<?php // Attempt to load all css and js based on the uri and stored user.
switch($_SERVER["REQUEST_URI"]) :
	// case for default landing route -> '/':
	case '/': ?>
		<link rel="stylesheet" type="text/css" href="css/home.css">
<?php	if(isset($device) && $device === 'mobile') : ?>
			<link rel="stylesheet" type="text/css" href="css/home-mobile.css" >
<?php	elseif(isset($device) && $device === 'tablet') : ?>
			<link rel="stylesheet" type="text/css" href="css/home-ipad.css" >
<?php	else : ?>
			<script src="js/static-elements.js"></script>
<?php	endif; break;
	// case for default home page -> '/home':
	case '/home':
		if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'guest') : ?>
		<link rel="stylesheet" type="text/css" href="css/home.css">
<?php		if(isset($device) && $device === 'mobile') : ?>
				<link rel="stylesheet" type="text/css" href="css/home-mobile.css" >
<?php		elseif(isset($device) && $device === 'tablet') : ?>
				<link rel="stylesheet" type="text/css" href="css/home-ipad.css" >
<?php		else : ?>
				<script src="js/static-elements.js"></script>
<?php		endif; endif; break;
	// case for default user page -> '/gebruik':
	case '/gebruik':
		// Loop for the 'user' user, the gebruik page.
		if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'user') : ?>
		<link rel="stylesheet" type="text/css" href="css/gebruik.css" >
<?php		if(isset($device) && $device === 'mobile') : ?>
				<link rel="stylesheet" type="text/css" href="css/gebruik-mobile.css" >
<?php		elseif(isset($device) && $device === 'tablet') : ?>
				<link rel="stylesheet" type="text/css" href="css/gebruik-ipad.css" >
<?php		else : ?>
				<script src="js/static-elements.js"></script>
<?php		endif; endif; break;
	// case for default administrator page -> '/beheer':
	case '/beheer':
		if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'admin') : ?>
		<link rel="stylesheet" type="text/css" href="css/beheer.css" >
<?php		if(isset($device) && $device === 'mobile') : ?>
				<link rel="stylesheet" type="text/css" href="css/beheer-mobile.css" >
<?php		elseif(isset($device) && $device === 'tablet') : ?>
				<link rel="stylesheet" type="text/css" href="css/beheer-ipad.css" >
<?php		else : ?>
				<script src="js/static-elements.js"></script>
<?php		endif; endif; break; endswitch; ?>


<?php // Store device type in browser storage, for JS script triggers.
	if(isset($device)) : ?>
		<script>localStorage.setItem("device", "<?= $device ?>");</script>
<?php endif; ?>

<?php // Set user name to browser storage, for JS script triggers.
	if(isset($_SESSION['user']['rights'])) : ?>
		<script>localStorage.setItem("user", "<?=$_SESSION['user']['rights']?>");</script>
<?php endif; ?>

<?php // Set user name to browser storage, for JS script triggers.
	if(isset($_SESSION['page-data']['reset'])) : ?>
		<script>localStorage.setItem("reset", "<?=$_SESSION['page-data']['reset']?>");</script>
<?php unset($_SESSION['page-data']['reset']); endif; ?>
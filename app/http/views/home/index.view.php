<!DOCTYPE html>
<html>
	<head>
        <?php require 'templates/header.php'; ?>
    </head>
	<body class="main-flex">
        <noscript>You need to enable JavaScript to run this app.</noscript>
        <?php if(isset($_SESSION['_flash']['feedback'])) { require 'popins/message-pop-in.php'; } ?>
        <div class="sub-grid-1" id="sub-grid-1">
            <?php if(isset($_SESSION['user']['rights'])) { require "templates/{$_SESSION['user']['rights']}/banner-subgrid.php"; } ?>
        </div>
        <div class="sub-grid-2" id="sub-grid-2">
            <?php if(isset($_SESSION['user']['rights'])) { require "templates/{$_SESSION['user']['rights']}/controler-subgrid.php"; } ?>
        </div>
        <div class="sub-grid-3">
            <?php if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] != 'admin') {
                    require "templates/{$_SESSION['user']['rights']}/table-subgrid.php";
                } elseif(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'admin') {
                    if(!isset($_SESSION['page-data']['huidige-reeks'])) {
                        require "templates/{$_SESSION['user']['rights']}/reeks-table-subgrid.php";
                    } else {
                        require "templates/{$_SESSION['user']['rights']}/items-table-subgrid.php";
                    }
                } ?>
        </div>
    <?php   if($_SESSION['user']['rights'] === 'user' && $device === 'mobile') { require "popins/{$_SESSION['user']['rights']}/more-info-mobile.php"; }
            if(isset($_SESSION['_flash']['tags']['pop-in']) && isset($_SESSION['user']['rights'])) {
                if($_SESSION['_flash']['tags']['pop-in'] === 'register') { require "popins/{$_SESSION['user']['rights']}/register.pop-in.php"; }
                if($_SESSION['_flash']['tags']['pop-in'] === 'login') { require "popins/{$_SESSION['user']['rights']}/login.pop-in.php"; }
                if($_SESSION['_flash']['tags']['pop-in'] === 'ww-reset') { require "popins/{$_SESSION['user']['rights']}/wachtwoord-reset-pop-in.php"; }
                if($_SESSION['_flash']['tags']['pop-in'] === 'reeks-maken') { require "popins/{$_SESSION['user']['rights']}/reeks-maken-pop-in.php"; }
                if($_SESSION['_flash']['tags']['pop-in'] === 'items-maken') { require "popins/{$_SESSION['user']['rights']}/items-maken-pop-in.php"; }
                if($_SESSION['_flash']['tags']['pop-in'] === 'bScan') { require 'popins/item-scan-pop-in.php'; }
                if($_SESSION['_flash']['tags']['pop-in'] === 'isbn-preview') { require 'popins/isbn-preview-pop-in.php'; }
            } ?>
        <footer class="sub-grid-4">
            <?php require 'templates/footer.php'; ?>
        </footer>
    </body>
</html>
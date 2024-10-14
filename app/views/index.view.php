<!DOCTYPE html>

<html>
    <head>
        <?php require( "templates/shared/header.php" ); ?>
    </head>

    <body class="main-flex" id="main-flex" >

    <noscript> You need to enable JavaScript to run this app. </noscript>

    <?php require( "pop-ins/message-pop-in.html" ); ?>

    <div class="sub-grid-1" id="sub-grid-1">

        <?php require( "templates/landing/landing-banner-buttons.html" ); ?>

        <div class="banner-head" id="banner-head" >
            <h1 class="header-text" id="header-text" > Collectie Tracker <?= $version ?> </h1>
        </div>

		</div>

		<div class="sub-grid-2" id="sub-grid-2" >

        <div class="contr-cont-1" id="contr-cont-1" > </div>

        <div class="contr-cont-2" id="contr-cont-2" >
            <?php require( "templates/landing/landing-contr-content.html" ); ?>
        </div>

        <div class="contr-cont-3" id="contr-cont-3" > </div>

		</div>

		<div class="sub-grid-3" id="sub-grid-3" >

        <div class="table-header" id="table-header" >
            <?php require( "templates/landing/landing-table-header.html" ); ?>
        </div>

        <div class="table-templ" id="table-templ" >
            <?php require( "templates/landing/landing-table-cont.html" ); ?>
        </div>

    </div>

    <?php require( "pop-ins/landing/landing-account-maken.html" );
          require( "pop-ins/landing/landing-gebr-overeenk.html" );
          require( "pop-ins/landing/landing-account-login.html" );
          require( "pop-ins/landing/landing-wachtwoord-reset.html" );
    ?>

		<footer class="sub-grid-4" id="sub-grid-4">
        <?php require( "templates/shared/footer.php" ); ?>
    </footer>

    </body>

</html>
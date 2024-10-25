<!DOCTYPE html>

<html>

	<head>
        <?php require( "templates/shared/header.php" ); ?>
    </head>

	<body class="main-flex" id="main-flex" >

        <noscript> You need to enable JavaScript to run this app. </noscript>

        <?php require( "pop-ins/message-pop-in.html" ); ?>

        <div class="sub-grid-1" id="sub-grid-1">

			 <?php require( "templates/gebruik/gebruik-banner-buttons.html" ); ?>

			<div class="banner-head" id="banner-head" >
                <h1 class="header-text" id="header-text" > Collectie Tracker: Gebruik </h1>
            </div>

		</div>

		<div class="sub-grid-2" id="sub-grid-2" >

			<div class="contr-cont-1" id="contr-cont-1" >
                <?php require( "templates/gebruik/gebruik-contr-cont-1.php" ); ?>
            </div>

			<div class="contr-cont-2" id="contr-cont-2" >
                <?php require( "templates/gebruik/gebruik-contr-cont-2.html" ); ?>
            </div>

			<?php require( "templates/gebruik/gebruik-contr-cont-3.php" ); ?>

		</div>

		<div class="sub-grid-3" id="sub-grid-3" >

            <div class="table-header" id="table-header" >

            <?php if( isset( $_SESSION["page-data"]["huidige-serie"] ) ): ?>
                <h2 id="beheer-albView-text" class="beheer-weerg-header" > <?= $_SESSION["page-data"]["huidige-serie"] ?> </h2>
            <?php else: ?>
                <h2 id="beheer-serieView-text" class="beheer-weerg-header" > Selecteer een Serie: </h2>
            <?php endif; ?>

            </div>

            <?php
                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) {
                    require( "templates/gebruik/gebruik-table-templ.php" );
                }
            ?>

        </div>

        <?php
            require( "pop-ins/gebruik/gebruik-albumS-pop-in.php" );

            if( isset( $_SESSION["page-data"]["mobile-details"] ) ) {
                require( "pop-ins/gebruik/more-info-mobile.php" );
            }
        ?>

		<footer class="sub-grid-4" id="sub-grid-4">
            <?php require( "templates/shared/footer.php" ); ?>
        </footer>

    </body>

</html>
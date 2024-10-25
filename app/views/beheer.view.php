<!DOCTYPE html>

<html>

	<head>
        <?php require( "templates/shared/header.php" ); ?>
    </head>

	<body class="main-flex" id="main-flex" >

        <noscript> You need to enable JavaScript to run this app. </noscript>

        <?php require( "pop-ins/message-pop-in.html" ); ?>

        <div class="sub-grid-1" id="sub-grid-1">

			<?php require( "templates/beheer/beheer-banner-buttons.php" ); ?>

			<div class="banner-head" id="banner-head" > <h1 class="header-text" id="header-text" > Collectie Tracker: Beheer </h1> </div>

		</div>

		<div class="sub-grid-2" id="sub-grid-2" >

			<div class="contr-cont-1" id="contr-cont-1" >
                <?php require( "templates/beheer/beheer-contr-cont-1.html" ); ?>
            </div>

			<div class="contr-cont-2" id="contr-cont-2" >
                <?php require( "templates/beheer/beheer-contr-cont-2.php" ); ?>
            </div>

			<div class="contr-cont-3" id="contr-cont-3" >
                <?php require( "templates/beheer/beheer-contr-cont-3.html" ); ?>
            </div>

		</div>

		<div class="sub-grid-3" id="sub-grid-3" >

            <div class="table-header" id="table-header" >

            <?php if( isset( $_SESSION["page-data"]["huidige-serie"] ) ): ?>
                <h2 id="beheer-albView-text" class="beheer-weerg-header" > <?= $_SESSION["page-data"]["huidige-serie"] ?> </h2>
            <?php else: ?>
                <h2 id="beheer-serieView-text" class="beheer-weerg-header" > Huidige Series: </h2>
            <?php endif; ?>

            </div>

            <div class="table-templ" id="table-templ" >

            <?php
                if( !isset( $_SESSION["page-data"]["huidige-serie"] ) ) {
                    require( "templates/beheer/beheer-serie-table.php" );
                } else {
                    require( "templates/beheer/beheer-album-table.php" );
                }
            ?>

            </div>

        </div>

        <?php
            require( "pop-ins/beheer/beheer-serie-maken-pop-in.php" );
            require( "pop-ins/beheer/beheer-serie-bewerken-pop-in.php" );
            require( "pop-ins/beheer/beheer-album-toevoegen-pop-in.php" );
            require( "pop-ins/beheer/beheer-album-bewerken-pop-in.php" );
            require( "pop-ins/beheer/beheer-isbn-scan-pop-in.php" );
            require( "pop-ins/beheer/beheer-wachtwoord-reset2-pop-in.html" );

            // Load review pop-in, if a isbn scan returned more then 1 item.
            if( isset( $_SESSION["page-data"]["show-titles"] ) ) {
                require( "pop-ins/beheer/beheer-review-isbn-titles.php" );
            }
        ?>

		<footer class="sub-grid-4" id="sub-grid-4">
            <?php require( "templates/shared/footer.php" ); ?>
        </footer>

    </body>
    
</html>
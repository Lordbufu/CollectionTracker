<div id="albumt-pop-in" class="modal-container">
    <div class="modal-content-container">

        <div class="modal-header">  
            <h1 class="modal-header-text"> Album Toevoegen </h1>
            <form class="modal-header-close-form" method="post" action="/beheer" >
                <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>

        <div class="modal-body">
            <?php
                if( isset( $_SESSION["page-data"]["album-dupl"] ) ) {

                    foreach( $_SESSION["page-data"]["album-dupl"] as $key => $value ) {
                        $store[$key] = $value;
                    }
                    
                    unset( $_SESSION["page-data"]["album-dupl"] );

                    require( "beheer-albumT-duplicate.php" );
                } else if ( isset( $_SESSION["page-data"]["isbn-search"] ) || isset( $_SESSION["page-data"]["isbn-scan"] ) ) {

                    if( isset( $_SESSION["page-data"]["isbn-scan"] ) && isset( $_SESSION["page-data"]["barcode"] ) ) {
                        $store = $_SESSION["page-data"]["isbn-scan"];
                        unset( $_SESSION["page-data"]["isbn-scan"] );
                        unset( $_SESSION["page-data"]["barcode"] );
                    }

                    if( isset( $_SESSION["page-data"]["isbn-search"] ) && isset( $_SESSION["page-data"]["searched"] ) ) {
                        $store = $_SESSION["page-data"]["isbn-search"];
                        unset( $_SESSION["page-data"]["isbn-search"] );
                        unset( $_SESSION["page-data"]["searched"] );
                    }

                    require( "beheer-albumT-search.php" );
                } else  {

                    if( isset($_SESSION["page-data"]["add-album"] ) ) {
                        $store = $_SESSION["page-data"]["add-album"];
                        unset( $_SESSION["page-data"]["add-album"] );
                    }

                    require( "beheer-albumT-clean.php" );
                }
            ?>
        </div>
    </div>
</div>
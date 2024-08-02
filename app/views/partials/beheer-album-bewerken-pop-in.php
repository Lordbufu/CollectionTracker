<div id="albumb-pop-in" class="modal-container">
    <div class="modal-content-container">

        <div class="modal-header">  
            <h1 class="modal-header-text"> Album Bewerken </h1>
            <form class="modal-header-close-form" method="post" action="/beheer">
                <input class="modal-header-input" name="close-pop-in" value="" hidden />
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>

        <div class="modal-body">
            <?php
                // If the album-edit is set, get the album data and load the edit template.
                if( isset( $_SESSION["page-data"]["album-edit"] ) ) {
                    foreach( $_SESSION["page-data"]["albums"] as $index => $album ) {
                        if( $album["Album_Index"] == $_SESSION["page-data"]["album-edit"] ) {
                            $store = $_SESSION["page-data"]["albums"][$index];
                        }
                    } 
                    unset( $_SESSION["page-data"]["album-edit"] );  // unset tag to prevent unexpected behavior
                    require("beheer-albumB-edit.php");
                // If the isbn-search is set, get the album data and load the isbn-search template.
                } elseif( isset( $_SESSION["page-data"]["isbn-search"] ) ) {
                    $result = $_SESSION["page-data"]["isbn-search"]; 
                    unset( $_SESSION["page-data"]["isbn-search"] ); // unset tag to prevent unexpected behavior
                    require("beheer-albumB-search.php");
                // If no special tags are set, load the default clean pop-in.                    
                } else {
                    require("beheer-albumB-clean.php");
                }
            ?>
            </form>
        </div>
    </div>
</div>
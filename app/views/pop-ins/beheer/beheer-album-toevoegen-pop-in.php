        <div id="albumt-pop-in" class="modal-cont" >

            <div class="modal-content-cont" id="modal-content-cont" >

                <div class="modal-header-cont" id="modal-header-cont" >

                    <h3 class="modal-header-text" id="modal-header-text" > Album Toevoegen </h3>

                    <form class="modal-header-close-form" method="post" action="/beheer" >
                        <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                        <input class="modal-header-close" type="submit" value="&times;" />
                    </form>

                </div>

                <div class="modal-body" id="modal-body" >

                <?php
                    /* Deal with duplicate entries detected by PhP */
                    if( isset( $_SESSION["page-data"]["album-dupl"] ) ) {
                        foreach( $_SESSION["page-data"]["album-dupl"] as $key => $value ) {
                            $tStore[$key] = $value;
                        }

                        unset( $_SESSION["page-data"]["album-dupl"] );

                    /* Deal with manual ISBN searches, and barcode scanning  */
                    } elseif( isset( $_SESSION["page-data"]["isbn-search"] ) || isset( $_SESSION["page-data"]["isbn-scan"] ) ) {

                        /* Loop for barcode scanning */
                        if( isset( $_SESSION["page-data"]["isbn-scan"] ) ) {
                            $tStore = $_SESSION["page-data"]["isbn-scan"];
                            unset( $_SESSION["page-data"]["isbn-scan"] );
                            if( isset( $_SESSION["page-data"]["shown-titles"] ) ) {
                                unset( $_SESSION["page-data"]["shown-titles"] );
                            }
                        }

                        /* Loop for manual isbn seaching */
                        if( isset( $_SESSION["page-data"]["isbn-search"] ) && isset( $_SESSION["page-data"]["searched"] ) ) {
                            $tStore = $_SESSION["page-data"]["isbn-search"];
                            unset( $_SESSION["page-data"]["searched"] );
                        }
                        
                    /* Only normal add-album cases remain */
                    } elseif( isset($_SESSION["page-data"]["add-album"] ) )  {
                        $tStore = $_SESSION["page-data"]["add-album"];
                        unset( $_SESSION["page-data"]["add-album"] );
                    }
                ?>
                    <form class="modal-form" id="albumt-form" enctype="multipart/form-data" method="post" action="/albumT" >

                    <?php if( isset( $tStore ) && !is_array( $tStore ) ) : ?>
                        <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?= $tStore ?>" hidden />
                    <?php elseif( isset( $tStore["Album_Serie"] )  ) : ?>
                        <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?= $tStore["Album_Serie"] ?>" hidden />
                    <?php else : ?>
                        <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" hidden />
                    <?php endif; ?>

                        <div class="modal-form-left-cont" id="modal-form-left-cont">

                            <p id="modal-small-text" class="modal-small-text" > De album naam & isbn zijn verplichte velden </p>

                            <label class="modal-form-label">
                            <?php if( isset( $tStore["Album_Naam"] ) ) : ?>
                                <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" value="<?= $tStore["Album_Naam"] ?>" placeholder="" autocomplete="on" required />
                            <?php else : ?>
                                <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" placeholder="" autocomplete="on" required />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Naam </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $tStore["Album_Nummer"] ) ) : ?>
                                <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" value="<?= $tStore["Album_Nummer"] ?>" placeholder="" autocomplete="on" />
                            <?php else : ?>
                                <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Uitgave Nummer </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $tStore["Album_UigDatum"] ) ) : ?>
                                <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" value="<?= $tStore["Album_UigDatum"] ?>" placeholder="" autocomplete="on" />
                            <?php else : ?>
                                <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Uitgave Datum </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $tStore["Album_Schrijver"] ) ) : ?>
                                <input type="text" class="modal-form-input" id="albumt-form-alb-schr" name="album-schrijver" placeholder="" value="<?= $tStore["Album_Schrijver"] ?>" autocomplete="on" />
                            <?php else : ?>
                                <input type="text" class="modal-form-input" id="albumt-form-alb-schr" name="album-schrijver" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Schrijver </span>
                            </label>

                            <div class="modal-album-cover" id="albumT-cover">
                            <?php if( !empty( $tStore["Album_Cover"] ) ) : ?>
                                <img class="modal-album-cover-img" id="albumt-cover-img" src="<?= $tStore["Album_Cover"] ?>" >
                            <?php endif; ?>
                            </div>

                            <label class="modal-form-alb-cov-lab button" id="modal-albumt-cov-lab" for="albumt-form-alb-cov">
                                <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumt-form-alb-cov" name="album-cover" />
                            <?php if( !empty( $tStore["Album_Cover"] ) ) : ?>
                                Nieuwe Cover Selecteren
                            <?php else : ?>
                                Selecteer een Album Cover
                            <?php endif; ?>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $tStore["Album_ISBN"] ) ) : ?>
                                <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" value="<?= $tStore["Album_ISBN"] ?>" placeholder="" autocomplete="on" required />
                            <?php else : ?>
                                <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" placeholder="" autocomplete="on" required />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album ISBN </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $tStore["Album_Opm"] ) ) : ?>
                                <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" value="<?= $tStore["Album_Opm"] ?>" autocomplete="on" />
                            <?php else : ?>
                                <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span" hidden> Album Opmerking </span>
                            </label>

                            <div class="butt-box" id="butt-box" >
                                <input class="modal-form-button button" id="albumt-form-button" type="submit" value="Bevestigen" />
                            </div>

                        </div>

                        <div class="modal-form-right-cont" id="modal-albumT-right-cont">

                            <div class="modal-form-fake-triger"> </div>

                            <div class="modal-form-fake-triger"> </div>

                            <div class="modal-form-fake-triger"> </div>

                            <div class="modal-form-fake-triger"> </div>

                        <?php if( !empty( $tStore["Album_Cover"] ) ) : ?>
                            <div class="modal-form-fake-triger" id="modal-form-albAdd-cov-trigger" > </div>
                        <?php else : ?>
                            <div class="modal-form-fake-triger" id="modal-form-albAdd-cov-trigger" hidden > </div>
                        <?php endif; ?>

                            <div class="modal-form-fake-triger" id="modal-form-addCov-trigger" > </div>

                            <button class="modal-form-isbn-triger" id="modal-form-albAdd-isbn-triger" formaction="/isbn" method="post" type="submit"></button>

                            <div class="modal-form-fake-triger"> </div>

                            <div class="modal-form-fake-triger"> </div>

                        </div>

                    </form>
                    
                </div>

            </div>
            
        </div>
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
                            $store[$key] = $value;
                        }

                        unset( $_SESSION["page-data"]["album-dupl"] );

                    /* Deal with manual ISBN searches, and barcode scanning  */
                    } elseif( isset( $_SESSION["page-data"]["isbn-search"] ) || isset( $_SESSION["page-data"]["isbn-scan"] ) ) {

                        /* Code for barcode scanning */
                        if( isset( $_SESSION["page-data"]["isbn-scan"] ) && isset( $_SESSION["page-data"]["barcode"] ) ) {
                            $store = $_SESSION["page-data"]["isbn-scan"];
                            unset( $_SESSION["page-data"]["isbn-scan"] );
                            unset( $_SESSION["page-data"]["barcode"] );
                        }

                        /* Loop for manual isbn seaching */
                        if( isset( $_SESSION["page-data"]["isbn-search"] ) && isset( $_SESSION["page-data"]["searched"] ) ) {
                            $store = $_SESSION["page-data"]["isbn-search"];
                        }

                    /* Only normal add-album cases remain */
                    } else  {

                        // Still check if a value was set, to prevent unexpected errors.
                        if( isset($_SESSION["page-data"]["add-album"] ) ) {
                            $store = $_SESSION["page-data"]["add-album"];
                        }
                    }
                ?>
                    <form class="modal-form" id="albumt-form" enctype="multipart/form-data" method="post" action="/albumT" >

                    <?php if( isset( $store ) && !is_array( $store ) ) : ?>
                        <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?= $store ?>" hidden />
                    <?php elseif( isset( $store["Album_Serie"] )  ) : ?>
                        <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?= $store["Album_Serie"] ?>" hidden />
                    <?php else : ?>
                        <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" hidden />
                    <?php endif; ?>

                        <div class="modal-form-left-cont" id="modal-form-left-cont">

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_Naam"] ) ) : ?>
                                <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" value="<?= $store["Album_Naam"] ?>" placeholder="" autocomplete="on" required />
                            <?php else : ?>
                                <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" placeholder="" autocomplete="on" required />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Naam </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_Nummer"] ) ) : ?>
                                <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" value="<?= $store["Album_Nummer"] ?>" placeholder="" autocomplete="on" />
                            <?php else : ?>
                                <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Uitgave Nummer </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_UigDatum"] ) ) : ?>
                                <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" value="<?= $store["Album_UigDatum"] ?>" placeholder="" autocomplete="on" />
                            <?php else : ?>
                                <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Uitgave Datum </span>
                            </label>

                            <!-- W.I.P. Album Schrijver -->
                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="albumt-form-alb-schr" name="album-schrijver" placeholder="" autocomplete="on" disabled />
                                <span class="modal-form-span"> Album Schrijver </span>
                            </label>

                            <div class="modal-album-cover" id="albumT-cover">
                            <?php if( !empty( $store["Album_Cover"] ) ) : ?>
                                <img class="modal-album-cover-img" id="albumt-cover-img" src="<?= $store["Album_Cover"] ?>" >
                            <?php endif; ?>
                            </div>

                            <label class="modal-form-alb-cov-lab" id="modal-albumt-cov-lab" for="albumt-form-alb-cov">
                                <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumt-form-alb-cov" name="album-cover" />
                            <?php if( !empty( $store["Album_Cover"] ) ) : ?>
                                Selecteer nogmaals een Album Cover
                            <?php else : ?>
                                Selecteer een Album Cover
                            <?php endif; ?>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_ISBN"] ) ) : ?>
                                <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" value="<?= $store["Album_ISBN"] ?>" placeholder="" autocomplete="on" required />
                            <?php else : ?>
                                <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" placeholder="" autocomplete="on" required />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album ISBN </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_Opm"] ) ) : ?>
                                <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" value="<?= $store["Album_Opm"] ?>" autocomplete="on" />
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

                            <div class="modal-form-fake-triger" id="modal-form-albAdd-cov-trigger" hidden > </div>

                            <div class="modal-form-fake-triger" id="modal-form-addCov-trigger" > </div>

                            <button class="modal-form-isbn-triger" id="modal-form-albAdd-isbn-triger" formaction="/isbn" method="post" type="submit"></button>

                            <div class="modal-form-fake-triger"> </div>

                            <div class="modal-form-fake-triger"> </div>

                        </div>

                    </form>
                    
                </div>
            </div>
        </div>
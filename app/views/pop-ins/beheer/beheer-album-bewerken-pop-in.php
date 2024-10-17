        <div id="albumb-pop-in" class="modal-cont" >

            <div class="modal-content-cont" id="modal-content-cont" >

                <div class="modal-header-cont" id="modal-header-cont" >

                    <h3 class="modal-header-text" id="modal-header-text" > Album Bewerken </h3>

                    <form class="modal-header-close-form" method="post" action="/beheer">
                        <input class="modal-header-input" name="close-pop-in" value="" hidden />
                        <input class="modal-header-close" type="submit" value="&times;" />
                    </form>

                </div>

                <div class="modal-body" id="modal-body" >

                    <?php
                        if( isset( $_SESSION["page-data"]["album-edit"] ) ) {
                            foreach( $_SESSION["page-data"]["albums"] as $index => $album ) {
                                if( $album["Album_Index"] == $_SESSION["page-data"]["album-edit"] ) {
                                    $store = $_SESSION["page-data"]["albums"][$index];
                                }
                            }

                            unset( $_SESSION["page-data"]["album-edit"] );
                        } elseif( isset( $_SESSION["page-data"]["isbn-search"] ) ) {
                            $store = $_SESSION["page-data"]["isbn-search"]; 
                        }
                    ?>

                    <form class="modal-form" id="albumb-form" enctype="multipart/form-data" method="post" action="/albumBew" >

                    <?php if( isset( $store["Album_Serie"] ) ) : ?>
                        <input type="text" class="modal-form-indexS" id="albumb-form-indexS" name="serie-index" value="<?= $store["Album_Serie"] ?>" hidden />
                    <?php else : ?>
                        <input type="text" class="modal-form-indexS" id="albumb-form-indexS" name="serie-index" value="" hidden />
                    <?php endif; ?>

                    <?php if( isset( $store["Album_Index"] ) ) : ?>
                        <input type="text" class="modal-form-indexA" id="albumb-form-indexA" name="album-index" value="<?= $store["Album_Index"] ?>" hidden />
                    <?php else : ?>
                        <input type="text" class="modal-form-indexA" id="albumb-form-indexA" name="album-index" value="" hidden />
                    <?php endif; ?>

                        <div class="modal-form-left-cont">

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_Naam"] ) ) : ?>
                                <input type="text" class="modal-form-input" id="albumb-form-alb-naam" name="album-naam" placeholder="" value="<?= $store["Album_Naam"] ?>" autocomplete="on" required />
                            <?php else : ?>
                                <input type="text" class="modal-form-input" id="albumb-form-alb-naam" name="album-naam" placeholder="" value="" autocomplete="on" required />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Naam </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_Nummer"] ) ) : ?>
                                <input type="number" min="0" class="modal-form-input" id="albumb-form-alb-nr" name="album-nummer" placeholder="" value="<?= $store["Album_Nummer"] ?>" autocomplete="on" />
                            <?php else : ?>
                                <input type="number" min="0" class="modal-form-input" id="albumb-form-alb-nr" name="album-nummer" placeholder="" value="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Uitgave Nummer </span>
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_UitgDatum"] ) ) : ?>
                                <input type="date" class="modal-form-input" id="albumb-form-alb-date" name="album-datum" placeholder=""  value="<?= $store["Album_UitgDatum"] ?>" autocomplete="on" />
                            <?php else : ?>
                                <input type="date" class="modal-form-input" id="albumb-form-alb-date" name="album-datum" placeholder=""  value="" autocomplete="on" />                                
                            <?php endif; ?>
                                <span class="modal-form-span"> Album Uitgave Datum </span>
                            </label>

                            <!-- W.I.P. Album Schrijver -->
                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="albumb-form-alb-schr" name="album-schrijver" placeholder="" autocomplete="on" disabled />
                                <span class="modal-form-span"> Album Schrijver </span>
                            </label>

                            <div class="modal-album-cover" id="albumB-cover">
                            <?php if( !empty( $store["Album_Cover"] ) ) : ?>
                                <img class="modal-album-cover-img" id="albumb-cover-img" src="<?= $store["Album_Cover"] ?>" alt='album-cover'/>
                            <?php endif; ?>
                            </div>

                            <label class="modal-form-alb-cov-lab button" id="modal-albumb-cov-lab" for="albumb-form-alb-cov">
                                <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                                Album Cover Selecteren
                            </label>

                            <label class="modal-form-label">
                            <?php if( isset( $store["Album_ISBN"] ) ) : ?>
                                <input class="modal-form-input" id="albumb-form-alb-isbn" name="album-isbn" placeholder="" value="<?= $store["Album_ISBN"] ?>" autocomplete="on" required />
                            <?php else : ?>
                                <input class="modal-form-input" id="albumb-form-alb-isbn" name="album-isbn" placeholder="" value="" autocomplete="on" required />
                            <?php endif; ?>
                                <span class="modal-form-span"> Album ISBN </span>
                            </label>

                            <label class="modal-form-label" >
                            <?php if( isset( $store["Album_Opm"] ) ) :?>
                                <input class="modal-form-input" id="albumb-form-alb-opm" name="album-opm" placeholder="" value="<?= $store["Album_Opm"] ?>" autocomplete="on" />
                            <?php else : ?>
                                <input class="modal-form-input" id="albumb-form-alb-opm" name="album-opm" placeholder="" value="" autocomplete="on" />
                            <?php endif; ?>
                                <span class="modal-form-span" hidden> Album Opmerking </span>
                            </label>

                            <div class="butt-box" id="butt-box" >
                                <input class="modal-form-button button" id="albumb-form-button" type="submit" value="Bevestigen" />
                            </div>

                        </div>


                        <div class="modal-form-right-cont" id="modal-albumB-right-cont" >

                            <div class="modal-form-fake-triger" > </div>

                            <div class="modal-form-fake-triger" > </div>

                            <div class="modal-form-fake-triger" > </div>

                            <div class="modal-form-fake-triger" > </div>

                            <?php if( !empty( $store["Album_Cover"] ) ) : ?>
                            <div class="modal-form-fake-triger" id="modal-form-albEdit-cov-trigger" > </div>
                            <?php else : ?>
                            <div class="modal-form-fake-triger" id="modal-form-albEdit-cov-trigger" hidden > </div>
                            <?php endif; ?>

                            <div class="modal-form-fake-triger" > </div>

                            <button class="modal-form-isbn-triger" id="modal-form-albEdit-isbn-triger" formaction="/isbn" method="post" type="submit" > </button>

                            <div class="modal-form-fake-triger" > </div>

                            <div class="modal-form-fake-triger" > </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>
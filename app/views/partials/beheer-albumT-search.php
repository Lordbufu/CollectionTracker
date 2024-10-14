            <form class="modal-form" id="albumt-form" enctype="multipart/form-data" method="post" action="/albumT" >
                <div class="modal-form-content-cont">

                    <!-- HIDDEN input for the serie index, that the album was being added to -->
                    <?php if( isset( $store["serie-index"] ) ) : ?>
                    <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?=$store["serie-index"]?>" hidden />
                    <?php else : ?>
                    <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" hidden />
                    <?php endif; ?>

                    <!-- Pop-In left-side content (search template) -->
                    <div class="modal-form-left-cont">

                        <!-- Input for the album naam -->
                        <label class="modal-form-label">
                            <?php if( isset( $store["album-naam"] ) ) : ?>
                            <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" value="<?=$store["album-naam"]?>" placeholder="" autocomplete="on" required />
                            <?php else : ?>
                            <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" placeholder="" autocomplete="on" required />
                            <?php endif; ?>
                            <span class="modal-form-span"> Album Naam </span>
                        </label>

                        <!-- Input for the album number -->
                        <label class="modal-form-label">
                            <?php if( isset( $store["album-nummer"] ) ) : ?>
                            <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" value="<?=$store["album-nummer"]?>" placeholder="" autocomplete="on" />
                            <?php else : ?>
                            <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" placeholder="" autocomplete="on" />            
                            <?php endif; ?>
                            <span class="modal-form-span"> Album Uitgave Nummer </span>
                        </label>

                        <!-- Input for the album publish date -->
                        <label class="modal-form-label">
                            <?php if( isset( $store["album-datum"] ) ) : ?>
                            <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" value="<?=$store["album-datum"]?>" placeholder="" autocomplete="on" />
                            <?php else : ?>
                            <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                            <span class="modal-form-span"> Album Uitgave Datum </span>
                        </label>

                        <!-- Preview for the album cover, if one was included -->
                        <div class="modal-album-cover" id="albumT-cover">
                        <?php if( !empty( $store["album-cover"] ) ) : ?>
                            <img src="<?=$store["album-cover"]?>" id="albumb-cover-img" class="modal-album-cover-img">
                        <?php endif; ?>
                        </div>

                        <!-- Input for the album cover -->
                        <label class="modal-form-alb-cov-lab" id="modal-form-alb-cov-lab" for="albumt-form-alb-cov">
                            <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumt-form-alb-cov" name="album-cover" />
                            <?php if( !empty( $store["album-cover"] ) ) : ?>
                            Selecteer een andere Cover
                            <?php else : ?>
                            Selecteer een Album Cover
                            <?php endif; ?>
                        </label>

                        <!-- Input for the album isbn number -->
                        <label class="modal-form-label">
                            <?php if( isset( $store["album-isbn"] ) ) : ?>
                            <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" value="<?=$store["album-isbn"]?>" placeholder="" autocomplete="on" required />
                            <?php else : ?>
                            <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" placeholder="" autocomplete="on" required />
                            <?php endif; ?>
                            <span class="modal-form-span"> Album ISBN </span>
                        </label>

                        <!-- Input for the album comments -->
                        <label class="modal-form-label">
                            <?php if( isset( $store["album-opm"] ) ) : ?>
                            <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" value="<?=$store["album-opm"]?>" autocomplete="on" />
                            <?php else : ?>
                            <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" autocomplete="on" />
                            <?php endif; ?>
                            <span class="modal-form-span" hidden> Album Opmerking </span>
                        </label>

                        <input class="modal-form-button" id="albumt-form-button" type="submit" value="Bevestigen" />
                    </div>
                </form>

                    <!-- Pop-In right-side content -->
                    <div class="modal-form-right-cont">
                        <!-- Album naam fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album nummer fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album datum fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <?php if( !empty( $store["album-cover"] ) ) : ?>
                        <!-- Album cover preview fake trigger -->
                        <button class="modal-form-fake-triger" id="modal-form-albAdd-cov-trigger" disabled> </button>
                        <?php endif; ?>

                        <!-- Album cover fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album isbn submit trigger -->
                        <form id="isbn-trigger-form" action="/isbn" method="post">
                            <button class="modal-form-isbn-triger" id="modal-form-albAdd-isbn-triger" type="button"></button>
                        </form>

                        <!-- Album opmerking fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album submit fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>
                    </div>
                    
                </div>

            <?php unset( $store ); ?>
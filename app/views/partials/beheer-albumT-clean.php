            <form class="modal-form" id="albumt-form" enctype="multipart/form-data" method="post" action="/albumT" >
                <div class="modal-form-content-cont">

                    <?php if ( isset( $store ) ) : ?>
                    <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?=$store?>" hidden />
                    <?php else : ?>
                    <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="" hidden />
                    <?php endif; ?>

                    <!-- Pop-In left-side content (clean template) -->
                    <div class="modal-form-left-cont">
                        <label class="modal-form-label">
                            <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" placeholder="" autocomplete="on" required />
                            <span class="modal-form-span"> Album Naam </span>
                        </label>

                        <label class="modal-form-label">
                            <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" placeholder="" autocomplete="on" />
                            <span class="modal-form-span"> Album Uitgave Nummer </span>
                        </label>

                        <label class="modal-form-label">
                            <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" placeholder="" autocomplete="on" />
                            <span class="modal-form-span"> Album Uitgave Datum </span>
                        </label>

                        <!-- Placeholder for a preview image -->
                        <div class="modal-album-cover" id="albumT-cover"> </div>

                        <label class="modal-form-alb-cov-lab" id="modal-form-alb-cov-lab" for="albumt-form-alb-cov">
                            <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumt-form-alb-cov" name="album-cover" />
                            Album Cover Selecteren
                        </label>

                        <label class="modal-form-label">
                            <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" placeholder="" autocomplete="on" required />
                            <span class="modal-form-span"> Album ISBN </span>
                        </label>
                        
                        <label class="modal-form-label" >
                            <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" value="" autocomplete="on" />
                            <span class="modal-form-span" hidden> Album Opmerking </span>
                        </label>

                        <input class="modal-form-button" id="albumt-form-button" type="submit" value="Bevestigen" />
                    </div>
                    </form>

                    <!-- Pop-In right-side content -->
                    <div class="modal-form-right-cont" style="padding: 1em 0em 1em 0em;">

                        <!-- Album naam fake trigger -->
                        <div class="modal-form-fake-triger"> </div>

                        <!-- Album nummer fake trigger -->
                        <div class="modal-form-fake-triger"> </div>

                        <!-- Album datum fake trigger -->
                        <div class="modal-form-fake-triger"> </div>


                        <?php if( !empty( $store["album-cover"] ) ) : ?>
                        <!-- Album cover preview fake trigger -->
                        <div class="modal-form-fake-triger" id="modal-form-albAdd-cov-trigger"> </div>
                        <?php endif; ?>

                        <!-- Album cover fake trigger -->
                        <div class="modal-form-fake-triger"> </div>

                        <!-- Album isbn submit trigger -->
                        <form id="isbn-trigger-form" action="/isbn" method="post">
                            <button class="modal-form-isbn-triger" id="modal-form-albAdd-isbn-triger" type="button"></button>
                        </form>

                        <!-- Album opmerking fake trigger -->
                        <div class="modal-form-fake-triger"> </div>

                        <!-- Album submit fake trigger -->
                        <div class="modal-form-fake-triger"> </div>
                    </div>
                </div>

            <?php unset( $store ); ?>
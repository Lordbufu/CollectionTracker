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
            <form class="modal-form" id="albumt-form" enctype="multipart/form-data" method="post" action="/albumT" >

            <?php
                if(isset($_SESSION['page-data']['album-dupl'])) :
                    foreach($_SESSION['page-data']['album-dupl'] as $key => $value) {
                        $store[$key] = $value;
                    }
            ?>
                <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?=$store['serie-index']?>" hidden />

                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" value="<?=$store['album-naam']?>" placeholder="" autocomplete="on" required />
                    <span class="modal-form-span"> Album Naam </span>
                </label>

                <label class="modal-form-label">
                    <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" value="<?=$store['album-nummer']?>" placeholder="" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Nummer </span>
                </label>

                <label class="modal-form-label">
                    <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" value="<?=$store['album-datum']?>" placeholder="" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Datum </span>
                </label>

                <div class="modal-album-cover" id="albumT-cover">
                <?php if( !empty( $store['album-cover'] ) ) : ?>
                    <img src="<?=$store['album-cover']?>" id="albumb-cover-img" class="modal-album-cover-img">
                <?php endif; ?>
                </div>

                <label class="modal-form-alb-cov-lab" id="modal-form-alb-cov-lab" for="albumt-form-alb-cov">
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumt-form-alb-cov" name="album-cover" />
                    <?php if( !empty( $store['album-cover'] ) ) : ?>
                    Selecteer nogmaals een Album Cover
                    <?php else : ?>
                    Selecteer een Album Cover
                    <?php endif; ?>
                </label>

                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" value="<?=$store['album-isbn']?>" placeholder="" autocomplete="on" required />
                    <span class="modal-form-span"> Album ISBN </span>
                </label>
                <!--<label class="modal-form-label">
                    <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" value="" autocomplete="on" hidden />
                    <span class="modal-form-span" hidden> Album Opmerking </span>
                </label>-->

                <input class="modal-form-button" id="albumt-form-button" type="submit" value="Bevestigen" />

        <?php
                // unset stored data to avoid unexpected behavior
                unset($store);
            else :
        ?>
                <?php if(isset($_SESSION['page-data']['add-album'])) : ?>
                <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="<?=$_SESSION['page-data']['add-album']?>" hidden />
                <?php else : ?>
                <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value="" hidden />
                <?php endif; ?>

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

                <!--<label class="modal-form-label">
                    <input class="modal-form-input" id="albumt-form-alb-opm" name="album-opm" placeholder="" value="" autocomplete="on" hidden />
                    <span class="modal-form-span" hidden> Album Opmerking </span>
                </label>-->

                <input class="modal-form-button" id="albumt-form-button" type="submit" value="Bevestigen" />

            <?php
                endif;
            ?>

            </form>
        </div>
    </div>
</div>
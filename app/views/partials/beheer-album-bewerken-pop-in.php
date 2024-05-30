<div id="albumb-pop-in" class="modal-container">
    <div class="modal-content-container">

        <div class="modal-header">  
            <h1 class="modal-header-text"> Album Bewerken </h1>
            <form class="modal-header-close-form" >
                <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" type="button" value="&times;" onclick="closePopIn(event)" />
            </form>
        </div>

        <div class="modal-body">
        <?php
            if( isset( $_SESSION['page-data']['album-edit'] ) ) :
                foreach( $_SESSION['page-data']['albums'] as $index => $album ) {
                    if( $album['Album_Index'] == $_SESSION['page-data']['album-edit'] ) {
                        $store = $_SESSION['page-data']['albums'][$index];
                    }
                }
        ?>
            <form class="modal-form" id="albumb-form" enctype="multipart/form-data" method="post" action="/albumBew">
                <input type="text" class="modal-form-indexS" id="albumb-form-indexS" name="serie-index" value="<?=$store['Album_Serie']?>" hidden />
                <input type="text" class="modal-form-indexA" id="albumb-form-indexA" name="album-index" value="<?=$store['Album_Index']?>" hidden />
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="albumb-form-alb-naam" name="album-naam" placeholder="" value="<?=$store['Album_Naam']?>" autocomplete="on" required />
                    <span class="modal-form-span"> Album Naam </span>
                </label>
                <label class="modal-form-label">
                    <input type="number" min="0" class="modal-form-input" id="albumb-form-alb-nr" name="album-nummer" placeholder="" value="<?=$store['Album_Nummer']?>" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Nummer </span>
                </label>
                <label class="modal-form-label">
                    <input type="date" class="modal-form-input" id="albumb-form-alb-date" name="album-datum" placeholder=""  value="<?=$store['Album_UitgDatum']?>" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Datum </span>
                </label>

            <?php if( !empty( $store['Album_Cover'] ) ) : ?>
                <div class="modal-album-cover" id="albumb-cover">
                    <img class="modal-album-cover-img" id="albumb-cover-img" src="<?=$store['Album_Cover']?>" alt='album-cover'/>
                </div>
                <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                    Nieuwe Cover Selecteren
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                </label>
            <?php else : ?>
                <div class="modal-album-cover" id="albumb-cover"> Geen cover gevonden, u kunt een cover selecteren, maar dit is niet verplicht. </div>
                <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                    Album Cover Selecteren
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                </label>
            <?php endif; ?>

                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumb-form-alb-isbn" name="album-isbn" placeholder="" value="<?=$store['Album_ISBN']?>" autocomplete="on" required />
                    <span class="modal-form-span"> Album ISBN </span>
                </label>
                <!-- <label class="modal-form-label"> -->
                    <input class="modal-form-input" id="albumb-form-alb-opm" name="album-opm" placeholder="" value="<?=$store['Album_Opm']?>" autocomplete="on" hidden />
                    <!-- <span class="modal-form-span" hidden> Album Opmerking </span> -->
                <!-- </label> -->
                <input class="modal-form-button" id="albumb-form-button" type="submit" value="Bevestigen" />
            </form>
            
            <?php else : ?>
                <form class="modal-form" id="albumb-form" enctype="multipart/form-data" method="post" action="/albumBew">
                <input type="text" class="modal-form-indexS" id="albumb-form-indexS" name="serie-index" value="" hidden />
                <input type="text" class="modal-form-indexA" id="albumb-form-indexA" name="album-index" value="" hidden />

                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="albumb-form-alb-naam" name="album-naam" placeholder="" value="" autocomplete="on" required />
                    <span class="modal-form-span"> Album Naam </span>
                </label>

                <label class="modal-form-label">
                    <input type="number" min="0" class="modal-form-input" id="albumb-form-alb-nr" name="album-nummer" placeholder="" value="" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Nummer </span>
                </label>

                <label class="modal-form-label">
                    <input type="date" class="modal-form-input" id="albumb-form-alb-date" name="album-datum" placeholder=""  value="" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Datum </span>
                </label>

                <div class="modal-album-cover" id="albumb-cover"> Geen cover gevonden, u kunt een cover selecteren, maar dit is niet verplicht. </div>
                <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                    Album Cover Selecteren
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                </label>

                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumb-form-alb-isbn" name="album-isbn" placeholder="" value="<?=$store['Album_ISBN']?>" autocomplete="on" required />
                    <span class="modal-form-span"> Album ISBN </span>
                </label>
                <!-- <label class="modal-form-label"> -->
                    <input class="modal-form-input" id="albumb-form-alb-opm" name="album-opm" placeholder="" value="<?=$store['Album_Opm']?>" autocomplete="on" hidden />
                    <!-- <span class="modal-form-span" hidden> Album Opmerking </span> -->
                <!-- </label> -->
                <input class="modal-form-button" id="albumb-form-button" type="submit" value="Bevestigen" />
            </form>
        <?php
            unset( $_SESSION['page-data']['album-edit'] );
            endif;
        ?>
        </div>

    </div>
</div>
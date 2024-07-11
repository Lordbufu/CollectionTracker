<div id="albumb-pop-in" class="modal-container">
    <div class="modal-content-container">

        <div class="modal-header">  
            <h1 class="modal-header-text"> Album Bewerken </h1>
            <form class="modal-header-close-form" method="post" action="/beheer">
                <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>

        <div class="modal-body">
            <form class="modal-form" id="albumb-form" enctype="multipart/form-data" method="post" action="/albumBew">
                
            <?php
                if( isset( $_SESSION['page-data']['album-edit'] ) ) :
                    foreach( $_SESSION['page-data']['albums'] as $index => $album ) {
                        if( $album['Album_Index'] == $_SESSION['page-data']['album-edit'] ) {
                            $store = $_SESSION['page-data']['albums'][$index];
                        }
                    }
            ?>

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

                <div class="modal-album-cover" id="albumB-cover">
                    <img class="modal-album-cover-img" id="albumb-cover-img" src="<?=$store['Album_Cover']?>" alt='album-cover'/>
                </div>

                <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                    Nieuwe Cover Selecteren
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                </label>

                <?php else : ?>

                <div class="modal-album-cover" id="albumB-cover"> </div>

                <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                    Album Cover Selecteren
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                </label>

                <?php endif; ?>

                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumb-form-alb-isbn" name="album-isbn" placeholder="" value="<?=$store['Album_ISBN']?>" autocomplete="on" required />
                    <span class="modal-form-span"> Album ISBN </span>
                </label>

                <button class="modal-form-isbnSearch" id="modal-form-isbnSearch" type="submit" formmethod="post" formaction="/isbn"> Zoek Isbn </button>

                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumb-form-alb-opm" name="album-opm" placeholder="" value="<?=$store['Album_Opm']?>" autocomplete="on" />
                    <span class="modal-form-span" hidden> Album Opmerking </span>
                </label>
                
                <input class="modal-form-button" id="albumb-form-button" type="submit" value="Bevestigen" />

            <!-- The same pop-in, but now populated via de isbn search option -->
            <?php elseif( isset( $_SESSION['page-data']['isbn-search'] ) ) :
                    $result = $_SESSION['page-data']['isbn-search']; ?>

                <input type="text" class="modal-form-indexS" id="albumb-form-indexS" name="serie-index" value="<?= $result["serie-index"] ?>" hidden />
                <input type="text" class="modal-form-indexA" id="albumb-form-indexA" name="album-index" value="<?= $result["album-index"] ?>" hidden />

                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="albumb-form-alb-naam" name="album-naam" placeholder="" value="<?= $result["album-naam"] ?>" autocomplete="on" required />
                    <span class="modal-form-span"> Album Naam </span>
                </label>

                <label class="modal-form-label">
                    <input type="number" min="0" class="modal-form-input" id="albumb-form-alb-nr" name="album-nummer" placeholder="" value="<?= $result["album-nummer"] ?>" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Nummer </span>
                </label>

                <label class="modal-form-label">
                    <input type="date" class="modal-form-input" id="albumb-form-alb-date" name="album-datum" placeholder=""  value="<?= $result["album-uitgDatum"] ?>" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Datum </span>
                </label>

                <?php if( !empty( $result["album-cover"] ) ) : ?>

                <div class="modal-album-cover" id="albumB-cover">
                    <img class="modal-album-cover-img" id="albumb-cover-img" src="<?= $result["album-cover"] ?>" alt='album-cover'/>
                </div>

                <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                    Nieuwe Cover Selecteren
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                </label>

                <?php else : ?>
                <div class="modal-album-cover" id="albumB-cover"> </div>

                <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                    Album Cover Selecteren
                    <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                </label>

                <?php endif; ?>

                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumb-form-alb-isbn" name="album-isbn" placeholder="" value="<?= $result["album-isbn"] ?>" autocomplete="on" required />
                    <span class="modal-form-span"> Album ISBN </span>
                </label>

                <button class="modal-form-isbnSearch" id="modal-form-isbnSearch" type="submit" formmethod="post" formaction="/isbn" hidden> Zoek Isbn </button>

                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumb-form-alb-opm" name="album-opm" placeholder="" value="<?= $result["album-opm"] ?>" autocomplete="on" />
                    <span class="modal-form-span" hidden> Album Opmerking </span>
                </label>

                <input class="modal-form-button" id="albumb-form-button" type="submit" value="Bevestigen" />
            <?php endif; ?>

            </form>
        </div>
    </div>
</div>
<div id="isbn-preview" class="modal-cont" >

    <div class="modal-content-cont" id="isbn-review-content-cont" >

        <div class="modal-header-cont" id="isbn-review-header-cont" >

            <h3 class="modal-header-text" id="isbn-review-header-text" > ISBN Search Review </h3>

            <form class="modal-header-close-form" id="isbn-review-header-close-form" method="post" action="/beheer" >
                <input class="modal-header-input" id="isbn-review-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" id="isbn-review-header-close" type="submit" value="&times;" />
            </form>

        </div>

        <?php // Store the titles,
            if( isset( $_SESSION["page-data"]["show-titles"] ) ) {
                $store = $_SESSION["page-data"]["show-titles"];
                unset( $_SESSION["page-data"]["show-titles"] );
            }
        ?>

        <div class="modal-body" id="isbn-review-body" >
            <form class="modal-form" id="isbn-review-form" enctype="multipart/form-data" method="post" action="/isbn" >

                <input class="modal-form-isbn" id="isbn-review-isbn" name="isbn-choice" value="<?= $store[1] ?>" hidden />
                <input class="modal-form-serie-index" id="isbn-review-serieInd" name="serie-index" value="<?= $store[2] ?>" hidden />

                <select class="modal-form-select" id="isbn-review-select" name="title-choice" id="album-toev" required>
                    <option class="modal-form-title-options" id="isbn-review-title-options" value="" > Selecteer een title </option>

                    <?php
                        foreach( $store as $key => $value ) :
                            // Ignore non-relevant data
                            if( $key != 0  && $key != 1 && $key != 2 ) :
                    ?>
                    <option class="modal-form-title-options" id="isbn-review-title-options-<?= $key ?>"> <?= $value ?> </option>

                    <?php
                            endif;
                        endforeach;
                    ?>
                </select>

                <!-- A button container, where the submit button and other submit related elements go (confirm checkboxes, etc). -->
                <div class="butt-box" id="butt-box" >
                    <input class="modal-form-button button" id="isbn-review-submit-butt" type="submit" value="Bevestigen" />
                </div>

            </form>
            
        </div>

    </div>

</div>
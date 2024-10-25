        <div id="albumS-pop-in" class="modal-cont">
            
            <div class="modal-content-cont" id="modal-content-cont" >

                <div class="modal-header-cont" id="modal-header-cont" >

                    <h3 class="modal-header-text" id="modal-header-text" > Barcode Scannen </h3>

                    <form class="modal-header-close-form" id="modal-header-close-form" method="post" action="/gebruik" >
                        <input class="modal-header-input" id="modal-header-input" name="close-pop-in" value="" hidden/>
                        <input class="modal-header-close" id="modal-header-close" type="submit" value="&times;" />
                    </form>

                </div>

                <div class="modal-body" id="modal-body" >

                    <div class="modal-scanner-pop-in" id="modal-scanner-pop-in">

                        <div id="reader-container">
                            <div id="reader"> </div>
                        </div>

                    </div>

                    <form class="modal-form" id="modal-form-gebr-scan" action="/userIsbn" method="post" hidden >
                    <?php if( isset( $_SESSION["page-data"]["serie-index"] ) ) : ?>
                        <input type="text" class="modal-form-indexS" id="albumS-form-indexS" name="serie-index" value="<?= $_SESSION["page-data"]["serie-index"] ?>" hidden />
                    <?php else : ?>
                        <input type="text" class="modal-form-indexS" id="albumS-form-indexS" name="serie-index" value="" hidden />
                    <?php endif; ?>
                        <input type="text" class="modal-form-indexS" id="albumSc-form-isbn" name="album-isbn" hidden />
                    </form>

                </div>

            </div>

        </div>
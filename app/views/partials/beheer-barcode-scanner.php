<div id="albumS-pop-in" class="modal-container">
    <div class="modal-content-container">

        <div class="modal-header">  
            <h1 class="modal-header-text"> Barcode Scannen </h1>
            <form class="modal-header-close-form" method="post" action="/beheer" >
                <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>

        <div class="modal-body">

            <div class="modal-scanner-pop-in" id="modal-scanner-pop-in">
                <div id="reader-container">
                    <div id="reader"> </div>
                </div>
            </div>

            <form class="modal-form" id="modal-form-scan" action="/isbn" method="post" hidden >
                <?php if( isset( $_SESSION["page-data"]["serie-index"] ) ) : ?>
                <input type="text" class="modal-form-indexS" id="albumS-form-indexS" name="serie-index" value="<?= $_SESSION["page-data"]["serie-index"] ?>" hidden />
                <?php unset( $_SESSION["page-data"]["serie-index"] ); else : ?>
                <input type="text" class="modal-form-indexS" id="albumS-form-indexS" name="serie-index" value="" hidden />
                <?php endif; ?>
                <input type="text" class="modal-form-indexS" id="albumS-form-isbn" name="album-isbn" hidden />
            </form>

        </div>

    </div>

</div>
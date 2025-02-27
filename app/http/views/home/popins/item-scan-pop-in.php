<div id="item-scan-pop-in" class="modal-cont">
    <div class="modal-content-cont">
        <div class="modal-header-cont">
            <h3 class="modal-header-text">Barcode Scannen</h3>

        <?php if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'user') : ?>
            <form class="modal-header-close-form" method="post" action="/gebruik">
                <input class="modal-header-input" name="close" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>

        <?php elseif(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'admin') : ?>
            <form class="modal-header-close-form" method="post" action="/beheer">
                <input class="modal-header-input" name="close" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>
        <?php endif; ?>

        </div>

        <div class="modal-body">

            <div class="modal-scanner-pop-in">
                <div class="reader-container">
                    <div id="reader"></div>
                </div>
            </div>

            <form class="modal-form" id="scan-form" action="/bCodeScan" method="post" hidden>
                <input type="text" class="modal-form-indexS" name="reeks-index" value="<?=$_SESSION['_flash']['tags']['reeks-index'] ?? ''?>" hidden/>
                <input type="text" class="modal-form-indexS" id='item-isbn' name="item-isbn" value hidden/>
            </form>
        </div>
    </div>
</div>

<script>
    let itemIsbn = document.getElementById('item-isbn');
    let scanForm = document.getElementById('scan-form');
    let config = {
        fps: 10,
        //supportedScanTypes: [ Html5QrcodeScanType.SCAN_TYPE_CAMERA ]  // uncommented for testing purposes atm.
    };
    
    html5QrcodeScanner = new Html5QrcodeScanner('reader', config);
    html5QrcodeScanner.render(onScanSuccess, onScanError);

    /*  onScanSuccess(decodedText, decodedResult):
            Using the qrcode scanning API, i take the ISBN/EAN number of a barcode.
            And store that in the form, so the backend code can querry google, and then parse any usefull data.
     */
    function onScanSuccess(decodedText, decodedResult) {
        formatName = decodedResult['result']['format']['formatName'];
        itemIsbn.value = decodedText;
        html5QrcodeScanner.clear();
        return scanForm.submit();
    }

    /*  onScanError(errorMessage):
            For now, i simply log the errorMessage, because i dunno how to handle these atm.
     */
    function onScanError(errorMessage) {
        return console.log(errorMessage);
    }
</script>
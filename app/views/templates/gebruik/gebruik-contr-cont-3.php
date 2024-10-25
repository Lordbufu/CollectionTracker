<?php if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) : ?>
        <div class="contr-cont-3" id="contr-cont-3" >
<?php else : ?>
        <div class="contr-cont-3" id="contr-cont-3" style="display: none">
<?php endif; ?>

            <form class="album-scan-form" id="album-scan-form" action="/userScan" method="post">
                <label for="album-scan-subm" class="album-scan-lab" > Scan barcode: </label>
                <input class="album-scan-subm button" id="album-scan-subm" type="submit" value="Scan Barcode"/>
            </form>
            
        </div>
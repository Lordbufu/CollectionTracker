<div class="contr-cont" id="contr-cont">

    <div class="serie-sel-cont">
        <form class="serie-sel-form" id="serie-sel-form" action="/gebruik" method="post" >
            <label for="serie-sel" class="serie-sel-lab"> Serie Selecteren:</label>
            <select class="serie-sel" name="serie_naam" id="serie-sel" required>
                <option value="">Selecteer een serie</option>
              <?php if( isset( $_SESSION['page-data']['series'] ) ) {
                        foreach( $_SESSION['page-data']['series'] as $key => $value ) {
                            if( isset( $_SESSION['page-data']['huidige-serie'] ) ) {
                                if( $value['Serie_Naam'] === $_SESSION['page-data']['huidige-serie'] ) {
                                    echo "<option class='serie-sel-opt' selected>{$value['Serie_Naam']}</option>";
                                } else {
                                    echo "<option class='serie-sel-opt'>{$value['Serie_Naam']}</option>";   
                                }
                            } else {
                                echo "<option class='serie-sel-opt'>{$value['Serie_Naam']}</option>";
                            }
                        }
                    } ?>
            </select>
            <input class="serie-sel-subm" id="serie-sel-subm" type="submit" value="Selecteer"/>
        </form>
    </div>

    <div class="album-zoek-cont">
        <form class="album-zoek-form" id="album-zoek-form" onsubmit="event.preventDefault()">
            <label class="album-zoek-lab" for="album-zoek-inp"> Album Zoeken: </label>

            <!-- Zoek opties, zodat er specifiek gezocht kan worden -->
            <div class="search-opt-cont">
                <!-- Zoeken op naam -->
                <div class="search-opt-naam-cont">
                    <label class="search-opt-naam-lab" for="album-zoek-naam-inp"> Naam: </label> <br>
                    <label class="album-zoek-naam" id="album-zoek-naam" for="album-zoek-naam-inp">
                        <input class="album-zoek-naam-inp" id="album-zoek-naam-inp" type="checkbox" />
                        <span class="album-zoek-naam-slider"></span>
                    </label>
                </div>
                <!-- Zoeken op album nummer -->
                <div class="search-opt-albNr-cont">
                    <label class="search-opt-albNr-lab" for="album-zoek-nr-inp"> Album Nr: </label> <br>
                    <label class="album-zoek-nr" id="album-zoek-nr" for="album-zoek-nr-inp">
                        <input class="album-zoek-nr-inp" id="album-zoek-nr-inp" type="checkbox" />
                        <span class="album-zoek-nr-slider"></span>
                    </label>
                </div>
                <!-- Zoeken op album isbn -->
                <div class="search-opt-isbn-cont">
                    <label class="search-opt-isbn-lab" for="album-zoek-isbn-inp"> Album Isbn: </label> <br>
                    <label class="album-zoek-isbn" id="album-zoek-isbn" for="album-zoek-isbn-inp">
                        <input class="album-zoek-isbn-inp" id="album-zoek-isbn-inp" type="checkbox" />
                        <span class="album-zoek-isbn-slider"></span>
                    </label>
                </div>
            </div>

            <label class="modal-form-label" for="album-zoek-inp">
                <input class="modal-form-input" id="album-zoek-inp" type="text" placeholder=""/>
                <span class="modal-form-span" id="album-zoek-span"> Zoek naar albums.. </span>
            </label>
        </form>
    </div>

    <div class="album-scan-cont">
        <form class="album-scan-form" id="album-scan-form" action="/userScan" method="post">
            <label for="album-scan-subm" class="album-scan-lab" > Scan barcode met telefoon: </label>
            <input class="album-scan-subm" id="album-scan-subm" type="submit" value="Scan Barcode"/>
        </form>
    </div>

</div>
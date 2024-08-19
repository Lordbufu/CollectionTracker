<div class="contr-cont" id="contr-cont">

    <div class="serie-maken-cont" id="serie-maken-cont">
        <form class="serie-maken-form" id="serie-maken-form" method="post" action="/beheer">
            <label for="serie-maken-inp" class="serie-maken-lab"> Serie Maken: </label>
            <label class="modal-form-label">
                <input type="text" class="modal-form-input" id="serie-maken-inp" name="newSerName" placeholder="" autocomplete="on" required/>
                <span class="modal-form-span"> Serie Naam </span>
            </label>
            <input class="serie-maken-subm" id="serie-maken-subm" type="submit" value="Bevestigen" />
        </form>
    </div>

    <div class="album-toev-cont" id=album-toev-cont"">
        <form class="album-toev-form" id="album-toev-form" method="post" action="/beheer">
            <label for="album-toev" class="album-toev-lab"> Album Toevoegen: </label>
            <select class="album-toev" name="album-toev" id="album-toev" required>
                <option value=""> Selecteer een serie </option>
                <?php if(isset($_SESSION['page-data']['series'])):
                        foreach($_SESSION['page-data']['series'] as $key => $value): ?>
                    <option class='album-toev-opt' id='album-toev-opt'><?=$value['Serie_Naam']?></option>
                <?php endforeach; endif; ?>
            </select>
            <input class="album-toev-subm" id="album-toev-subm" type="submit" value="Invoeren" />
            <button class="album-isbn-search" id="album-isbn-search" type="submit" formmethod="post" formaction="/scan"> Scan Barcode (niet voor desktop) </button>
        </form>
    </div>
    
</div>
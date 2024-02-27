<div class="contr-cont">

    <div class="serie-sel-cont">
        <form class="serie-sel-form" id="serie-sel-form" action="/gebruik" method="post" >
            <label for="serie-sel" class="serie-sel-lab"> Serie Selecteren:</label>
            <select class="serie-sel" name="serie_naam" id="serie-sel" required>
                <option value="">Selecteer een serie</option>
                <?php
                    if(isset($_SESSION['page-data']['series'])):
                        $series = $_SESSION['page-data']['series'];
                        foreach($series as $key => $value):
                ?>
              <option class="serie-sel-opt" value="<?= $series[$key]['Serie_Naam'] ?>"> <?= $series[$key]['Serie_Naam'] ?> </option>
                <?php
                    endforeach;
                    endif;
                ?>
            </select>
            <input class="serie-sel-subm" id="serie-sel-subm" type="submit" value="Selecteer"/>
        </form>
    </div>

    <div class="album-zoek-cont">
        <form class="album-zoek-form" id="album-zoek-form" onsubmit="event.preventDefault()">
            <label class="album-zoek-lab" for="album-zoek-inp"> Album Zoeken: </label>
            <label class="modal-form-label" for="album-zoek-inp">
                <input class="modal-form-input" id="album-zoek-inp" type="text" placeholder=""/>
                <span class="modal-form-span"> Zoek naar albums.. </span>
            </label>
        </form>
    </div>

</div>
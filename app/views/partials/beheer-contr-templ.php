<!-- Controle Container -->
<div class="contr-cont">

    <!-- Serie Maken (Klaar) -->
    <div class="serie-maken-cont">
        <form class="serie-maken-form" id="serie-maken-form" >
            <label for="serie-maken-inp" class="serie-maken-lab"> Serie Maken: </label>
            <input type="text" class="serie-maken-inp" id="serie-maken-inp" name="serieNaam" />
            <input class="serie-maken-subm" id="serie-maken-subm" type="submit" value="Bevestigen" onclick="serieSelSubmit(event)" />
        </form>
    </div>

    <!-- Album Toevoegen (Klaar) -->
    <div class="album-toev-cont">
        <form class="album-toev-form" id="album-toev-form" >
            <label for="album-toev" class="album-toev-lab"> Album Toevoegen: </label>
            <select class="album-toev" name="album-toev" id="album-toev" required>
                <option value=""> Selecteer een serie </option>
                <?php if(isset($data["series"]))
                          $series = $data["series"];
                          if(isset($series))
                              foreach($series as $key => $value): ?>
                <option class="album-toev-opt" id="album-toev-opt" value="<?= $series[$key]['Serie_Naam'] ?>"> <?= $series[$key]['Serie_Naam'] ?> </option>
                <?php endforeach; ?>
            </select>
            <input class="album-toev-subm" id="album-toev-subm" type="button" value="Invoeren" onclick="albumToevInv()" />
        </form>
    </div>
</div>
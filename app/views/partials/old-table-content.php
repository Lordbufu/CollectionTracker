        <!-- Serie Bewerken (Klaar) -->
        <div class="serie-bewerk-cont">
          <form class="serie-bewerk-form" id="serie-bewerk-form" method="post" action="/beheer" >
            <label for="serie-bewerk" class="serie-bewerk-lab"> Serie Bewerken: </label>
            <select class="serie-bewerk" name="serie-bewerken" id="serie-bewerk" required>
              <option value=""> Selecteer een serie </option>
              <?php if(isset($data["series"]))
                      $series = $data["series"];
                      if(isset($series))
                        foreach($series as $key => $value): ?>
              <option class="serie-bewerk-opt" id="serie-bewerk-opt" value="<?= $series[$key]['Serie_Naam'] ?>"> <?= $series[$key]['Serie_Naam'] ?> </option>
              <?php endforeach; ?>
            </select>
            <input class="serie-bewerk-subm" id="serie-bewerk-subm" type="submit" value="Bewerken" onclick="return serieBewSubmit()" />
          </form>
        </div>

        <!-- Album Verwijderen / Bewerken (Klaar) -->
        <div class="album-verw-cont">
          <form class="album-verw-form" id="album-verw-form" method="post">
            <label class="album-verw-lab" for="album-verw-serie">Albums Bewerken:</label>

            <select class="album-verw-serie" name="album-verw-serie" id="album-verw-serie" required>
              <option value="">Selecteer een serie</option>
              <?php
                    if(isset($data["series"]))
                      $series = $data["series"];
                      if(isset($series))
                        foreach($series as $key => $value):
              ?>
              <option class="album-serie-opt" id="album-serie-opt" value="<?= $series[$key]['Serie_Naam'] ?>"><?= $series[$key]['Serie_Naam'] ?></option>
              <?php endforeach; ?>
            </select>

            <select class="album-verw-album" name="album-verw-album" id="album-verw-album" required>
              <option value="">Selecteer een album</option>
              <?php
                    if(isset($data["albums"][0]))
                      $albums = $data["albums"][0];
                      if(isset($albums))
                        foreach($albums as $key => $value):
              ?>
              <option class="album-verw-opt" id="album-verw-opt" value="<?= $albums[$key]['Album_Naam'] ?>"><?= $albums[$key]['Album_Naam'] ?></option>
              <?php endforeach; ?>
            </select>

            <input class="album-bew-subm" id="album-bew-subm" value="Bewerken" formaction="/beheer" type="submit" onclick="return albumBewerken()" />
            <input class="album-verw-subm" id="album-verw-subm" value="Verwijderen" formaction="/albumV" type="submit" onclick="return albumVerwijderen()" />

          </form>
        </div>
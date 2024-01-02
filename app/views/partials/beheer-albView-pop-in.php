    <!-- Weergave Container -->
    <div id="beheer-albView-pop-in" class="beheer-weerg-cont" style="display: none">
        <div id="beheer-albView-content-container" class="beheer-weerg-repl-cont">

            <!-- Weergave Title -->
            <h1 id="beheer-albView-text" class="beheer-weerg-header-text"> Test Title </h1>

            <!-- Body Container -->
            <div id="beheer-albView-body" class="beheer-album-weerg-modal-body">

                <!-- Albums Tafel -->
                <table id="beheer-albView-tafel" class="beheer-albums-tafel">

                    <!-- Tafel Titles -->
                    <tr id="beheer-albView-titles" class="beheer-albums-tafel-titles">

                        <th style="border: 0px;"> </th>
                        <th style="border: 0px;"> </th>
                        <th> Album Naam </th>
                        <th> Album Nummer </th>
                        <th> Album Uitgave Datum </th>
                        <th> Album Cover </th>
                        <th> Album ISBN </th>
                        <th> Album Opmerking </th>
                            
                    </tr>

                    <!-- Tafel Inhoud -->
                    <?php
                        if(isset($data['albums']))
                            if(isset($albums))
                                foreach($albums as $key => $value):
                                    echo "<tr class='album-bewerken-inhoud-{$albums[$key]['Album_Index']}' id='album-bewerken-inhoud-{$albums[$key]['Album_Index']}'>";
                    ?>

                        <!-- Album Bewerken Button -->
                        <th id="album-bewerken" class="album-bewerken">
                            <button class="album-bewerken-butt" id="<?= $albums[$key]['Album_Index']; ?>" type="button" onclick="albumBewerken(event)"></button>
                        </th>

                        <!-- Album Verwijderen Button  -->
                        <th id="album-verwijderen" class="album-verwijderen">
                            <button class="album-verwijderen-butt" id="<?= $albums[$key]['Album_Index']; ?>" type="button" onclick="albumVerwijderen(event)"></button>
                        </th>

                        <!-- Album Naam -->
                        <th id="album-naam" class="album-naam"><?=$albums[$key]['Album_Naam']?></th>

                        <!-- Album Nummer -->
                        <th id="album-nummer" class="album-nummer"><?=$albums[$key]['Album_Nummer']?></th>

                        <!-- Album Uitgave Datum -->
                        <th id="album-uitgave" class="album-uitgave"><?=$albums[$key]['Album_UitgDatum']?></th>

                        <!-- Album Cover -->
                        <th id="album-cover" class="album-cover"><?php
                                if(isset($albums[$key]['Album_Cover'])) {
                                    echo "<img id='album-cover-img' class='album-cover-img' src='{$albums[$key]['Album_Cover']}' alt='album-cover'/>";
                                }
                            ?></th>

                        <!-- Album ISBN -->
                        <th id="album-isbn" class="album-isbn"><?=$albums[$key]['Album_ISBN']?></th>

                        <!-- Album Opmerking -->
                        <th id="album-opm" class="album-opm"><?=$albums[$key]['Album_Opm']?></th>

                    <?php endforeach; ?>

                    </tr>
                </table>
            </div>
        </div>
    </div>
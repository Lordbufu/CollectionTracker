<div id="beheer-albView-pop-in" class="beheer-weerg-cont" style="display: none">
    <div id="beheer-albView-content-container" class="beheer-weerg-repl-cont">
        <h2 id="beheer-albView-text" class="beheer-weerg-header"> Test Title </h1>
        <div id="beheer-albView-body" class="beheer-album-weerg-modal-body">
            <table id="beheer-albView-tafel" class="beheer-albums-tafel">
                <tr id="beheer-albView-titles" class="beheer-albums-tafel-titles">
                    <th style="border: 0px;"></th>
                    <th style="border: 0px;"></th>
                    <th>Album Naam</th>
                    <th>Album Nummer</th>
                    <th>Album Uitgave Datum</th>
                    <th>Album Cover</th>
                    <th>Album ISBN</th>
                    <th>Album Opmerking</th>
                </tr>

                <?php
                    if(isset($_SESSION['page-data']['albums'])):
                        $albums = $_SESSION['page-data']['albums'];

                        foreach($albums as $key => $value):
                            echo "<tr class='album-bewerken-inhoud-{$albums[$key]['Album_Index']}' id='album-bewerken-inhoud-{$albums[$key]['Album_Index']}'>";
                ?>

                    <th id="album-bewerken" class="album-bewerken button">
                        <button class="album-bewerken-butt" id="<?= $albums[$key]['Album_Index']; ?>" type="button" onclick="albumBewerken(event)"></button>
                    </th>
                    <th id="album-verwijderen" class="album-verwijderen button">
                        <button class="album-verwijderen-butt" id="<?= $albums[$key]['Album_Index']; ?>" type="button" onclick="albumVerwijderen(event)"></button>
                    </th>
                    <th id="album-naam" class="album-naam"><?=$albums[$key]['Album_Naam']?></th>
                    <th id="album-nummer" class="album-nummer"><?=$albums[$key]['Album_Nummer']?></th>
                    <th id="album-uitgave" class="album-uitgave"><?=$albums[$key]['Album_UitgDatum']?></th>
                    <th id="album-cover" class="album-cover">
                        <?php
                            if(isset($albums[$key]['Album_Cover'])) {
                                echo "<img id='album-cover-img' class='album-cover-img' src='{$albums[$key]['Album_Cover']}' alt='album-cover'/>";
                            }
                        ?>
                    </th>
                    <th id="album-isbn" class="album-isbn"><?=$albums[$key]['Album_ISBN']?></th>
                    <th id="album-opm" class="album-opm"><?=$albums[$key]['Album_Opm']?></th>

                <?php
                    endforeach;
                    endif;
                ?>
                </tr>
            </table>
        </div>
    </div>
</div>
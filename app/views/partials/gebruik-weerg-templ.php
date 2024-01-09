<div class="weergave-container">
    <h2 id="weergave-header"> Huidige Serie, met alle Albums: </h2>
    <table class="album-tafel">
        <tr class="album-tafel-titles">
            <th> Aanwezig </th>
            <th> Album Naam </th>
            <th> Uitgave Nr </th>
            <th> Album Cover </th>
            <th> Uitgave Datum </th>
            <th> ISBN </th>
            <th> Opmerking </th>
        </tr>

        <?php
            $count = 0;
            $aanw = false;

            if(!empty($data['albums']))
                foreach($albums as $key => $value):
                    $count++;
                    echo "<tr class='album-tafel-inhoud-{$count}' id='album-tafel-inhoud'>";
        ?>

            <th class="album-aanwezig">
                <form class="album-aanwezig-form" id="album-form" >
                    <input class="album-aanwezig-data" id="album-form-data1" name="albumNaam" value="" />
                    <input class="album-aanwezig-data" id="album-form-data2" name="aanwezig" value="" />
                    <input class="album-aanwezig-data" id="album-form-data3" name="gebr-email" value="" />
                    <input class="album-aanwezig-data" id="album-form-data4" name="serieNaam" value="" />
                    <?php
                        if(!empty($data['collecties'])) {
                            foreach($collecties as $iKey => $iValue) {
                                if($collecties[$iKey]['Alb_Index'] === $albums[$key]['Album_Index']) {
                                    $aanw = true;
                                }
                            }

                            if($aanw) {
                                echo "<input class='album-aanwezig-checkbox' id='{$count}' type='checkbox' checked style='display: none;'/>";
                                echo "<label for='{$count}' class='album-aanwezig-toggle'> </label>";
                            } else {
                                echo "<input class='album-aanwezig-checkbox' id='{$count}' type='checkbox' style='display: none;'/>";
                                echo "<label for='{$count}' class='album-aanwezig-toggle'> </label>";
                            }

                            $aanw = false;
                        } else if(!$aanw) {
                            echo "<input class='album-aanwezig-checkbox' id='{$count}' type='checkbox' style='display: none;'/>";
                            echo "<label for='{$count}' class='album-aanwezig-toggle'> </label>";
                        }
                    ?>
                </form>
            </th>
            <th class="album-naam" id="albumNaam"><?= $albums[$key]['Album_Naam']; ?></th>
            <th class="album-uitgnr"><?= $albums[$key]['Album_Nummer']; ?></th>
            <th class="album-cover">
            <?php
                if($albums[$key]['Album_Cover'] == "") {
                    echo "Geen";
                } else {
                    echo '<img id="alb-cov" src="'.$albums[$key]['Album_Cover'].'">';
                }
            ?>
            </th>
            <th class="album-uitgdt"><?= $albums[$key]['Album_UitgDatum']; ?></th>
            <th class="album-isbn"><?= $albums[$key]['Album_ISBN']; ?></th>
            <th class="album-opm"><?= $albums[$key]['Album_Opm']; ?></th>
            <?php endforeach; ?>
        </tr>
    </table>
</div>
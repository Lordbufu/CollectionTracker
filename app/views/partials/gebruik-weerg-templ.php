<div class="gebr-weerg-cont">
    <?php
        if(isset($_SESSION['page-data']['huidige-serie'])) {
            echo "<h2 id='weergave-header'> {$_SESSION['page-data']['huidige-serie']}, met alle Albums: </h2>";
        } else {
            echo "<h2 id='weergave-header'> Huidige Serie, met alle Albums: </h2>";
        }
    ?>
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

            if(isset($_SESSION['page-data']['albums'])):
                $albums = $_SESSION['page-data']['albums'];
                foreach($albums as $key => $value):
                    $count++;
                    echo "<tr class='album-tafel-inhoud-{$count}' id='album-tafel-inhoud'>";
        ?>

            <th class="album-aanwezig">
                <form class="album-aanwezig-form" id="album-form">
                    <?php
                        if(isset($_SESSION['page-data']['collections'])) {
                            $collecties = $_SESSION['page-data']['collections'];

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
            <?php
                endforeach;
                endif;
            ?>
        </tr>
    </table>
</div>
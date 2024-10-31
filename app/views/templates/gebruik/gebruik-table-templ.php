        <table class="album-tafel" id="album-tafel">

            <tr class="album-tafel-titles" id="album-tafel-titles">
                <th> Aanwezig </th>
                <th> Album Naam </th>
                <th> Uitgave Nr </th>
                <th class="albumUitTitle"> Uitgave Datum </th>
                <th class="albumSchrijver"> Album Schrijver </th>
                <th class="albumCovTitle"> Cover </th>
                <th> ISBN </th>
                <th class="albumOpmTitle"> Opmerking </th>
            </tr>

        <?php
        if( isset( $_SESSION["page-data"]["albums"] ) ) {
            $albums = $_SESSION["page-data"]["albums"];
        }

        if( isset($albums) ) :
            foreach( $albums as $key => $value) :
                $aanw = FALSE;
        ?>

        <tr class="album-tafel-inhoud-<?= $value["Album_Index"] ?>" id="album-tafel-inhoud">

            <th class="album-aanwezig" id="album-aanwezig">

                <form class="album-aanwezig-form" id="album-form" method="post" action="/albSta">

                    <input class="album-aanw-form-index" id="album-aanw-form-index" name="albumIndex" value="<?= $value["Album_Index"] ?>" hidden />
                    <input class="album-aanw-form-naam" id="album-aanw-form-naam" name="albumNaam" value="<?= $value["Album_Naam"] ?>" hidden />
                    <?php
                        if( isset( $_SESSION["page-data"]["collections"] ) ) {
                            $coll = $_SESSION["page-data"]["collections"];
                            foreach( $coll as $iKey => $iValue ) {
                                if( $iValue["Alb_Index"] === $value["Album_Index"] ) {
                                    $aanw = TRUE;
                                }
                            }
                        }

                        if( $aanw ) :
                    ?>

                    <input class="album-aanwezig-ch-state" id="album-aanwezig-ch-state" name="checkState" value="true" hidden />

                    <label for="<?= $value["Album_Index"] ?>" class="album-aanwezig-toggle">
                        <input class="album-aanwezig-checkbox" id="<?= $value["Album_Index"] ?>" type="checkbox" checked style="display: none;" />
                        <span class="album-aanwezig-slider"> </span>
                    </label>

                    <?php else : ?>

                    <input class="album-aanwezig-ch-state" id="album-aanwezig-ch-state" name="checkState" value="false" hidden />

                    <label for="<?= $value["Album_Index"] ?>" class="album-aanwezig-toggle">
                        <input class="album-aanwezig-checkbox" id="<?= $value["Album_Index"] ?>" type="checkbox" style="display: none;" />
                        <span class="album-aanwezig-slider"> </span>
                    </label>

                    <?php endif; ?>

                </form>
            </th>

            <th class="album-naam" id="<?= $value["Album_Index"] ?>" > <?= $value["Album_Naam"]; ?> </th>
            <th class="album-uitgnr" id="album-uitgnr" > <?= $value["Album_Nummer"]; ?> </th>
            <th class="album-uitgdt" id="album-uitgdt" > <?= $value['Album_UitgDatum']; ?> </th>
            <?php if( isset( $value["Album_Schijver"] ) ) : ?>
            <th id="album-schr" class="album-schr"> <?= $value["Album_Schijver"] ?> </th>
            <?php else : ?>
            <th id="album-schr" class="album-schr"> Geen </th>
            <?php endif; ?>
            <th class="album-cover" id="album-cover" >
            <?php if( $value["Album_Cover"] == "" ) : ?>
                Geen
            <?php else: ?>
                <img id="album-cover-img" class="album-cover-img" src="<?= $value["Album_Cover"] ?>" >
            <?php endif; ?>
            </th>
            <th class="album-isbn" id="album-isbn" > <?= $value['Album_ISBN']; ?> </th>
            <th class="album-opm" id="album-opm" > <?= $value['Album_Opm']; ?> </th>
        <?php
            endforeach;
        endif;
        ?>
        </tr>
    </table>
</div>
<div class="gebr-weerg-cont" id="gebr-weerg-cont">
<?php if(isset($_SESSION['page-data']['huidige-serie'])): ?>
    <h2 class="weergave-header" id="weergave-header"> <?=$_SESSION['page-data']['huidige-serie']?>, met alle Albums: </h2>
<?php else: ?>
    <h2 class="weergave-header" id="weergave-header"> Huidige Serie, met alle Albums: </h2>
<?php endif; ?>

    <table class="album-tafel" id="album-tafel">
        <tr class="album-tafel-titles" id="album-tafel-titles">
            <th> Aanwezig </th>
            <th> Album Naam </th>
            <th> Uitgave Nr </th>
            <th> Album Cover </th>
            <th> Uitgave Datum </th>
            <th> ISBN </th>
            <th> Opmerking </th>
        </tr>

<?php
    if( isset( $_SESSION['page-data']['albums'] ) ) {
        $albums = $_SESSION['page-data']['albums'];
    }

    if( isset($albums) ) :
        foreach( $albums as $key => $value) :
            $aanw = FALSE;
?>
        <!-- Display all Albums for the Serie -->
        <tr class="album-tafel-inhoud-<?=$value['Album_Index']?>" id="album-tafel-inhoud">
            <th class="album-aanwezig" id="album-aanwezig">
                <form class="album-aanwezig-form" id="album-form" method="post" action="/albSta">
                    <!-- Add Album hidden form data, to change the collection state -->
                    <input class="album-aanw-form-index" id="album-aanw-form-index" name="albumIndex" value="<?=$value['Album_Index']?>" hidden />
                    <input class="album-aanw-form-naam" id="album-aanw-form-naam" name="albumNaam" value="<?=$value['Album_Naam']?>" hidden />
                    <!-- Display the correct collection state -->
                <?php
                    if( isset( $_SESSION['page-data']['collections'] ) ) {
                        $coll = $_SESSION['page-data']['collections'];

                        foreach( $coll as $iKey => $iValue ) {
                            if( $iValue['Alb_Index'] === $value['Album_Index'] ) {
                                $aanw = TRUE;
                            }
                        }
                    }
                if( $aanw ) : ?>
                        <input class='album-aanwezig-ch-state' id='album-aanwezig-ch-state' name='checkState' value='true' hidden />
                        <input class='album-aanwezig-checkbox' id='<?=$value['Album_Index']?>' type='checkbox' checked style='display: none;' />
                        <label for='<?=$value['Album_Index']?>' class='album-aanwezig-toggle'> </label>
                <?php else : ?>
                        <input class='album-aanwezig-ch-state' id='album-aanwezig-ch-state' name='checkState' value='false' hidden />
                        <input class='album-aanwezig-checkbox' id='<?=$value['Album_Index']?>' type='checkbox' style='display: none;' />
                        <label for='<?=$value['Album_Index']?>' class='album-aanwezig-toggle'> </label>
                <?php endif; ?>
                </form>
            </th>
            <th class="album-naam" id="albumNaam"><?= $value['Album_Naam']; ?></th>
            <th class="album-uitgnr" id="album-uitgnr">
                <?=$value['Album_Nummer'];?>
            </th>

            <th class="album-cover" id="album-cover">
            <?php if($value['Album_Cover'] == ""): ?>
                <?="Geen"?>
            <?php else: ?>
                <img id="album-cover-img" class="album-cover-img" src="<?=$value['Album_Cover']?>">
            <?php endif; ?>
            </th>

            <th class="album-uitgdt" id="album-uitgdt">
                <?=$value['Album_UitgDatum'];?>
            </th>

            <th class="album-isbn" id="album-isbn">
                <?=$value['Album_ISBN'];?>
            </th>

            <th class="album-opm" id="album-opm">
                <?=$value['Album_Opm'];?>
            </th>
<?php endforeach; endif; ?>
        </tr>
    </table>
</div>
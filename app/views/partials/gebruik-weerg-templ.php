<div class="gebr-weerg-cont">
<?php if(isset($_SESSION['page-data']['huidige-serie'])): ?>
    <h2 id='weergave-header'> <?=$_SESSION['page-data']['huidige-serie']?>, met alle Albums: </h2>
<?php else: ?>
    <h2 id='weergave-header'> Huidige Serie, met alle Albums: </h2>
<?php endif; ?>

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
    if(isset($_SESSION['page-data']['albums'])):
        $count = 0;
        
        foreach($_SESSION['page-data']['albums'] as $key => $value):
            $count++;
?>

        <tr class='album-tafel-inhoud-<?=$count?>' id='album-tafel-inhoud'>
            <th class="album-aanwezig">
                <form class="album-aanwezig-form" id="album-form">
            <?php
                if(isset($_SESSION['page-data']['collections'])):
                    foreach($_SESSION['page-data']['collections'] as $iKey => $iValue):
                        if($iValue['Alb_Index'] === $value['Album_Index']):
            ?>

                    <input class='album-aanwezig-checkbox' id='<?=$count?>' type='checkbox' checked style='display: none;'/>
                    <label for='<?=$count?>' class='album-aanwezig-toggle'> </label>

                        <?php else: ?>
                            
                    <input class='album-aanwezig-checkbox' id='<?=$count?>' type='checkbox' style='display: none;'/>
                    <label for='<?=$count?>' class='album-aanwezig-toggle'> </label>

            <?php
                        endif;
                    endforeach;
            ?>
            <?php else: ?>
                    <input class='album-aanwezig-checkbox' id='<?=$count?>' type='checkbox' style='display: none;'/>
                    <label for='<?=$count?>' class='album-aanwezig-toggle'> </label>
            <?php endif; ?>
                </form>
            </th>
            <th class="album-naam" id="albumNaam"><?= $value['Album_Naam']; ?></th>
            <th class="album-uitgnr"><?= $value['Album_Nummer']; ?></th>
            <th class="album-cover">
            <?php if($value['Album_Cover'] == ""): ?>
                <?="Geen"?>
            <?php else: ?>
                <img id='album-cover-img' class='album-cover-img' src='<?=$value['Album_Cover']?>'>
            <?php endif; ?>
            </th>
            <th class="album-uitgdt"><?= $value['Album_UitgDatum']; ?></th>
            <th class="album-isbn"><?= $value['Album_ISBN']; ?></th>
            <th class="album-opm"><?= $value['Album_Opm']; ?></th>
<?php
        endforeach;
    endif;
?>
        </tr>
    </table>
</div>
<div id="beheer-albView-content-container" class="beheer-weerg-cont">
    
<?php if(isset($_SESSION['page-data']['huidige-serie'])): ?>
    <h2 id='beheer-albView-text' class='beheer-weerg-header'><?=$_SESSION['page-data']['huidige-serie']?></h2>
<?php else: ?>
    <h2 id='beheer-albView-text' class='beheer-weerg-header'> Test Title </h2>
<?php endif; ?>

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
        foreach($_SESSION['page-data']['albums'] as $key => $value):
?>

            <tr class='album-bewerken-inhoud-<?=$value['Album_Index']?>' id='album-bewerken-inhoud-<?=$value['Album_Index']?>'>
                <th id="album-bewerken" class="album-bewerken button">
                    <button class="album-bewerken-butt" id="<?= $value['Album_Index']; ?>" type="button" onclick="albumBewerken(event)"></button>
                </th>
                <th id="album-verwijderen" class="album-verwijderen button">
                    <button class="album-verwijderen-butt" id="<?= $value['Album_Index']; ?>" type="button" onclick="albumVerwijderen(event)"></button>
                </th>
                <th id="album-naam" class="album-naam"><?=$value['Album_Naam']?></th>
                <th id="album-nummer" class="album-nummer"><?=$value['Album_Nummer']?></th>
                <th id="album-uitgave" class="album-uitgave"><?=$value['Album_UitgDatum']?></th>
                <th id="album-cover" class="album-cover">

<?php if(isset($value['Album_Cover'])): ?>
                    <img id='album-cover-img' class='album-cover-img' src='<?= $value['Album_Cover'] ?>' alt='album-cover'/>
<?php endif; ?>

                </th>
                <th id="album-isbn" class="album-isbn"><?=$value['Album_ISBN']?></th>
                <th id="album-opm" class="album-opm"><?=$value['Album_Opm']?></th>

<?php
        endforeach;
    endif;
?>
            </tr>
        </table>
    </div>
</div>
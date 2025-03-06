<?php // Store the correct data from the session.
if(isset($_SESSION['page-data']['huidige-reeks'])) {
    $hReeks = inpFilt($_SESSION['page-data']['huidige-reeks']);
}

if(isset($_SESSION['page-data']['items'])) {
    $store = $_SESSION['page-data']['items'];
}
?>

<div class="table-header">
    <h2 class="table-header-text"><?='Alle items in: ' . $hReeks ?? 'Selecteer een Reeks'?></h2>
</div>

<table class="items-table">
    <tr class="items-table-titles">
        <th style="border: 0px;"></th>
        <th style="border: 0px;"></th>
        <th>Naam</th>
        <th>Uitg-Nr</th>
        <th class="item-uitgTitle">Uitg-Datum</th>
        <th class="item-schrTitle">Schrijver</th>
        <th class="item-covTitle">Cover</th>
        <th>ISBN</th>
        <th class="item-opmTitle">Opmerking</th>
    </tr>

<?php   // Loop over all stored items.
foreach($store as $key => $value) : ?>

    <tr class="item-tafel-inhoud">
        <th class="item-bew">
            <form class="item-bewerken-form" method="POST" action="/iEdit">
                <input class="item-bew-method" name="_method" value="PATCH" hidden/>
                <input class="item-bew-inp" name="iIndex" value="<?=$value['Item_Index']?>" hidden/>
                <input class="item-bew-butt button" id="<?=$value['Item_Index']?>" type="submit" value=""/>
            </form>
        </th>

        <th class="item-verw">
            <form class="item-verw-form" method="POST" action="/iDel">
                <input class="item-verw-method" name="_method" value="DELETE" hidden/>
                <input class="item-verw-inp" name="iIndex" value="<?=$value['Item_Index']?>" hidden/>
                <input class="item-verw-inp" name="rIndex" value="<?=$value['Item_Reeks']?>" hidden/>
                <input class="item-verw-butt" id="<?=$value['Item_Index']?>" type="submit" value="" onclick="return itemVerwijderen(event)"/>
            </form>
        </th>

        <th class="item-naam"><?=inpFilt($value['Item_Naam'])?></th>
        <th class="item-nummer"><?=$value['Item_Nummer']?></th>
        <th class="item-uitgave"><?=$value['Item_Uitgd']?></th>
        <th class="item-schr"><?=isset($value['Item_Auth']) ? inpFilt($value['Item_Auth']) : 'Geen'?></th>
        <th class="item-cover">
            <?php if(isset($value['Item_Plaatje'])) : ?>
            <img id="item-cover-img" class="item-cover-img" src="<?=$value['Item_Plaatje']?>" alt="item-cover"/>
            <?php endif; ?>
        </th>
        <th class="item-isbn"><?=$value['Item_Isbn']?></th>
        <th class="item-opm"><?=inpFilt($value['Item_Opm'])?></th>

    </tr>
<?php
    endforeach; ?>
</table>

<script>
    const bewButt = document.getElementsByClassName('item-bew-butt');
    const bewButtArr = Array.from(bewButt);
    for(key in bewButtArr) {
        bewButtArr[key].addEventListener('click', saveScroll);
    }

    const verwButt = document.getElementsByClassName('item-verwijderen-butt');
    const verwButtArr = Array.from(verwButt);
    for(key in verwButtArr) {
        verwButtArr[key].addEventListener('click', saveScroll);
    }
</script>
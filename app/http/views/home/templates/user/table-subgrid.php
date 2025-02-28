<div class="table-header">
    <h2 class="gebruik-weerg-header"><?=inpFilt($_SESSION['page-data']['huidige-reeks']) ?? 'Selecteer een Reeks'?></h2>
</div>

<table class="item-table">

    <tr class="item-table-titles">
        <th>Aanwezig</th>
        <th>Item Naam</th>
        <th>Uitgave Nr</th>
        <th class="itemUitTitle">Uitgave Datum</th>
        <th class="itemSchrijver">Item Schrijver</th>
        <th class="itemCovTitle">Cover</th>
        <th>ISBN</th>
        <th class="itemOpmTitle">Opmerking</th>
    </tr>

<?php // Store the correct data from the session.
if(isset($_SESSION['page-data']['items'])) {
    $items = $_SESSION['page-data']['items'];
}

if(isset($_SESSION['page-data']['collecties'])) {
    $coll = $_SESSION['page-data']['collecties'];
}

if(isset($items)) : // Loop over all loaded item, and set collection status to false.
    foreach($items as $key => $value) :
        $aanw = false;
?>

        <tr class="item-table-content-<?=$value['Item_Index']?>" id="items-table-content">
            <th class="item-aanw">
<?php   // If item is in colllection, set state to true
        foreach($coll as $iKey => $iValue) {
            if($iValue['Item_Index'] === $value['Item_Index']) {
                $aanw = true;
            }
        }

        if(!$aanw) :    // Render the correct 'slider' based on the collection status.
?>
                <form class="item-aanw-form" method="post" action="/colAdd">
                    <input class="item-aanw-form-method" name="_method" value="PUT" hidden/>
                    <input class="item-aanw-form-index" name="index" value="<?=$value['Item_Index']?>" hidden/>
                    <label for="<?=$value['Item_Index']?>" class="item-aanw-toggle">
                        <input class="item-aanw-checkbox" id="<?=$value['Item_Index']?>" type="checkbox" style="display: none;"/>
                        <span class="item-aanw-slider"></span>
                    </label>
                </form>
<?php   else : ?>

                <form class="item-aanw-form" method="post" action="/colRem">
                    <input class="item-aanw-form-method" name="_method" value="DELETE" hidden/>
                    <input class="item-aanw-form-index" name="index" value="<?=$value['Item_Index']?>" hidden/>
                    <label for="<?=$value['Item_Index']?>" class="item-aanw-toggle">
                        <input class="item-aanw-checkbox" id="<?=$value['Item_Index']?>" type="checkbox" style="display: none;" checked/>
                        <span class="item-aanw-slider"></span>
                    </label>
                </form>
<?php   endif; ?>
            </th>

            <th class="item-naam" id="<?=$value['Item_Index']?>"><?=inpFilt($value['Item_Naam'])?></th>
            <th class="item-uitgnr"><?=$value['Item_Nummer']?></th>
            <th class="item-uitgdt"><?=$value['Item_Uitgd']?></th>
            <th class="item-schr"><?=inpFilt($value['Item_Auth']) ?? 'Geen'?></th>
            <th class="item-cover">
            <?php if($value['Item_Plaatje'] == "") : ?>
                Geen
            <?php else: ?>
                <img class="item-cover-img" src="<?=$value['Item_Plaatje']?>">
            <?php endif; ?>
            </th>
            <th class="item-isbn"><?=$value['Item_Isbn']?></th>
            <th class="item-opm"><?=inpFilt($value['Item_Opm'])?></th>
        </tr>
        <?php endforeach; endif; ?>
    </table>
</div>

<script>
    /* Create a trigger on the item-name column for each item, that opens the extra details pop-in on mobile devices only. */
    if(localDevice === 'mobile') {
        const nameEl = document.getElementsByClassName('item-naam');
        let tempEl = Array.from(nameEl);

        tempEl.forEach((item, index, arr) => {
            arr[index].addEventListener('click', viewDetails);
        });
    }
    
    /* Assing a listenEvent to all checkboxes on the page */
    const chBox = document.getElementsByClassName('item-aanw-checkbox');
    chBoxArr = Array.from(chBox);
    chBoxArr.forEach( (item, index, arr) => {
        arr[index].addEventListener('change', checkBox);
    });

    /*  checkBox(e): Checkbox listenEvent that simply submits the form. */
    function checkBox(e) {
        saveScroll(e);
        return e.target.closest('form').submit();
    }
</script>
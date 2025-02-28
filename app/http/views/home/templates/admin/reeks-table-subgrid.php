<div class="table-header">
    <h2 class="table-header-text"><?=inpFilt($_SESSION['page-data']['huidige-reeks']) ?? 'Selecteer een Reeks'?></h2>
</div>

<table class="reeks-table">

    <tr class="reeks-table-titles">
        <th style="border: 0px;"></th>
        <th style="border: 0px;"></th>
        <th style="border: 0px;"></th>
        <th>Reeks Naam</th>
        <th class="reeksAuthTitle">Reeks Makers</th>
        <th class="reeksOpmTitle">Reeks Opmerking</th>
        <th>Reeks Items</th>
    </tr>

<?php   // Loop over the reeks data is there is any
if(isset($_SESSION['page-data']['reeks'])) :
    foreach($_SESSION['page-data']['reeks'] as $key => $value) : ?>
    <tr class="reeks-table-cont-<?=$value['Reeks_Index']?>">
        <th class="reeks-view">
            <form class="reeks-view-form" method="post" action="/selReeks">
                <input class="reeks-view-form-index" name="index" value="<?=$value['Reeks_Index'];?>" hidden/>
                <input class="reeks-view-butt" type="submit" value=""/>
            </form>
        </th>

        <th class="reeks-bew">
            <form class="reeks-edit-form" method="post" action="/rEdit">
                <input class="reeks-edit-form-method" name="_method" value="PATCH" hidden/>
                <input class="reeks-edit-form-index" name="index" value="<?=$value['Reeks_Index']?>" hidden/>
                <input class="reeks-edit-butt" id="reeks-edit-butt" type="submit" value=""/>
            </form>
        </th>

        <th class="reeks-del">
            <form class="reeks-edit-form" method="post" action="/rDel">
                <input class="reeks-del-form-method" name="_method" value="DELETE" hidden/>
                <input class="reeks-del-form-index" name="index" value="<?=$value['Reeks_Index']?>" hidden/>
                <input class="reeks-del-form-naam" name="naam" value="<?=$value['Reeks_Naam']?>" hidden/>
                <input class="reeks-del-butt" id="<?=$value['Reeks_Index']?>" value="" onclick="return reeksVerwijderen(event)"/>
            </form>
        </th>

        <th class="reeks-naam"><?=inpFilt($value['Reeks_Naam'])?></th>
        <th class="reeks-maker"><?=inpFilt($value['Reeks_Maker'])?></th>
        <th class="reeks-opmerk"><?=inpFilt($value['Reeks_Opmerk'])?></th>
        <th class="reeks-items"><?=$value['Item_Aantal']?></th>
    </tr>
<?php
    endforeach;
endif; ?>
</table>

<script>
    /* Elements, button states and listen events for editing a serie */
    const serieBewButt = document.getElementsByClassName('reeks-edit-butt');
    const serieBewButtArr = Array.from(serieBewButt);
    for(key in serieBewButtArr) {
        serieBewButtArr[key].addEventListener('click', saveScroll);
    }

    /* Elements and listen events for removing series */
    const serieVerButt = document.getElementsByClassName('reeks-del-butt');
    const serieVerButtArr = Array.from(serieVerButt);
    for(key in serieVerButtArr) {
        serieVerButtArr[key].addEventListener('click', saveScroll);
    }

    /*  serieVerwijderen(e:
        A simple confirmation check, that displays the serie name, and triggers the submit button base on said confirmation.
            rowCol  - The table row in witch the button was pressed.
            rowArr  - The table row in array format for easier access.
            conf    - The confirmation box when the button is pressed.

        Return Value: Boolean.
     */
    function reeksVerwijderen(e) {        
        const rowCol = document.getElementsByClassName('reeks-table-cont-' + e.target.id );
        const rowArr = Array.from(rowCol);
        const conf = confirm('Weet u zeker dat de Serie: ' + rowArr[0].children[3].innerHTML + '\n Haar albums en alle collectie data wilt verwijderen ?');

        if(conf) {
            return e.target.closest('form').submit();
        } else {
            if(sessionStorage.scrollPos) {
                sessionStorage.removeItem('scrollPos');
            }
            return false;
        }
    }
</script>
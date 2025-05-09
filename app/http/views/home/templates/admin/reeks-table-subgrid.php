<?php // Ensure the correct data is set, depending on the session _flash content.
if(isset($_SESSION['page-data']['huidige-reeks'])) { $hReek = inpFilt($_SESSION['page-data']['huidige-reeks']); }
if(isset($_SESSION['page-data']['reeks'])) { $reeks = $_SESSION['page-data']['reeks']; } ?>
<div class="table-header"> <h2 class="table-header-text"><?=$hReeks ?? 'Selecteer een Reeks'?></h2> </div>
<table class="reeks-table">
    <tr class="reeks-table-titles">
        <th style="border: 0px;"></th>
        <th style="border: 0px;"></th>
        <th style="border: 0px;"></th>
        <th>Reeks Naam</th>
        <th>Reeks Plaatje</th>
        <th class="reeksAuthTitle">Reeks Makers</th>
        <th class="reeksOpmTitle">Reeks Opmerking</th>
        <th>Reeks Items</th>
    </tr>
<?php if($reeks) :
    foreach($reeks as $key => $value) : ?>
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
        <th class="reeks-cover">
            <?php if(!empty($value['Reeks_Plaatje'])) : ?>
            <img id="reeks-cover-img" class="reeks-cover-img" src="<?=$value['Reeks_Plaatje']?>" alt="reeks-cover"/>
            <?php endif; ?>
        </th>
        <th class="reeks-maker"><?=inpFilt($value['Reeks_Maker'])?></th>
        <th class="reeks-opmerk"><?=inpFilt($value['Reeks_Opmerk'])?></th>
        <th class="reeks-items"><?=$value['Item_Aantal']?></th>
    </tr>
<?php endforeach; endif; ?>
</table>
<script>
    /* Elements, button states and listen events for editing a serie */
    const serieBewButt = document.getElementsByClassName('reeks-edit-butt'), serieBewButtArr = Array.from(serieBewButt);
    for(key in serieBewButtArr) { serieBewButtArr[key].addEventListener('click', saveScroll); }
    /* Elements and listen events for removing series */
    const serieVerButt = document.getElementsByClassName('reeks-del-butt'), serieVerButtArr = Array.from(serieVerButt);
    for(key in serieVerButtArr) { serieVerButtArr[key].addEventListener('click', saveScroll); }
    /* serieVerwijderen(e): A simple confirmation check, that displays the serie name, and triggers the submit button base on said confirmation. */
    function reeksVerwijderen(e) {        
        const rowCol = document.getElementsByClassName('reeks-table-cont-' + e.target.id ), rowArr = Array.from(rowCol), conf = confirm('Weet u zeker dat de Serie: ' + rowArr[0].children[3].innerHTML + '\n Haar albums en alle collectie data wilt verwijderen ?');
        if(conf) { return e.target.closest('form').submit(); } else { if(sessionStorage.scrollPos) { sessionStorage.removeItem('scrollPos'); } return false; }
    }
</script>
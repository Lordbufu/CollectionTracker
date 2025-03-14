<?php   // Store the correct data from the session.
if(isset($_SESSION['page-data']['reeks'])) { $store = $_SESSION['page-data']['reeks']; }
if(isset($_SESSION['page-data']['huidige-reeks'])) { $hReeks = inpFilt($_SESSION['page-data']['huidige-reeks']); } ?>

<div class="contr-cont-1">
    <form class="reeks-sel-form" action="/selReeks" method="post" >
        <label for="reeks-sel" class="reeks-sel-lab">Reeks Selecteren:</label>
        <select class="reeks-sel" name="naam" id="reeks-sel" required>
            <option class="reeks-sel-opt" value="">Selecteer een reeks</option>        
<?php   // Loop over all items and see if it matches a current selection if that was already made.
    if(isset($store)) :
        foreach($store as $key => $value) :
            if(isset($hReeks)) :
                if(inpFilt($value['Reeks_Naam']) === $hReeks) :
?>
            <option class="reeks-sel-opt" selected><?=inpFilt($value['Reeks_Naam'])?></option>
<?php           else : ?>
            <option class="reeks-sel-opt"><?=inpFilt($value['Reeks_Naam'])?></option>
<?php           endif;
            else : ?>
            <option class="reeks-sel-opt"><?=inpFilt($value['Reeks_Naam'])?></option>
<?php       endif;
        endforeach;
    endif; ?>
        </select>
        <input class="reeks-sel-subm button" id="reeks-sel-subm" type="submit" value="Selecteer"/>
    </form>

    <script>
        const formButt = document.getElementById('reeks-sel-subm'), formInput = document.getElementById('reeks-sel');
        formInput.addEventListener('change', selectEvent),  formButt.disabled = true;
        /* selectEvent(e): Enable or Disable form submit button, based on the formInput value from the reeks selecteren dropdown. */
        function selectEvent(e) { if(formInput.value === "") { return formButt.disabled = true; } else { return formButt.disabled = false; } }
    </script>
</div>

<div class="contr-cont-2">
    <?php require __DIR__ . '/../item-search-cont.php'; ?>
</div>

<?php if(isset($hReeks)) : ?>
<div class="contr-cont-3">
    <form class="item-scan-form" action="/scanPop" method="post">
        <label for="item-scan-subm" class="item-scan-lab">Scan barcode:</label>
        <input class="item-scan-reeks-naam" name="naam" value="<?=$hReeks?>" hidden/>
        <input class="item-scan-subm button" id="item-scan-subm" type="submit" value="Scan Barcode"/>
    </form>
</div>
<script>
    /* Elements and listen events for the isbn search function */
    document.getElementById('item-scan-subm').addEventListener('click', saveScroll);
</script>
<?php endif; ?>
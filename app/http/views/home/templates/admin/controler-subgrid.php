<div class="contr-cont-1" id="contr-cont-1" >
    <form class="contr-reeks-form" method="post" action="/reeksPop">
        <label for="reeks-maken-inp" class="contr-reeks-lab">Reeks Maken:</label>
        <label class="contr-reeks-lab-inp">
            <input class="contr-reeks-inp" id="reeks-maken-inp" type="text" name="naam" placeholder="" autocomplete="on" required/>
            <span class="contr-reeks-span">Reeks Naam</span>
        </label>
        <input class="contr-reeks-butt button" id="reeks-pop-req" type="submit" value="Bevestigen"/>
    </form>
</div>

<script>
    const reeksPopReq = document.getElementById('reeks-pop-req');
    reeksPopReq.addEventListener('click', saveScroll);
</script>

<div class="contr-cont-2">
    <form class="contr-item-form"method="post" action="/itemsPop">
        <label for="item-toev" class="contr-item-lab">Item Toevoegen:</label>

        <select class="contr-item-select" id="item-toev" name="naam" required>
            <option value="">Selecteer een reeks</option>
        <?php
            if(isset($_SESSION['page-data']['reeks'])) {
                $store = $_SESSION['page-data']['reeks'];
            }

            if(isset($_SESSION['page-data']['huidige-reeks'])) {
                $hReeks = $_SESSION['page-data']['huidige-reeks'];
            }

            $current = FALSE;

            if(isset($store)) :
                foreach($store as $key => $value) :
                    if(isset($hReeks)) {
                        if($hReeks === $value['Reeks_Naam']) {
                            $current = TRUE;
                        }
                    } ?>
                <option class="item-toev-opt" <?= $current ? 'selected' : '' ?>><?=$value['Reeks_Naam']?></option>
        <?php endforeach; endif; ?>
        </select>

        <input class="contr-item-subm button" id="item-toev-subm" type="submit" value="Invoeren" />
        <button class="contr-item-isbn-search button" id="item-isbn-scan" type="submit" formmethod="post" formaction="/scanPop">Scan Barcode</button>
    </form>
</div>

<script>
    /* Add saveScroll to the submit buttons. */
    const isbnButt = document.getElementById('item-isbn-scan');
    isbnButt.addEventListener('click', saveScroll);
    
    const itemToevSubm = document.getElementById('item-toev-subm');
    itemToevSubm.addEventListener('click', saveScroll);
</script>

<?php if(isset($_SESSION['page-data']['huidige-reeks'])) : ?>
<div class="contr-cont-3">
    <?php require __DIR__ . '/../item-search-cont.php'; ?>
</div>
<?php endif; ?>
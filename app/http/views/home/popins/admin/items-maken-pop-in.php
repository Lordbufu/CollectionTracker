<?php if(isset($_SESSION['_flash']['oldForm'])) { $store = $_SESSION['_flash']['oldForm']; }
    if(isset($_SESSION['_flash']['oldItem'])) { $store = $_SESSION['_flash']['oldItem']; }
    if(isset($_SESSION['_flash']['newItem'])) { $store = $_SESSION['_flash']['newItem']; } ?>

<div id="items-maken-pop-in" class="modal-cont" >
    <div class="modal-content-cont">
        <div class="modal-header-cont">
            <h3 class="modal-header-text">Item Toevoegen</h3>

            <form class="modal-header-close-form" method="post" action="/beheer">
                <input class="modal-header-input" name="close" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>
        </div>

        <div class="modal-body">
            <div class="modal-form-left-cont" id="modal-form-left-cont">
                <form class="modal-form" enctype="multipart/form-data" method="post" action="/itemsM">
                    <input class="modal-form-hidden" name="_method" value="<?=$store['method'] ?? ''?>" hidden/>
                    <input class="modal-form-hidden" name="rIndex" value="<?=$store['rIndex'] ?? ''?>" hidden/>
                    <input class="modal-form-hidden" name="iIndex" value="<?=$store['iIndex'] ?? ''?>" hidden/>
                    <p id="modal-small-text" class="modal-small-text" >De itemn naam & isbn zijn verplichte velden</p>

                    <label class="modal-form-label">
                        <input type="text" class="modal-form-input" id="item-maken-naam" name="naam" value="<?=isset($store['naam']) ? inpFilt($store['naam']) : ''?>" placeholder="" autocomplete="on" required/>
                        <span class="modal-form-span">Item Naam</span>
                    </label>

                    <label class="modal-form-label">
                        <input type="number" min="0" class="modal-form-input" name="nummer" value="<?=isset($store['nummer']) ? $store['nummer'] : ''?>" placeholder="" autocomplete="on"/>
                        <span class="modal-form-span">Item Nummer</span>
                    </label>

                    <label class="modal-form-label">
                        <input type="date" class="modal-form-input" name="datum" value="<?=isset($store['datum']) ? $store['datum'] : ''?>" placeholder="" autocomplete="on"/>
                        <span class="modal-form-span">Item Uitg-datum</span>
                    </label>

                    <label class="modal-form-label">
                        <input type="text" class="modal-form-input" id="autheurs" name="autheur" value="<?=isset($store['autheur']) ? inpFilt($store['autheur']) : ''?>" placeholder="" autocomplete="on"/>
                        <span class="modal-form-span">Item Autheur</span>
                    </label>

                    <div class="modal-item-cover" id="modal-item-cover">
                        <?php if(!empty($store['cover'])) : ?>
                        <img class="modal-item-cover-img" src="<?=$store['cover']?>">
                        <?php endif; ?>
                    </div>

                    <label class="modal-form-cov-lab button" id="modal-form-cov-lab">
                        <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="item-cover-inp" name="cover" />
                        <?php if(!empty($store['cover'])) : ?>
                            Nieuwe Cover Selecteren
                        <?php else : ?>
                            Selecteer een Item Cover
                        <?php endif; ?>
                    </label>

                    <label class="modal-form-label">
                        <input class="modal-form-input" id="item-maken-isbn" name="isbn" value="<?=isset($store['isbn']) ? $store['isbn'] : '0'?>" placeholder="" autocomplete="on" required/>
                        <span class="modal-form-span">Item ISBN</span>
                    </label>

                    <label class="modal-form-label">
                        <input class="modal-form-input" id="itemOpm" name="opmerking" value="<?=isset($store['opmerking']) ? inpFilt($store['opmerking']) : ''?>" placeholder="" autocomplete="on"/>
                        <span class="modal-form-span">Item Opmerking</span>
                    </label>

                    <div class="butt-box" id="butt-box">
                        <input class="modal-form-button button" id="item-maken-submit" type="submit" value="Bevestigen"/>
                    </div>
            </div>

            <div class="modal-form-right-cont">
                    <div class="modal-form-fake-trigger"></div>
                    <div class="modal-form-fake-trigger"></div>
                    <div class="modal-form-fake-trigger"></div>
                    <div class="modal-form-fake-trigger"></div>
                    <?php if(!empty($store['cover'])) : ?>
                    <div class="modal-form-cov-trigger" id="modal-form-item-cov-trigger"></div>
                    <?php else : ?>
                    <div class="modal-form-cov-trigger" id="modal-form-item-cov-trigger" hidden></div>
                    <?php endif; ?>
                    <div class="modal-form-fake-trigger"></div>
                    <button class="modal-form-isbn-trigger" id="item-isbn-search-butt" formaction="/iIsbnS" method="post" type="submit"></button>
                    <div class="modal-form-fake-trigger"></div>
                    <div class="modal-form-fake-trigger"></div>
            </div>
                </form>
        </div>
    </div>
</div>

<script>
    /* Item name element and its listen event. */
    const itemNaamInp = document.getElementById('item-maken-naam'); itemNaamInp.addEventListener('input', naamCheck);
    /* Item cover element and its listen event. */
    const itemCoverInp = document.getElementById('item-cover-inp'); itemCoverInp.addEventListener('change', coverInpCheck);
    /* Isbn element and its listen event. */
    const itemIsbnInp = document.getElementById('item-maken-isbn'); itemIsbnInp.addEventListener('input', isbnCheck);   
    /* Form submit button element, listen event and its initial state. */
    const itemMakenSubm = document.getElementById('item-maken-submit'); itemMakenSubm.addEventListener('click', saveScroll), itemMakenSubm.disabled = true;
    /* Isbn search button element and its listen event. */
    const itemSearch = document.getElementById('item-isbn-search-butt'); itemSearch.addEventListener('click', saveScroll);
    /* Initial name and isbn check state. */
    let naamChecked = false, isbnChecked = false;

    /* (Optional) Extra visual validation, incase more indication is required considering the form validation i used in PhP. */
    const itemAutheur = document.getElementById('autheurs'); itemAutheur.addEventListener('change', validateInput);
    const itemOpm = document.getElementById('itemOpm'); itemOpm.addEventListener('change', validateInput);

    /* albCovCheck(e): This function simply checks the files size, and is triggered with the on-change coverInpCheck. */
    function albCovCheck(e) { const file = e.target.files; if(file[0].size > 4096000) { displayMessage('Bestand is te groot, graag iets van 4MB of kleiner.'); e.target.value = ''; return false; } return true; }
    /* coverInpCheck(e): The Event function for the cover input, to change the preview and text in related pop-ins. */
    function coverInpCheck(e) { const imgEl = document.createElement('img'), imageFile = e.target.files[0], check = albCovCheck(e); let labEl = '', triggerEl = ''; if(check) { imgEl.src = URL.createObjectURL(imageFile), imgEl.className = 'modal-item-cover-img', imgEl.id = 'item-cover-img'; if(e.target.id === 'item-cover-inp') { let divCov = document.getElementById('modal-item-cover'); divCov.innerHTML = '', divCov.appendChild(imgEl), labEl = document.getElementById('modal-form-cov-lab'), triggerEl = document.getElementById('modal-form-item-cov-trigger'); if(triggerEl.hidden) { triggerEl.hidden = false; } } labEl.innerHTML = 'Nieuwe Cover Selecteren', labEl.appendChild(e.target); return; } }
    /* Elements and events required for the isbn search function. */ 
    const searchSubm = document.getElementById('item-isbn-search-butt'); searchSubm.addEventListener('click', submitIsbnSearch);
    /* submitIsbnSearch(e): This simple removes the required tag from the name inputs so i can submit only the ISBN that needs to be searched. */
    function submitIsbnSearch(e) { itemNaamInp.removeAttribute('required'); }
</script>

<style>
    .modal-form-left-cont, .modal-form-right-cont { display: inline-grid; }
    .modal-body { grid-template-columns: 85% 15%; }
</style>
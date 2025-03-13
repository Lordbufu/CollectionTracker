<?php   // Ensure the correct data is set, depending on the session _flash content.
    if(isset($_SESSION['_flash']['oldForm'])) { $store = $_SESSION['_flash']['oldForm']; }
    if(isset($_SESSION['_flash']['oldItem'])) { $store = $_SESSION['_flash']['oldItem']; }
    if(isset($_SESSION['_flash']['newItem'])) { $store = $_SESSION['_flash']['newItem']; }
    
    if(isset($_SESSION['_flash']['tags']['rIndex']) && !isset($store['rIndex'])) {
        $store['rIndex'] = $_SESSION['_flash']['tags']['rIndex'];
    }

    if(isset($_SESSION['_flash']['tags']['method']) && !isset($store['method'])) {
        $store['method'] = $_SESSION['_flash']['tags']['method'];
    } ?>

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
                    <p id="modal-small-text" class="modal-small-text" >De album naam & isbn zijn verplichte velden</p>

                    <label class="modal-form-label">
                        <input type="text" class="modal-form-input" id="item-naam-inp" name="naam" value="<?=isset($store['naam']) ? inpFilt($store['naam']) : ''?>" placeholder="" autocomplete="on" required/>
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
                        <input type="text" class="modal-form-input" name="autheur" value="<?=isset($store['autheur']) ? inpFilt($store['autheur']) : ''?>" placeholder="" autocomplete="on"/>
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
                        <input class="modal-form-input" id="item-isbn-inp" name="isbn" value="<?=isset($store['isbn']) ? $store['isbn'] : '0'?>" placeholder="" autocomplete="on" required/>
                        <span class="modal-form-span">Item ISBN</span>
                    </label>

                    <label class="modal-form-label">
                        <input class="modal-form-input" name="opmerking" value="<?=isset($store['opmerking']) ? inpFilt($store['opmerking']) : ''?>" placeholder="" autocomplete="on"/>
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

<style>
    .modal-form-left-cont, .modal-form-right-cont {
        display: inline-grid;
    }

    .modal-body {
        grid-template-columns: 85% 15%;
    }
</style>

<script>
    /* Elements, button states and listen events for creating a album */
    const itemNaamInp = document.getElementById('item-naam-inp');
    const itemIsbnInp = document.getElementById('item-isbn-inp');
    const itemCoverInp = document.getElementById('item-cover-inp');
    const itemMakenSubm = document.getElementById('item-maken-submit');
    const itemSearch = document.getElementById('item-isbn-search-butt');

    itemIsbnInp.addEventListener('input', isbnCheck);
    itemNaamInp.addEventListener('input', naamCheck);
    itemCoverInp.addEventListener('change', coverInpCheck);
    itemMakenSubm.addEventListener('click', saveScroll);
    itemSearch.addEventListener('click', saveScroll);
    itemMakenSubm.disabled = true;

    /*  albCovCheck(e):
            This function simply checks the files size, and is triggered with the on-change coverInpCheck.
                e       - The submit button listen event object, passed on via the covInpCheck.
                file    - The file that has been selected by the user.
            
            Return Value: Boolean.
     */
    function albCovCheck(e) {
        const file = e.target.files;
        if(file[0].size > 4096000) {
            displayMessage('Bestand is te groot, graag iets van 4MB of kleiner.'), e.target.value = '';
            return false;
        }
        return true;
    }
        
    /*  coverInpCheck(e):
            The Event function for the cover input, to change the preview and text in related pop-ins.
            It also checks if the Image file is not larger then 4MB, using the albCovCheck.
                divCov      - The div container that should include the preview image.
                imageFile   - The uploaded file its temp location (in blob format).
                imgEl       - The new image element for the cover preview.
                labEl       - The label element from the cover input.
            
            Return Value: None.
     */
    function coverInpCheck(e) {
        const imgEl = document.createElement('img');
        const imageFile = e.target.files[0];
        const check = albCovCheck(e);
        let labEl = '';
        let triggerEl = '';

        if(check) {
            imgEl.src = URL.createObjectURL(imageFile);
            imgEl.className = 'modal-item-cover-img';
            imgEl.id = 'item-cover-img';

            if(e.target.id === 'item-cover-inp') {
                let divCov = document.getElementById('modal-item-cover');
                divCov.innerHTML = '';
                divCov.appendChild(imgEl);
                labEl = document.getElementById('modal-form-cov-lab');
                triggerEl = document.getElementById('modal-form-item-cov-trigger');

                if(triggerEl.hidden) {
                    triggerEl.hidden = false;
                }
            }

            labEl.innerHTML = 'Nieuwe Cover Selecteren';
            return labEl.appendChild(e.target);
        }
    }

    /* Elements and events required for the isbn search function. */ 
    const searchSubm = document.getElementById('item-isbn-search-butt');
    searchSubm.addEventListener('click', submitIsbnSearch);

    /*  submitIsbnSearch(e):
        This simple removes the required tag from the name inputs so i can submit only the ISBN that needs to be searched.
     */
    function submitIsbnSearch(e) {
        itemNaamInp.removeAttribute('required');
    }
</script>
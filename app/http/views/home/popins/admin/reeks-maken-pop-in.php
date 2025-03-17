<?php // Ensure the correct data is set, depending on the session _flash content.
if(isset($_SESSION['_flash']['oldForm'])) { $store = $_SESSION['_flash']['oldForm']; }
if(isset($_SESSION['_flash']['oldItem'])) { $store = $_SESSION['_flash']['oldItem']; }
if(isset($_SESSION['_flash']['oldItem'])) { $store = $_SESSION['_flash']['newReeks']; } ?>

<div id="reeks-maken-pop-in" class="modal-cont" >
    <div class="modal-content-cont">
        <div class="modal-header-cont">
            <h3 class="modal-header-text">Reeks Maken</h3>
            <form class="modal-header-close-form" method="post" action="/beheer">
                <input class="modal-header-input" name="close" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>
        </div>
        <div class="modal-body">
            <div class="modal-form-left-cont">
                <form class="modal-form" enctype="multipart/form-data" method="post" action="/reeksM">
                    <p id="modal-small-text" class="modal-small-text" >De reeks naam is een verplicht veld.</p>
                    <input class="modal-form-hidden-method" name="_method" value="<?=$store['method'] ?? ''?>" hidden/>
                    <input class="modal-form-hidden-index" name="index" value="<?=$store['index'] ?? ''?>" hidden/>
                    <label class="modal-form-label">
                        <input type="text" class="modal-form-input" id="reeks-maken-naam" name="naam" placeholder="" autocomplete="on" required value="<?=isset($store['naam']) ? inpFilt($store['naam']) : ''?>"/>
                        <span class="modal-form-span">Reeks Naam</span>
                    </label>
                    <label class="modal-form-label">
                        <input type="text" class="modal-form-input" name="makers" placeholder="" autocomplete="on" value="<?=isset($store['makers']) ? inpFilt($store['makers']) : ''?>"/>
                        <span class="modal-form-span">Makers/Artiesten</span>
                    </label>
                    <label class="modal-form-label">
                        <input type="text" class="modal-form-input" name="opmerking" placeholder="" autocomplete="on" value="<?=isset($store['opmerking']) ? inpFilt($store['opmerking']) : ''?>"/>
                        <span class="modal-form-span">Opmerking/Notitie</span>
                    </label>
                    <div class="modal-reeks-cover" id="modal-reeks-cover">
                        <?php if(!empty($store['cover'])) : ?>
                        <img class="modal-reeks-cover-img" src="<?=$store['cover']?>">
                        <?php endif; ?>
                    </div>
                    <label class="modal-form-cov-lab button" id="modal-form-cov-lab">
                        <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="reeks-cover-inp" name="plaatje" />
                        <?php if(!empty($store['cover'])) : ?>
                            Nieuwe Cover Selecteren
                        <?php else : ?>
                            Selecteer een Item Cover
                        <?php endif; ?>
                    </label>
                    <div class="butt-box">
                        <input class="modal-form-button button" id="reeks-maken-submit" type="submit" value="Bevestigen">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const reeksMakenInput = document.getElementById('reeks-maken-naam'), reeksMakenSubmit = document.getElementById('reeks-maken-submit'), reeksCoverInp = document.getElementById('reeks-cover-inp');
    reeksMakenInput.addEventListener('input', naamCheck), reeksMakenSubmit.addEventListener('click', saveScroll), reeksCoverInp.addEventListener('change', coverInpCheck), reeksMakenSubmit.disabled = true;
    /* albCovCheck(e): This function simply checks the files size, and is triggered with the on-change coverInpCheck. */
    function albCovCheck(e) { const file = e.target.files; if(file[0].size > 4096000) { displayMessage('Bestand is te groot, graag iets van 4MB of kleiner.'), e.target.value = ''; return false; } return true; }
    /* coverInpCheck(e): The Event function for the cover input, to change the preview and text in related pop-ins. */
    function coverInpCheck(e) { const imgEl = document.createElement('img'), imageFile = e.target.files[0], check = albCovCheck(e); let labEl = '', triggerEl = ''; if(check) { imgEl.src = URL.createObjectURL(imageFile), imgEl.className = 'modal-reeks-cover-img', imgEl.id = 'reeks-cover-img'; if(e.target.id === 'reeks-cover-inp') { let divCov = document.getElementById('modal-reeks-cover'); divCov.innerHTML = '', divCov.appendChild(imgEl), labEl = document.getElementById('modal-form-cov-lab'), triggerEl = document.getElementById('modal-form-reeks-cov-trigger'); if(triggerEl.hidden) { triggerEl.hidden = false; } } return labEl.innerHTML = 'Nieuwe Cover Selecteren', labEl.appendChild(e.target); } }
</script>
<style>
    .modal-form-left-cont { display: inline-grid; }
    .modal-form { display: block; }
</style>
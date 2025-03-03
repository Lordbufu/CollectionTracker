<?php
if(isset($_SESSION['_flash']['tags']['rNaam'])) {
    $store['naam'] = $_SESSION['_flash']['tags']['rNaam'];
}

if(isset($_SESSION['_flash']['oldForm'])) {
    $store = $_SESSION['_flash']['oldForm'];
}

if(isset($_SESSION['_flash']['oldItem'])) {
    $store = $_SESSION['_flash']['oldItem'];
}

if(isset($_SESSION['_flash']['tags']['method'])) {
    $store['method'] = $_SESSION['_flash']['tags']['method'];
}
?>

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

                <form class="modal-form" method="post" action="/reeksM">
                    <p id="modal-small-text" class="modal-small-text" > De serie naam is een verplicht veld </p>
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

                    <div class="butt-box">
                        <input class="modal-form-button button" id="reeks-maken-submit" type="submit" value="Bevestigen">
                    </div>
                </form>

            </div>
        </div>
        
    </div>
</div>

<script>
    const reeksMakenInput = document.getElementById('reeks-maken-naam');
    const reeksMakenSubmit = document.getElementById('reeks-maken-submit');
    reeksMakenInput.addEventListener('input', naamCheck);
    reeksMakenSubmit.addEventListener('click', saveScroll);
    reeksMakenSubmit.disabled = true;
</script>

<style>
    .modal-form-left-cont {
        display: inline-grid;
    }

    .modal-form {
        display: block;
    }
</style>
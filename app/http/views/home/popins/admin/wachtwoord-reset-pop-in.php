<div id="ww-reset-pop-in" class="modal-cont" >
    <div class="modal-content-cont" id="modal-content-cont" >
        <div class="modal-header-cont" id="modal-header-cont" >
            <h3 class="modal-header-text" id="modal-header-text" >Wachtwoord Reset</h3>

            <form class="modal-header-close-form" method="post" action="/beheer">
                <input class="modal-header-input" name="close" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>
        </div>

        <div class="modal-body" id="modal-body" >
            <form class="modal-form" id="ww-reset-form" method="post" action="/aReset">
                <input type="text" class="modal-form-input" name="_method" placeholder="" value="PATCH" hidden>

                <label class="modal-form-label">
                    <input type="email" class="modal-form-input" id="emailField" name="email" placeholder="" autocomplete="on" required>
                    <span class="modal-form-span">E-mail</span>
                </label>

                <label class="modal-form-label">
                    <input type="password" class="modal-form-input" id="pwInp1" name="wachtwoord1" placeholder="" autocomplete="on" required>
                    <span class="modal-form-span">Nieuw Wachtwoord</span>
                </label>

                <label class="modal-form-label">
                    <input type="password" class="modal-form-input" id="pwInp2" name="wachtwoord2" placeholder="" autocomplete="on" required>
                    <span class="modal-form-span">Wachtwoord Bevestigen</span>
                </label>

                <p id="modal-small-text" class="modal-small-text" >Uw wachtwoord moet minimaal 7 tekens lang zijn, en 1 hoofdletter + getal & speciaal teken bevatten.</p>

                <div class="butt-box" id="butt-box">
                    <input class="modal-form-button button" id="reset-submit" type="submit" value="Bevestigen" onclick="return aResetBev(event)" >
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    let submButt= document.getElementById('reset-submit'); submButt.disabled = true;
    let pwInp1 = document.getElementById('pwInp1'); pwInp1.addEventListener('input', validateReset);
    let pwInp2 = document.getElementById('pwInp2'); pwInp2.addEventListener('input', validateReset);

    function validateReset(e) {
        if(e.target.value !== '' && valLength('regular', e.target.value.length)) {
            if(e.target.id === 'pwInp1') {
                e.target.style.outline = '3px solid green';
            }

            if(e.target.id === 'pwInp2') {
                e.target.style.outline = '3px solid green';
            }

            if(pwInp1.value === pwInp2.value) {
                submButt.disabled = false;
            }

            return;
        }

        if(!valLength('regular', e.target.value.length)) {
            e.target.style.outline = '3px solid red';
            submButt.disabled = true;
            return;
        }
    }
</script>
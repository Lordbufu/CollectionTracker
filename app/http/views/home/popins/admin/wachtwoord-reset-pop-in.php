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
                <label class="modal-form-label">
                    <input type="email" class="modal-form-input" id="emailField" name="email" placeholder="" autocomplete="on" required>
                    <span class="modal-form-span">E-mail</span>
                </label>
                <label class="modal-form-label">
                    <input type="password" class="modal-form-input" id="resetVeld1" name="wachtwoord1" placeholder="" autocomplete="on" required>
                    <span class="modal-form-span">Nieuw Wachtwoord</span>
                </label>
                <label class="modal-form-label">
                    <input type="password" class="modal-form-input" id="resetVeld2" name="wachtwoord2" placeholder="" autocomplete="on" required>
                    <span class="modal-form-span">Wachtwoord Bevestigen</span>
                </label>
                <div class="butt-box" id="butt-box">
                    <input class="modal-form-button button" id="reset-submit" type="submit" value="Bevestigen" onclick="return aResetBev(event)" >
                </div>
            </form>
        </div>
        
    </div>
</div>
<script>
    /* Elements and listen events for the user password reset */
    const resetVeld2 = document.getElementById('resetVeld2'); resetVeld2.addEventListener('input', pwChecker), pwSubButt = document.getElementById('reset-submit'), pwSubButt.disabled = true;
    /* pwChecker(e): For visual confirmation, that both password entered are equal, and allowing submit only if they are. */
    function pwChecker(e) { const resetVeld1 = document.getElementById('resetVeld1'); if(e.target.value === resetVeld1.value) { return e.target.style.outline = '3px solid green', pwSubButt.disabled = false; } else { return e.target.style.outline = '3px solid red', pwSubButt.disabled = true; } }
    /* aResetBev(e): This function asks for user confirmation, before submitting the Admin password reset form. */
    function aResetBev( e ) { const conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?"); if(conf) { return true; } else { return false; } }
</script>
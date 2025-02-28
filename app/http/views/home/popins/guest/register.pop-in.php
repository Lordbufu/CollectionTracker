<div id="account-maken-pop-in" class="modal-cont" >
    <div class="modal-content-cont">

        <div class="modal-header-cont">
            <h3 class="modal-header-text" >Account Aanmaken</h3>
            <form class="modal-header-close-form" method="post" action="/home">
                <input class="modal-header-input" name="return" value="back" hidden />
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>

        <div class="modal-body">
            <form class="modal-form" method="post" action="/register" >
                <input type="text" class="modal-form-input" name="_method" placeholder="" value="PUT" hidden>

                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" name="naam" placeholder="" autocomplete="on" value="<?= $_SESSION['_flash']['oldForm']['naam'] ?? '' ?>" required>
                    <span class="modal-form-span">Gebruikers Naam</span>
                </label>

                <label class="modal-form-label">
                    <input type="email" class="modal-form-input" name="email" placeholder="" autocomplete="on" value="<?= $_SESSION['_flash']['oldForm']['email'] ?? '' ?>" required>
                    <span class="modal-form-span">E-Mail</span>
                </label>

                <label class="modal-form-label">
                    <input type="password" class="modal-form-input" id="pwInp1" name="wachtwoord" autocomplete="on" placeholder="" required>
                    <span class="modal-form-span">Wachtwoord Invoeren</span>
                </label>

                <label class="modal-form-label">
                    <input type="password" class="modal-form-input" id="pwInp2" name="wachtwoord-bev" autocomplete="on" placeholder="" required>
                    <span class="modal-form-span">Wachtwoord Bevestigen</span>
                </label>

                <div class="butt-box">
                    <label class="modal-form-agree-text" for="chBox">
                        <input type="checkbox" class="modal-form-checkbox" id="chBox">
                        Ik ga akoord met de Overeenkomst
                    </label>

                    <a class="modal-form-link" href="#gebr-overeenkomst">Gebruikers Overeenkomst</a>
                    <input class="modal-form-button button" id="reg-submit" type="submit" value="Bevestigen">
                </div>

            </form>
        </div>
    </div>
</div>

<div id="gebr-overeenkomst" class="modal-cont">
    <div class="modal-content-cont">

        <div class="modal-header-cont">
            <h3 class="modal-header-text">Gebruikers Overeenkomst</h3>
            <a class="modal-header-close" href="#account-maken-pop-in">&times;</a>
        </div>

        <div class="modal-body">
            <p class="modal-para-1">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                Nulla quam velit, vulputate eu pharetra nec, mattis ac neque.
                Duis vulputate commodo lectus, ac blandit elit tincidunt id.
            </p>

            <ol class="modal-ord-list">
                <li class="modal-list-i1">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Nulla quam velit, vulputate eu pharetra nec, mattis ac neque.
                    Duis vulputate commodo lectus, ac blandit elit tincidunt id.
                </li>

                <li class="modal-list-i2">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Nulla quam velit, vulputate eu pharetra nec, mattis ac neque.
                    Duis vulputate commodo lectus, ac blandit elit tincidunt id.
                </li>

                <li class="modal-list-i3">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Nulla quam velit, vulputate eu pharetra nec, mattis ac neque.
                    Duis vulputate commodo lectus, ac blandit elit tincidunt id.
                </li>
            </ol>

            <div class="butt-box">
                <a class="modal-gebr-ov-button button" href="#account-maken-pop-in">Terug</a>
            </div>    
        </div>

    </div>
</div>

<script>
    /* Events required for account registration */
    const pwInp1 = document.getElementById('pwInp1');
    const pwInp2 = document.getElementById('pwInp2');
    const chBox = document.getElementById('chBox');
    const submButt = document.getElementById('reg-submit');
    pwInp1.addEventListener('input', pwCheck);
    pwInp2.addEventListener('input', pwCheck);
    chBox.addEventListener('change', checkBox);

    /* States required for account registration */
    submButt.disabled = true;
    let pwChecked = false;
    let inputChecked = false;

    /*  pwCheck(e): This function checks the users password input, and changes the button states and input style. */
    function pwCheck(e) {
        if(pwInp1.value === pwInp2.value) {
            pwChecked = true;
            pwInp1.style.outline = '3px solid green';
            pwInp2.style.outline = '3px solid green';
            if(inputChecked) {
                return submButt.disabled = false;
            }
        } else {
            pwChecked = false;
            pwInp1.style.outline = '3px solid red';
            pwInp2.style.outline = '3px solid red';
            return submButt.disabled = true;
        }
    }

    /*  checkBox(e): This function checks the users input on the user agreement checkbox, similar to the pwCheck function. */
    function checkBox(e) {
        if(e.target.checked) {
            inputChecked = e.target.checked;
            if(pwChecked) {
                return submButt.disabled = false;
            }
        } else {
            inputChecked = e.target.checked;
            return submButt.disabled = true;
        }
    }
</script>

<style>
    .modal-body {
        display: inline-flex;
    }

    .modal-form {
        display: block;
    }
</style>
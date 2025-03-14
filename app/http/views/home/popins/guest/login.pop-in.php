<div id="login-pop-in" class="modal-cont">
    <div class="modal-content-cont">
        <div class="modal-header-cont">
            <h3 class="modal-header-text">Account Login</h3>
            <form class="modal-header-close-form" method="post" action="/home">
                <input class="modal-header-input" name="return" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>
        </div>
        <div class="modal-body">
            <form class="modal-form" method="post">
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" name="accountCred" placeholder="" autocomplete="on" value="<?=$_SESSION['_flash']['oldForm']['accountCred'] ?? ''?>" required>
                    <span class="modal-form-span">E-Mail of Account Naam</span>
                </label>
                <label class="modal-form-label">
                    <input type="password" class="modal-form-input" name="wachtwoord" placeholder="" autocomplete="on" required>
                    <span class="modal-form-span">Wachtwoord</span>
                </label>
                <div class="butt-box">
                    <input class="modal-form-button button" type="submit" value="Login" formaction="/login">
                    <a class="modal-form-link" href="#ww-reset-pop-in" style="<?=isset($_SESSION['_flash']['tags']['log-fail']) ? '' : 'display:none'?>">Wachtwoord Reset</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="ww-reset-pop-in" class="modal-cont">
    <div class="modal-content-cont">
        <div class="modal-header-cont">
            <h3 class="modal-header-text">Wachtwoord Reset</h3>
            <a class="modal-header-close"href="#login-pop-in">&times;</a>
        </div>
        <div class="modal-body">
            <p class="modal-body-p">
                Normaal gesproken, zou hier een e-mail systeem achter zitten, die via tokens een tijdelijke wachtwoord reset mogelijk maakt.<br><br>
                Echter is dit voor de scope van dit project wat overdreven, mocht je echt je wachtwoord zijn vergeten, kan de admin/beheerder die veranderen.<br><br>
                In de bijbehorende documentatie, staat vermeld hoe je een admin wachtwoord kan resetten, mocht je die ook zijn vergeten/verloren.
            </p>
        </div>
    </div>
</div>
<style>
    .modal-form { display: block; }
    .modal-body p { padding: 0.2em; }
</style>
<?php require('partials/header.php'); ?>
    
    <form method="post" class="gebr-data-form" id="gebr-data-form" hidden>
        <input class="gebr-form-input" id="gebr-form-input" name="gebr-email" value="" hidden />
    </form>

    <?php require('partials/message-pop-in.html'); ?>
    
    <div class="content-container">

        <div class="title-banner">

            <div id="title-buttons" class="title-buttons">
                <button class="ww-reset-butt" onclick=wwResetClick()> Wachtwoord Reset </button> <br>
                <button class="logoff-butt" onclick=logoff()> Afmelden </button> <br>
                <button id="beheer-back-butt" class="beheer-back-butt" onclick=beheerBackButt() hidden> < Series </button> <br>
            </div>

            <h1 class="title-text"> Collectie Tracker v1.1: Beheer Applicatie </h1>
            
        </div>

        <?php
            require('partials/beheer-contr-templ.php');
            require('partials/beheer-weerg-templ.php');
            require('partials/beheer-albView-pop-in.php');
            require('partials/beheer-serie-maken-pop-in.html');
            require('partials/beheer-serie-bewerken-pop-in.html');
            require('partials/beheer-album-toevoegen-pop-in.html');
            require('partials/beheer-album-bewerken-pop-in.html');
            require('partials/beheer-wachtwoord-reset2-pop-in.html');
        ?>

    </div>

<?php require('partials/footer.php'); ?>
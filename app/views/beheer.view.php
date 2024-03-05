<?php require('partials/header.php'); ?>
<div class="content-container">

    <?php require('partials/message-pop-in.html'); ?>
    
    <div id="title-banner" class="banner-container">

        <div id="title-buttons" class="title-buttons">
            <button id="ww-reset-butt" class="ww-reset-butt" onclick=wwResetClick()> Wachtw Reset </button>
            <form class="logoff-form" id="logoff-form" method="post" action="/logout">
                <input class="logoff-butt" type="submit" value="Afmelden" />
            </form>
            <?php if(isset($_SESSION['page-data']['huidige-serie'])): ?>
                <form class="back-form" id="back-form" method="post" action="/beheer">
                    <button id="beheer-back-butt" class="beheer-back-butt" type="submit"> < Series </button>
                </form>
            <?php endif; ?>
        </div>

        <div id="title-cont" class="title-banner">
            <h1 class="title-text"> Collectie Tracker: Beheer Applicatie </h1>
        </div>
        
    </div>

    <?php
        require('partials/beheer-contr-templ.php');
        if(!isset($_SESSION['page-data']['huidige-serie'])) {
            require('partials/beheer-weerg-templ.php');
        } else {
            require('partials/beheer-albView-templ.php');
        }
        require('partials/beheer-serie-maken-pop-in.php');
        require('partials/beheer-serie-bewerken-pop-in.html');
        require('partials/beheer-album-toevoegen-pop-in.html');
        require('partials/beheer-album-bewerken-pop-in.html');
        require('partials/beheer-wachtwoord-reset2-pop-in.html');
        require('partials/footer.php');
    ?>
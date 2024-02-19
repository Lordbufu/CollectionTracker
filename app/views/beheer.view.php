<?php require('partials/header.php'); ?>
<!-- Test Code for sessions -->
<?php if (session_status() == PHP_SESSION_ACTIVE): ?>
    <p> Session Name: <?=session_name()?></p>
    <p> Cookie: <?=var_dump($_COOKIE)?></p>
    <p> Session Var: <?=var_dump($_SESSION)?></p>
<?php endif; ?>

<?php if (session_status() == PHP_SESSION_NONE): ?>
    <p> A Redirect Happend </p>
    <p> Session Name: <?=session_name()?></p>
    <p> Cookie: <?=var_dump($_COOKIE)?></p>
    <p> Session Var: <?=var_dump($_SESSION)?></p>
<?php endif; ?>

<div class="content-container">
    
    <form method="post" class="gebr-data-form" id="gebr-data-form" hidden>
        <input class="gebr-form-input" id="gebr-form-input" name="gebr-email" value="" hidden />
    </form>

    <?php require('partials/message-pop-in.html'); ?>
    
    <div id="title-banner" class="banner-container">
        <div id="title-buttons" class="title-buttons">
            <button id="ww-reset-butt" class="ww-reset-butt" onclick=wwResetClick()> Wachtw Reset </button>
            <button id="logoff-butt" class="logoff-butt" onclick=logoff()> Afmelden </button>
            <button id="beheer-back-butt" class="beheer-back-butt" onclick=beheerBackButt() hidden> < Series </button>
        </div>
        <div id="title-cont" class="title-banner">
            <h1 class="title-text"> Collectie Tracker: Beheer Applicatie </h1>
        </div>
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
        require('partials/footer.php');
    ?>
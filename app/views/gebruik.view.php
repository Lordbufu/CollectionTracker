<?php require('partials/header.php'); ?>
<div class="content-container">
    
    <form method="post" class="gebr-data-form" id="gebr-data-form">
        <input class="gebr-form-input" id="gebr-form-input" name="gebr-email" value="" hidden />
    </form>

    <?php require('partials/message-pop-in.html'); ?>

    <div id="title-banner" class="banner-container">
        <div id="title-buttons" class="title-buttons">
            <button class="logoff-butt" onclick=logoff()> <b>Afmelden</b> </button>
        </div>
        <div id="title-cont" class="title-banner">
            <h1 class="title-text"> Collectie Tracker: Gebruikers Applicatie </h1>
        </div>
    </div>

    <?php
        require('partials/gebruik-contr-templ.php');
        require('partials/gebruik-weerg-templ.php');
        require('partials/footer.php');
    ?>
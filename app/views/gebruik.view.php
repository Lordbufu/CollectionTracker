<?php require('partials/header.php'); ?>

<div class="content-container">
    
    <?php require('partials/message-pop-in.html'); ?>

    <div id="title-banner" class="banner-container">

        <div id="title-buttons" class="title-buttons">
            <form class="logoff-form" id="logoff-form" method="post" action="/logout">
                <input class="logoff-butt" type="submit" value="Afmelden" />
            </form>
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
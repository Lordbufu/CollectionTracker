<?php require('partials/header.php'); ?>
    
    <form method="post" class="gebr-data-form" id="gebr-data-form">
        <input class="gebr-form-input" id="gebr-form-input" name="gebr-email" value="" hidden />
    </form>

    <?php require('partials/message-pop-in.html'); ?>

    <div class="content-container">
        <div class="title-banner">
            <h1 class="title-text"> Collectie Tracker v1.1: Gebruikers Applicatie </h1>
            <button class="logoff-butt" onclick=logoff()> <b>Afmelden</b> </button>
        </div>

        <?php
            require('partials/gebruik-contr-templ.php');
            require('partials/gebruik-weerg-templ.php');
        ?>

    </div>
    
<?php require('partials/footer.php'); ?>
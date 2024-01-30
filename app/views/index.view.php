<?php require('partials/header.php'); ?>

<div class="content-container">

    <div id="title-banner" class="banner-container">
        <div class="title-buttons">
            <a class="reg-modal-butt" href="#account-maken-pop-in"> Register </a>
            <a class="log-modal-butt" href="#login-pop-in"> Login </a>
        </div>
        <div class="title-banner">
            <h1 class="title-text"> Collectie Tracker <?= $version ?> </h1>
        </div>
    </div>

    <?php require('partials/message-pop-in.html'); ?>

    <div class="text-content-container">
        <h2 class="content-header"> Welcome bij de Collectie Tracker </h2>
        <p class="content-intro">
            Deze Web-App is gemaakt, voor het bijhouden van verschillende collecties (series), zoals bv stripboeken/CD's/DVD's etc.
            Momenteel zal een serie, met de hand gemaakt en gevult moeten worden, door een ingebouwde Administrator.
            Iedereen kan een gebruikers account maken, en dan per beschikbare serie, aangeven welk(e) album(s) in zijn/haar bezit zijn.
        </p>
        <p class="content-info">
            Er zijn ook al een aantal geplande extra features, voor evt toekomstige versies.
            Dit betrekt voornamelijk administrative functies, zoals extra invul velden voor de ablums en series, voor alle gebruikers.
            Echter word er ook al gedacht aan een barcode scanner, afhankelijk van hoe dat gaat, kan het wellicht ook helpen bij het maken van series.
        </p>
    </div>
       
    <?php
        require('partials/account-maken-pop-in.html');
        require('partials/gebruikers-overeenkomst.html');
        require('partials/account-login-pop-in.html');
        require('partials/wachtwoord-reset-pop-in.html');
    ?>
    
    <div class="example-container">
        <img class="img-1" src="images/donald-duck.jpg" alt="Donald Duck Collectie">
        <img class="img-2" src="images/suske-wiske.jpg" alt="Suske en Wiske Collectie">
        <img class="img-3" src="images/lord-of-the-rings.jpg" alt="Lord of the Ring Collectie">
        <img class="img-4" src="images/janis-joplin.jpg" alt="Janis Jopin Collectie">
        <img class="img-5" src="images/marco-borsato.jpg" alt="Marco Borsato Collectie">
        <img class="img-6" src="images/the-office.jpg" alt="The Office Collectie">
    </div>

<?php require('partials/footer.php'); ?>
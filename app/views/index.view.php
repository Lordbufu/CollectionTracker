<?php
    require('partials/header.php');
?>
<!-- main content container -->
<div class="content-container">
    <!-- pagina title banner -->
    <div class="title-banner">
        <h1 class="title-text"> Collectie Tracker v1.1 </h1>
    </div>
    <?php
        require('partials/message-pop-in.html');
    ?>
    <!-- text container -->
    <div class="text-content-container">
        <h2 class="content-header"> Welcome bij de Collectie Tracker </h2>
        <p class="content-intro">
            Deze Web-App is gemaakt, voor het bijhouden van verschillende collecties (series), zoals bv stripboeken/CD's/DVD's etc.<br>
            Momenteel zal een serie, met de hand gemaakt en gevult moeten worden, door een ingebouwde Administrator.<br>
            Iedereen kan een gebruikers account maken, en dan per beschikbare serie, aangeven welk(e) album(s) in zijn/haar bezit zijn.<br>
        </p>
        <p class="content-info">
            Er zijn ook al een aantal geplande extra features, voor evt toekomstige versies.<br>
            Dit betrekt voornamelijk administrative functies, zoals extra invul velden voor de ablums en series, voor alle gebruikers.<br>
            Echter word er ook al gedacht aan een barcode scanner, afhankelijk van hoe dat gaat, kan het wellicht ook helpen bij het maken van series.<br>
        </p>
    </div>
    <!-- 'button' container -->
    <div class="button-container">
        <div class="butt-box">
            <a class="reg-modal-butt" href="#account-maken-pop-in"> Register </a>
        </div>
        <div class="butt-box">
            <a class="log-modal-butt" href="#login-pop-in"> Login </a>
        </div>
    </div>
    <!-- Pop-in Templates\Partials -->
    <?php
        require('partials/account-maken-pop-in.html');
        require('partials/gebruikers-overeenkomst.html');
        require('partials/account-login-pop-in.html');
        require('partials/wachtwoord-reset-pop-in.html');
    ?>
    <!-- plaatjes content -->
    <div class="example-container">
        <div class="picture-set">
            <img src="images/donald-duck.jpg" alt="Donald Duck Collectie">
            <img src="images/suske-wiske.jpg" alt="Suske en Wiske Collectie">
            <img src="images/lord-of-the-rings.jpg" alt="Lord of the Ring Collectie">
            <img src="images/janis-joplin.jpg" alt="Janis Jopin Collectie">
            <img src="images/marco-borsato.jpg" alt="Marco Borsato Collectie">
            <img src="images/the-office.jpg" alt="The Office Collectie">
        </div>
    </div>
</div>
<?php
    require('partials/footer.php');
?>
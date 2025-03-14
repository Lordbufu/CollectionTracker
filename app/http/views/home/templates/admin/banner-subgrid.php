<div class="banner-butt" id="banner-butt" >
    <form class="ww-reset-form" id="ww-reset-banner" method="get" action="/wwReset"> <button id="ww-reset-butt" class="banner-reset-butt button" type="submit">Wachtw Reset</button> </form>
    <form class="logoff-form" id="logoff-form" method="get" action="/logout"> <input class="banner-logoff-butt button" type="submit" value="Afmelden" /> </form>
    <?php if(isset($_SESSION['page-data']['huidige-reeks'])) : ?>
    <form class="back-form" id="back-form" method="post" action="/beheer">
        <input id="beheer-back-inp" class="beheer-back-inp" name="return" value="back" hidden />
        <input id="beheer-back-butt" class="banner-back-butt button" type="submit" value="< Reeks" />
    </form>
    <?php endif; ?>
</div>
<div class="banner-head" id="banner-head"> <h1 class="header-text" id="header-text">Collectie Tracker: Beheer</h1> </div>
<?php if(isset($_SESSION['page-data']['huidige-reeks'])) { $hReeks = inpFilt($_SESSION['page-data']['huidige-reeks']); } ?>

<div class="banner-butt">
    <form class="logoff-form" method="get" action="/logout">
        <input class="logoff-butt button" type="submit" value="Afmelden" />
    </form>

    <?php if(isset($hReeks)) : ?>
    <form class="back-form" id="back-form" method="post" action="/gebruik">
        <input id="beheer-back-inp" class="beheer-back-inp" name="return" value="back" hidden />
        <input id="beheer-back-inp2" class="beheer-back-inp2" name="reset" value="yes" hidden />
        <input id="beheer-back-butt" class="banner-back-butt button" type="submit" value="< Reeks" />
    </form>
    <?php endif; ?>
</div>

<div class="banner-head">
    <h1 class="header-text">Collectie Tracker: Gebruik</h1>
</div>
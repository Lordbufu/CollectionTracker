<!DOCTYPE html>
<html>
    <head> <?php require 'home\templates\header.php'; ?> </head>
    <body class="main-flex">
        <noscript>You need to enable JavaScript to run this app.</noscript>
        <div class="sub-grid-1">
            <div class="banner-head"> <h1 class="header-text">Collectie Tracker <?=$version?></h1> </div>
        </div>
        <div class="sub-grid-2">
            <div class="contr-cont-2">
                <h2 class="content-header">The empty page of pages !</h2>
                <p class="content-intro">
                    There are several reason why you might be seeing the current page.<br>
                    If you dont know why you are seeing this page, your Administrator is your friend.<br>
                </p>
                <p class="content-info">
                    In all other case, no other pages will be served, untill the issue is resolved.<br>
                    Have a nice day.
                </p>
            </div>
        </div>
        <div class="sub-grid-3"> <img class="img-2" src="images/goodbye.png" alt="adios amigos"> </div>
        <footer class="sub-grid-4"> <?php require 'home\templates\footer.php'; ?> </footer>
    </body>
</html>
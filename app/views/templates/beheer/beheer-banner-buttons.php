            <div class="banner-butt" id="banner-butt" >

                <button id="ww-reset-butt" class="banner-reset-butt button" onclick=wwResetClick() > Wachtw Reset </button>

                <form class="logoff-form" id="logoff-form" method="post" action="/logout">
                    <input class="banner-logoff-butt button" type="submit" value="Afmelden" />
                </form>

                <?php if( isset( $_SESSION["page-data"]["huidige-serie"] ) ): ?>

                <form class="back-form" id="back-form" method="post" action="/beheer">
                    <input id="beheer-back-inp" class="beheer-back-inp" name="return" value="back" hidden />
                    <input id="beheer-back-butt" class="banner-back-butt button" type="submit" value="< Series" />
                </form>

                <?php endif; ?>
                </div>
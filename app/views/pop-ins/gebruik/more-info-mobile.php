<div id="more-info-album" class="modal-cont" >

    <div class="modal-content-cont" id="modal-info-body" >

        <div class="modal-header-cont" id="modal-info-header" >

            <div class="modal-header-text" id="header-title" >
                <?php if( $_SESSION["page-data"]["mobile-details"] ) : ?>
                    <h3> <?= $_SESSION["page-data"]["mobile-details"]["Album_Naam"] ?> </h3>
                <?php endif; ?>
            </div>

            <form class="modal-header-close-form" id="close-info-form" method="post" action="/gebruik" >
                <input class="modal-header-input" id="close-info-inp" name="close-pop-in" value="" hidden />
                <input class="modal-header-close" id="close-info-butt" type="submit" value="&times;" />
            </form>

        </div>

        <div class="modal-body" id="modal-info-content" >

            <table class="extra-info-tafel" id="extra-info-tafel" >

            <?php
                if( isset( $_SESSION["page-data"]["mobile-details"] ) ) :
                    $store = $_SESSION["page-data"]["mobile-details"];
            ?>

                <tr class="first-data-set" >
                    <th class="first-data-name" id="first-data-name" > Uitgave-Nr: </th>
                    <th class="first-data-val" id="first-data-val" > <?= $store["Album_Nummer"] ?> </th>
                </tr>

                <tr class="second-data-set" >
                    <th class="second-data-name" id="second-data-name" > Uitgave Datum </th>
                    <th class="second-data-val" id="second-data-val" > <?= $store["Album_UitgDatum"] ?> </th>
                </tr>

                <tr class="third-data-set" >
                    <th class="third-data-name" id="third-data-name" > Album Cover: </th>
                    <th class="third-data-val" id="third-data-val" >
                        <img class="album-cover-img" id="album-cover-img" src="<?= $store["Album_Cover"] ?>" >
                    </th>
                </tr>

                <tr class="fourth-data-set" >
                    <th class="fourth-data-name" id="fourth-data-name" > Album ISBN: </th>
                    <th class="fourth-data-val" id="fourth-data-val" > <?= $store["Album_ISBN"] ?> </th>
                </tr>

                <tr class="fith-data-set" >
                    <th class="fith-data-name" id="fith-data-name" > Opmerking: </th>
                    <th class="fith-data-val" id="fith-data-val" > <?= $store["Album_Opm"] ?> </th>
                </tr>

            <?php endif; unset( $_SESSION["page-data"]["mobile-details"] ); ?>

            </table>

        </div>

    </div>

</div>
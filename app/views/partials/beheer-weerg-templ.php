<div class="beheer-weerg-cont">
    <div id="beheer-weerg-repl-cont" class="beheer-weerg-repl-cont">
        <h2 class="beheer-weerg-header"> Huidige Series, het aantal Albums en Gebruikers: </h2>
        <table id="serie-tafel" class="beheer-serie-tafel">
            <tr class="serie-tafel-titles">
                <th style="border: 0px;"></th>
                <th style="border: 0px;"></th>
                <th style="border: 0px;"></th>
                <th>Serie Naam</th>
                <th>Serie Makers</th>
                <th>Serie Opmerking</th>
                <th>Aantal Albums</th>
                <th>Aantal Gebruikers</th>
            </tr>

        <?php
            if(isset($_SESSION['page-data']['series'])):
                foreach($_SESSION['page-data']['series'] as $key => $value):
        ?>

            <tr class='serie-tafel-inhoud-<?=$value['Serie_Index']?>' id='serie-tafel-inhoud-<?=$value['Serie_Index']?>'>

                <!-- Series View -->
                <th class="serie-bekijken button" id="serie-bekijken">
                    <form id="serie-bekijken-form-<?=$value['Serie_Index'];?>" class="serie-bekijken-form" method="post" action="/beheer">
                        <input id="serie-bekijken-form-index-<?=$value['Serie_Index'];?>" class="serie-bekijken-form-index" name="serie-index" value="<?=$value['Serie_Index'];?>" hidden />
                        <input id="serie-bekijken-form-butt-<?=$value['Serie_Index'];?>" class="serie-bekijken-butt" type="submit" value=""/>
                    </form>
                </th>

                <!-- Series Edit -->
                <th class="serie-bewerken button" id="serie-bewerken">
                    <form id="serie-edit-form-<?=$value['Serie_Index'];?>" class="serie-edit-form" method="post" action="/beheer">
                        <input id="serie-edit-form-index-<?=$value['Serie_Index'];?>" class="serie-edit-form-index" name="serie-edit-index" value="<?=$value['Serie_Index'];?>" hidden />
                        <input id="<?=$value['Serie_Index'];?>" class="serie-bewerken-butt" type="submit" value="" />
                    </form>
                </th>

                <!-- Series Delete -->
                <th class="serie-verwijderen button">
                    <button class="serie-verwijderen-butt" id="<?= $value['Serie_Index']; ?>" type="button" onclick="serieVerwijderen(event)"> </button>
                </th>

                <th class="serie-naam" id="serieNaam"><?= $value['Serie_Naam']; ?></th>
                <th class="serie-maker" id="serieMaker"><?= $value['Serie_Maker']; ?></th>
                <th class="serie-opmerk" id="serieOpmerk"><?= $value['Serie_Opmerk'] ?></th>
                <th class="serie-albums"><?= $value['Album_Aantal'] ?></th>
                <th class="serie-gebruikers">W.I.P.</th>

        <?php
                endforeach;
            endif;
        ?>

            </tr>
        </table>
    </div>
</div>
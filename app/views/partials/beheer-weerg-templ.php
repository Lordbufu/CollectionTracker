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
                    $series = $_SESSION['page-data']['series'];
                    foreach($series as $key => $value):
                        echo "<tr class='serie-tafel-inhoud-{$series[$key]['Serie_Index']}' id='serie-tafel-inhoud-{$series[$key]['Serie_Index']}'>";
            ?>

                <th class="serie-bekijken button" id="serie-bekijken">
                    <form id="serie-bekijken-form-<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-form-<?=$series[$key]['Serie_Index'];?>" method="post" action="/beheer">
                        <input id="serie-bekijken-form-index-<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-form-index-<?=$series[$key]['Serie_Index'];?>" name="serie-index" value="" hidden />
                        <input id="serie-bekijken-form-naam-<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-form-naam-<?=$series[$key]['Serie_Index'];?>" name="serie-naam" value="" hidden />
                        <input id="<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-butt" type="submit" value="" onclick="serieBekijken(event)"/>
                    </form>
                </th>
                <th class="serie-bewerken button" id="serie-bewerken">
                    <button class="serie-bewerken-butt" id="<?= $series[$key]['Serie_Index']; ?>" type="button" onclick="serieBewerken(event)"> </button>
                </th>
                <th class="serie-verwijderen button">
                    <button class="serie-verwijderen-butt" id="<?= $series[$key]['Serie_Index']; ?>" type="button" onclick="serieVerwijderen(event)"> </button>
                </th>
                <th class="serie-naam" id="serieNaam"><?= $series[$key]['Serie_Naam']; ?></th>
                <th class="serie-maker" id="serieMaker"><?= $series[$key]['Serie_Maker']; ?></th>
                <th class="serie-opmerk" id="serieOpmerk"><?= $series[$key]['Serie_Opmerk'] ?></th>
                <th class="serie-albums"><?= $series[$key]['Album_Aantal'] ?></th>
                <th class="serie-gebruikers">W.I.P.</th>

            <?php
                endforeach;
                endif;
            ?>

            </tr>
        </table>
    </div>
</div>
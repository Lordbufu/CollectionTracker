<!-- Weergave Container & Vervang Container -->
<div class="beheer-weerg-cont">
    <div id="beheer-weerg-repl-cont" class="beheer-weerg-repl-cont">

        <!-- Weergave Title -->
        <h2 class="beheer-weerg-header"> Huidige Series, het aantal Albums en Gebruikers: </h2>

        <!-- Serie Tafel -->
        <table id="serie-tafel" class="serie-tafel">

            <!-- Tafel Titles -->
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

            <!-- Serie Tafel Inhoud -->
            <?php
                if(isset($data['series']))
                    foreach($series as $key => $value):
                        echo "<tr class='serie-tafel-inhoud-{$series[$key]['Serie_Index']}' id='serie-tafel-inhoud-{$series[$key]['Serie_Index']}'>";
            ?>

                <!-- Serie Bekijken -->
                <th class="serie-bekijken" id="serie-bekijken">
                    <form id="serie-bekijken-form-<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-form-<?=$series[$key]['Serie_Index'];?>" method="post" action="/beheer">
                        <input id="serie-bekijken-form-index-<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-form-index-<?=$series[$key]['Serie_Index'];?>" name="serie-index" value="" hidden />
                        <input id="serie-bekijken-form-naam-<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-form-naam-<?=$series[$key]['Serie_Index'];?>" name="serie-naam" value="" hidden />
                        <input id="<?=$series[$key]['Serie_Index'];?>" class="serie-bekijken-butt" type="submit" value="" onclick="serieBekijken(event)"/>
                    </form>
                </th>

                <!-- Serie Bewerken -->
                <th class="serie-bewerken" id="serie-bewerken">
                    <button class="serie-bewerken-butt" id="<?= $series[$key]['Serie_Index']; ?>" type="button" onclick="serieBewerken(event)"> </button>
                </th>

                <!-- Serie + Albums Verwijderen -->
                <th class="serie-verwijderen">
                    <button class="serie-verwijderen-butt" id="<?= $series[$key]['Serie_Index']; ?>" type="button" onclick="serieVerwijderen(event)"> </button>
                </th>

                <!-- Serie Album Naam -->
                <th class="serie-naam" id="serieNaam"><?= $series[$key]['Serie_Naam']; ?></th>

                <!-- Serie Album Makers -->
                <th class="serie-maker" id="serieMaker"><?= $series[$key]['Serie_Maker']; ?></th>

                <!-- Serie Album Opmerking -->
                <th class="serie-opmerk" id="serieOpmerk"><?= $series[$key]['Serie_Opmerk']; ?></th>

                <!-- Serie Album Uitgave Nummer-->
                <th class="serie-albums"><?= $series[$key]['Album_Aantal']; ?></th>

                <!-- Serie Album Cover -->
                <th class="serie-gebruikers">W.I.P.</th>

            <?php endforeach; ?>

            </tr>
        </table>
    </div>
</div>
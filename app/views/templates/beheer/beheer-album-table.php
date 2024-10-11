                <table id="beheer-albView-tafel" class="beheer-albums-tafel">
                    
                    <tr id="beheer-albView-titles" class="albums-tafel-titles">
                        <th style="border: 0px;" ></th>
                        <th style="border: 0px;" ></th>
                        <th> Naam </th>
                        <th> Uitg-Nr </th>
                        <th class="album-uitgTitle" > Uitg-Datum </th>
                        <th class="album-schrTitle" > Schrijver </th>
                        <th class="album-covTitle" > Cover </th>
                        <th> ISBN </th>
                        <th class="album-opmTitle" > Opmerking </th>
                    </tr>

                    <?php if( isset( $_SESSION["page-data"]["albums"] ) ) :
                            foreach( $_SESSION["page-data"]["albums"] as $key => $value ) : ?>

                    <tr class="album-tafel-inhoud-<?= $value["Album_Index"] ?>" id="album-tafel-inhoud">

                        <th id="album-bewerken" class="album-bewerken">

                            <form class="album-bewerken-form" id="album-bewerken-form-<?= $value["Album_Index"]; ?>" method="post" action="/albumBew" >
                                <input class="album-bewerken-inp" id="album-bew-inp-<?= $value["Album_Index"]; ?>" name="albumEdit" value="<?= $value["Album_Index"]; ?>" hidden />
                                <input class="album-bewerken-butt" id="album-bew-<?= $value["Album_Index"]; ?>" type="submit" value="" />
                            </form>

                        </th>

                        <th id="album-verwijderen" class="album-verwijderen">

                            <form class="album-verwijderen-form" id="album-verwijderen-form-<?= $value["Album_Index"]; ?>" method="post" action="/albumV">
                                <input class="album-verwijderen-inp" id="album-verw-inp1-<?= $value["Album_Index"]; ?>" name="album-index" value="<?= $value["Album_Index"]; ?>" hidden />
                                <input class="album-verwijderen-inp" id="album-verw-inp2-<?= $value["Album_Index"]; ?>" name="serie-index" value="<?= $value["Album_Serie"]; ?>" hidden />
                                <input class="album-verwijderen-butt" id="album-verw-<?= $value["Album_Index"]; ?>" type="submit" value="" />
                            </form>

                        </th>

                        <th id="album-naam" class="album-naam"> <?= $value["Album_Naam"] ?> </th>

                        <th id="album-nummer" class="album-nummer"> <?= $value["Album_Nummer"] ?> </th>

                        <th id="album-uitgave" class="album-uitgave"> <?= $value["Album_UitgDatum"] ?> </th>

                        <th id="album-schr" class="album-schr"> W.I.P. </th>

                        <th id="album-cover" class="album-cover">
                        <?php if( isset( $value["Album_Cover"] ) ) : ?>
                            <img id="album-cover-img" class="album-cover-img" src="<?= $value["Album_Cover"] ?>" alt="album-cover" />
                        <?php endif; ?>
                        </th>

                        <th id="album-isbn" class="album-isbn"> <?= $value["Album_ISBN"] ?> </th>

                        <th id="album-opm" class="album-opm"> <?= $value["Album_Opm"] ?> </th>

                    </tr>

                    <?php endforeach; endif; ?>

                </table>
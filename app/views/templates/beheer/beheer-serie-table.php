                <table id="serie-tafel" class="beheer-serie-tafel">

                    <tr class="serie-tafel-titles">

                        <th style="border: 0px;"></th>
                        <th style="border: 0px;"></th>
                        <th style="border: 0px;"></th>
                        <th>Serie Naam</th>
                        <th class="serieAuthTitle">Serie Makers</th>
                        <th class="serieOpmTitle">Serie Opmerking</th>
                        <th>Aantal Albums</th>
                        <th class="serieUsersTitle">Aantal Gebruikers</th>

                    </tr>
                    
                    <?php
                        if( isset( $_SESSION["page-data"]["series"] ) ) :
                            foreach( $_SESSION["page-data"]["series"] as $key => $value ) :
                    ?>

                    <tr class="serie-tafel-inhoud-<?= $value["Serie_Index"] ?>" id="serie-tafel-inhoud-<?= $value["Serie_Index"] ?>" >

                        <th class="serie-bekijken" id="serie-bekijken" >

                            <form id="serie-bekijken-form-<?= $value["Serie_Index"]; ?>" class="serie-bekijken-form" method="post" action="/beheer">
                                <input id="serie-bekijken-form-index-<?= $value["Serie_Index"]; ?>" class="serie-bekijken-form-index" name="serie-index" value="<?= $value["Serie_Index"]; ?>" hidden />
                                <input id="serie-bekijken-form-butt-<?= $value["Serie_Index"]; ?>" class="serie-bekijken-butt" type="submit" value=""/>
                            </form>

                        </th>

                        <th class="serie-bewerken" id="serie-bewerken" >

                            <form id="serie-edit-form-<?= $value["Serie_Index"]; ?>" class="serie-edit-form" method="post" action="/beheer">
                                <input id="serie-edit-form-index-<?= $value["Serie_Index"]; ?>" class="serie-edit-form-index" name="serie-edit-index" value="<?= $value["Serie_Index"]; ?>" hidden />
                                <input id="serie-edit-<?=$value["Serie_Index"];?>" class="serie-bewerken-butt" type="submit" value="" />
                            </form>

                        </th>

                        <th class="serie-verwijderen" id="serie-verwijderen" >

                            <form id="serie-verw-form-<?= $value["Serie_Index"]; ?>" class="serie-edit-form" method="post" action="/serieVerw" >
                                <input id="serie-verw-form-index-<?= $value["Serie_Index"]; ?>" class="serie-verw-form-index" name="serie-index" value="<?= $value["Serie_Index"]; ?>" hidden />
                                <input id="serie-verw-form-naam-<?=$value["Serie_Index"]; ?>" class="serie-verw-form-naam" name="serie-naam" value="<?= $value["Serie_Naam"]; ?>" hidden />
                                <input id="serie-verw-<?= $value["Serie_Index"]; ?>" class="serie-verwijderen-butt" type="submit" value="" onclick="return serieVerwijderen(event)" />
                            </form>

                        </th>

                        <th class="serie-naam" id="serieNaam" > <?= $value["Serie_Naam"]; ?> </th>

                        <th class="serie-maker" id="serieMaker" > <?= $value["Serie_Maker"]; ?> </th>

                        <th class="serie-opmerk" id="serieOpmerk" > <?= $value["Serie_Opmerk"]; ?> </th>

                        <th class="serie-albums" id="serieAlbums" > <?= $value["Album_Aantal"]; ?> </th>

                        <th class="serie-gebruikers" id="serieGebr" > W.I.P. </th>

                    <?php
                            endforeach;
                        endif;
                    ?>

                    </tr>
                    
                </table>
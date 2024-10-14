                <form class="contr-album-form" id="album-toev-form" method="post" action="/beheer">

                    <label for="album-toev" class="contr-album-lab"> Album Toevoegen: </label>

                    <select class="contr-album-select" name="album-toev" id="album-toev" required>

                        <option value=""> Selecteer een serie </option>
                        <?php
                            if( isset( $_SESSION["page-data"]["series"] ) ):
                                foreach( $_SESSION["page-data"]["series"] as $key => $value ) :
                        ?>
                                    <option class="album-toev-opt" id="album-toev-opt"><?= $value["Serie_Naam"] ?></option>
                        <?php
                                endforeach;
                            endif;
                        ?>

                    </select>

                    <input class="contr-album-subm button" id="album-toev-subm" type="submit" value="Invoeren" />

                    <button class="contr-album-isbn-search button" id="album-isbn-search" type="submit" formmethod="post" formaction="/scan"> Scan Barcode </button>

                </form>
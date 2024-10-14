                <form class="serie-sel-form" id="serie-sel-form" action="/gebruik" method="post" >

                    <label for="serie-sel" class="serie-sel-lab"> Serie Selecteren: </label>

                    <select class="serie-sel" name="serie_naam" id="serie-sel" required>
                        <option class="serie-sel-opt" value=""> Selecteer een serie </option>
                        <?php if( isset( $_SESSION["page-data"]["series"] ) ) :
                            foreach( $_SESSION["page-data"]["series"] as $key => $value ) :
                                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) :
                                    if( $value["Serie_Naam"] === $_SESSION["page-data"]["huidige-serie"] ) : ?>
                            <option class="serie-sel-opt" selected><?= $value["Serie_Naam"] ?></option>
                            <?php else : ?>
                            <option class="serie-sel-opt"><?= $value["Serie_Naam"] ?></option>
                            <?php endif; else : ?>
                            <option class="serie-sel-opt"><?= $value["Serie_Naam"] ?></option>
                            <?php endif; endforeach; endif; ?>
                    </select>

                    <input class="serie-sel-subm button" id="serie-sel-subm" type="submit" value="Selecteer" />

                </form>
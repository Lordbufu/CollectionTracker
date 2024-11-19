        <div id="serieb-pop-in" class="modal-cont" >

            <div class="modal-content-cont" id="modal-content-cont" >

                <div class="modal-header-cont" id="modal-header-cont" >

                    <h3 class="modal-header-text" id="modal-header-text" > Serie Bewerken </h3>

                    <form class="modal-header-close-form" method="post" action="/beheer" >
                        <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                        <input class="modal-header-close" type="submit" value="&times;" />
                    </form>

                </div>

                <div class="modal-body" id="modal-body" >

                    <?php
                        if( isset( $_SESSION["page-data"]["edit-serie"] ) ) :
                            foreach( $_SESSION["page-data"]["series"] as $key => $value)  :
                                if( $_SESSION["page-data"]["edit-serie"] == $value["Serie_Index"] ) :
                    ?>
                                
                    <form class="modal-form" id="serieb-form" method="post" action="/serieBew">

                        <input type="text" class="modal-form-input" id="serieb-form-index" name="index" value="<?= $value["Serie_Index"] ?>" hidden />

                        <div class="modal-form-left-cont" id="modal-form-left-cont">

                            <p id="modal-small-text" class="modal-small-text" > De serie naam is een verplicht veld </p>

                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="serieb-form-serieNaam" name="naam" placeholder="" value="<?= $value["Serie_Naam"] ?>" autocomplete="on" required />
                                <span class="modal-form-span"> Serie Naam </span>
                            </label>

                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="serieb-form-makers" name="makers" placeholder="" value="<?= $value["Serie_Maker"] ?>" autocomplete="on" />
                                <span class="modal-form-span"> Makers/Artiesten </span>
                            </label>

                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="serieb-form-opmerking" name="opmerking" placeholder="" value="<?= $value["Serie_Opmerk"] ?>" autocomplete="on" />
                                <span class="modal-form-span"> Opmerking/Notitie </span>
                            </label>

                            <div class="butt-box" id="butt-box" >
                                <input class="modal-form-button button" id="serieb-form-button" type="submit" value="Bevestigen" />
                            </div>

                        </div>

                        <div class="modal-form-right-cont" id="modal-form-right-cont"> </div>

                    </form>

                    <?php
                                endif;
                            endforeach;
                            unset( $_SESSION["page-data"]["edit-serie"] );
                        elseif( !isset( $_SESSION["page-data"]["edit-serie"] ) ) :
                    ?>
                    <!-- For JS reasons i need a empty pop-in if nothing is being edited -->
                    <form class="modal-form" id="serieb-form" method="post" action="/serieBew">

                        <input type="text" class="modal-form-input" id="serieb-form-index" name="index" value="" hidden />

                        <div class="modal-form-left-cont" id="modal-form-left-cont">

                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="serieb-form-serieNaam" name="naam" placeholder="" value="" autocomplete="on" required />
                                <span class="modal-form-span"> Serie Naam </span>
                            </label>

                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="serieb-form-makers" name="makers" placeholder="" value="" autocomplete="on" />
                                <span class="modal-form-span"> Makers/Artiesten </span>
                            </label>

                            <label class="modal-form-label">
                                <input type="text" class="modal-form-input" id="serieb-form-opmerking" name="opmerking" placeholder="" value="" autocomplete="on" />
                                <span class="modal-form-span"> Opmerking/Notitie </span>
                            </label>

                            <div class="butt-box" id="butt-box" >
                                <input class="modal-form-button button" id="serieb-form-button" type="submit" value="Bevestigen" />
                            </div>

                        </div>

                        <div class="modal-form-right-cont" id="modal-form-right-cont"> </div>
                    </form>

                    <?php
                        endif;
                    ?>

                </div>

            </div>

        </div>
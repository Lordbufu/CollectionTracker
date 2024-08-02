            <form class="modal-form" id="albumb-form" enctype="multipart/form-data" method="post" action="/albumBew">
                <div class="modal-form-content-cont">

                    <!-- Extra info for album links -->
                    <input type="text" class="modal-form-indexS" id="albumb-form-indexS" name="serie-index" value="<?=$store["Album_Serie"]?>" hidden />
                    <input type="text" class="modal-form-indexA" id="albumb-form-indexA" name="album-index" value="<?=$store["Album_Index"]?>" hidden />

                    <!-- Pop-In left-side content -->
                    <div class="modal-form-left-cont">
                        <!-- Album naam input -->
                        <label class="modal-form-label">
                            <input type="text" class="modal-form-input" id="albumb-form-alb-naam" name="album-naam" placeholder="" value="<?=$store["Album_Naam"]?>" autocomplete="on" required />
                            <span class="modal-form-span"> Album Naam </span>
                        </label>

                        <!-- Album nummer input -->
                        <label class="modal-form-label">
                            <input type="number" min="0" class="modal-form-input" id="albumb-form-alb-nr" name="album-nummer" placeholder="" value="<?=$store["Album_Nummer"]?>" autocomplete="on" />
                            <span class="modal-form-span"> Album Uitgave Nummer </span>
                        </label>

                        <!-- Album datum input -->
                        <label class="modal-form-label">
                            <input type="date" class="modal-form-input" id="albumb-form-alb-date" name="album-datum" placeholder=""  value="<?=$store["Album_UitgDatum"]?>" autocomplete="on" />
                            <span class="modal-form-span"> Album Uitgave Datum </span>
                        </label>

                        <!-- Album cover preview & input -->
                        <?php if( !empty( $store["Album_Cover"] ) ) : ?>

                        <div class="modal-album-cover" id="albumB-cover">
                            <img class="modal-album-cover-img" id="albumb-cover-img" src="<?=$store['Album_Cover']?>" alt='album-cover'/>
                        </div>

                        <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                            Nieuwe Cover Selecteren
                            <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                        </label>

                        <?php else : ?>

                        <div class="modal-album-cover" id="albumB-cover"> </div>

                        <label class="modal-form-alb-cov-lab" id="modal-form-albumB-cov-lab" for="albumb-form-alb-cov" >
                            Album Cover Selecteren
                            <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumb-form-alb-cov" name="album-cover" />
                        </label>

                        <?php endif; ?>

                        <!-- Album isbn input -->
                        <label class="modal-form-label">
                            <input class="modal-form-input" id="albumb-form-alb-isbn" name="album-isbn" placeholder="" value="<?=$store['Album_ISBN']?>" autocomplete="on" required />
                            <span class="modal-form-span"> Album ISBN </span>
                        </label>

                        <!-- Album opmerking input -->
                        <label class="modal-form-label">
                            <input class="modal-form-input" id="albumb-form-alb-opm" name="album-opm" placeholder="" value="<?=$store['Album_Opm']?>" autocomplete="on" />
                            <span class="modal-form-span" hidden> Album Opmerking </span>
                        </label>
                        
                        <input class="modal-form-button" id="albumb-form-button" type="submit" value="Bevestigen" />
                    </div>

                    <!-- Pop-In right-side content -->
                    <div class="modal-form-right-cont">
                        <!-- Album naam fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album nummer fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album datum fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album cover fake trigger -->
                        <?php if( !empty( $store["Album_Cover"] ) ) : ?>
                            <button class="modal-form-fake-extraTriger" disabled> </button>
                            <button class="modal-form-fake-triger" disabled> </button>
                        <?php else : ?>
                            <button class="modal-form-fake-triger" disabled> </button>
                        <?php endif; ?>

                        <!-- Album isbn submit trigger -->
                        <button class="modal-form-isbn-triger" id="modal-form-isbn-triger" type="submit" formaction="/isbn" formmethod="post"></button>

                        <!-- Album opmerking fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>

                        <!-- Album submit fake trigger -->
                        <button class="modal-form-fake-triger" disabled> </button>
                    </div>

                </div>
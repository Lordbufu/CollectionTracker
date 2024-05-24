<div id="albumt-pop-in" class="modal-container">
    <div class="modal-content-container">

        <div class="modal-header">  
            <h1 class="modal-header-text"> Album Toevoegen </h1>
            <form class="modal-header-close-form" method="post" action="/beheer" >
                <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>

        <div class="modal-body">
            <form class="modal-form" id="albumt-form" enctype="multipart/form-data" method="post" action="/albumT" >
                <?php if(isset($_SESSION['page-data']['add-album'])): ?>
                    <input class="modal-form-hidden" id="modal-form-hidden" name="serie-index" value='<?=$_SESSION['page-data']['add-album']?>' hidden />
                <?php
                    unset($_SESSION['page-data']['add-album']);
                    endif;
                ?>
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="albumt-form-alb-naam" name="album-naam" placeholder="" autocomplete="on" required />
                    <span class="modal-form-span"> Album Naam </span>
                </label>
                <label class="modal-form-label">
                    <input type="number" min="0" class="modal-form-input" id="albumt-form-alb-nr" name="album-nummer" placeholder="" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Nummer </span>
                </label>
                <label class="modal-form-label">
                    <input type="date" class="modal-form-input" id="albumt-form-alb-date" name="album-datum" placeholder="" autocomplete="on" />
                    <span class="modal-form-span"> Album Uitgave Datum </span>
                </label>
                <div class="modal-album-cover" id="albumt-cover">
                    <?php if( isset($_SESSION['page-data']['album-cover']) ) : ?>
                        <div class="modal-album-cover" id="albumb-cover">
                            <img src="<?= $_SESSION['page-data']['album-cover'] ?>" id="albumb-cover-img" class="modal-album-cover-img">
                        </div>
                    <?php endif; ?>
                </div>
                <label class="modal-form-alb-cov-lab" id="modal-form-alb-cov-lab" for="albumt-form-alb-cov">
                        <input type="file" accept="jpg, png, jpeg, gif" class="modal-form-input" id="albumt-form-alb-cov" name="album-cover" />
                    Album Cover Selecteren
                </label>
                <label class="modal-form-label">
                    <input class="modal-form-input" id="albumt-form-alb-isbn" name="album-isbn" placeholder="" autocomplete="on" required />
                    <span class="modal-form-span"> Album ISBN </span>
                </label>
                <input class="modal-form-button" id="albumt-form-button" type="submit" value="Bevestigen" />
            </form>
        </div>

    </div>
</div>
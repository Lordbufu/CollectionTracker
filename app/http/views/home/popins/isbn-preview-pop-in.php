<?php if(isset($_SESSION['_flash']['isbn-choices'])) {
    $store = $_SESSION['_flash']['isbn-choices'];
    $store['index'] = $_SESSION['_flash']['tags']['reeks-index'];
    $store['isbn'] = $_SESSION['_flash']['tags']['isbn-scanned'];
    unset($_SESSION['_flash']['tags']);
} ?>

<div id="isbn-preview" class="modal-cont">
    <div class="modal-content-cont">
        <div class="modal-header-cont">
            <h3 class="modal-header-text">ISBN Search Review</h3>
            <?php if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'user') : ?>
            <form class="modal-header-close-form" method="post" action="/gebruik">
                <input class="modal-header-input" name="close" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>
            <?php elseif(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'admin') : ?>
            <form class="modal-header-close-form" method="post" action="/beheer">
                <input class="modal-header-input" name="close" value="back" hidden/>
                <input class="modal-header-close" type="submit" value="&times;"/>
            </form>
            <?php endif; ?>
        </div>

        <div class="modal-body">
            <form class="modal-form" enctype="multipart/form-data" method="post" action="/scanConf">
                <input class="modal-form-isbn" name="isbn-choice" value="<?=$store['isbn']?>" hidden/>
                <input class="modal-form-serie-index" name="reeks-index" value="<?=$store['index']?>" hidden/>
                <div class="modal-form-left-cont" id="modal-form-left-cont">
                    <select class="modal-form-select" name="title-choice" id="item-choice" required>
                        <option class="modal-form-title-options" value="">Selecteer een title</option>
                        <?php   // Ignore the 'Titles', 'isbn' & 'index' entries.
                            foreach($store as $key => $value) :
                                if($key != 0 && $key !== 'isbn' && $key !== 'index') : ?>
                        <option class="modal-form-title-options"><?=$value?></option>
                        <?php   endif;
                            endforeach; ?>
                    </select>

                    <div class="butt-box">
                        <input class="modal-form-button button" id="prevSubm" type="submit" value="Bevestigen"/>
                    </div>
                </div>
                <div class="modal-form-right-cont"></div>
            </form>
        </div>
        
    </div>
</div>

<script>
    const prevSubm = document.getElementById('prevSubm');
    prevSubm.addEventListener('click', saveScroll);
</script>
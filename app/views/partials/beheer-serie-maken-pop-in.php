<div id="seriem-pop-in" class="modal-container">
    <div class="modal-content-container">
          
        <div class="modal-header">  
            <h1 class="modal-header-text"> Serie Maken </h1>
            <form class="modal-header-close-form" method="post" action="/beheer" >
                <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>
          
        <div class="modal-body">
            <form class="modal-form" id="seriem-form" method="post" action="/serieM">
                <label class="modal-form-label">
                <?php if( isset( $_SESSION["page-data"]["new-serie"] ) ): ?>
                    <input type='text' class='modal-form-input' id='seriem-form-serieNaam' name='serie-naam' placeholder='' value='<?=$_SESSION["page-data"]["new-serie"]?>' autocomplete='on' required >
                <?php elseif( isset( $_SESSION["page-data"]["serie-dupl"] ) ): ?>
                <input type='text' class='modal-form-input' id='seriem-form-serieNaam' name='serie-naam' placeholder='' value='<?=$_SESSION["page-data"]["serie-dupl"]["serie-naam"]?>' autocomplete='on' required >
                <?php else: ?>
                    <input type='text' class='modal-form-input' id='seriem-form-serieNaam' name='serie-naam' placeholder='' value='' autocomplete='on' required >
                <?php endif; unset($_SESSION['page-data']['new-serie']); ?>
                    <span class="modal-form-span"> Serie Naam </span>
                </label>
                <label class="modal-form-label">
                    <?php if( isset( $_SESSION["page-data"]["serie-dupl"] ) ) : ?>
                        <input type="text" class="modal-form-input" id="seriem-form-makers" name="makers" value="<?=$_SESSION["page-data"]["serie-dupl"]["makers"]?>" placeholder="" autocomplete="on" />
                    <?php else : ?>
                        <input type="text" class="modal-form-input" id="seriem-form-makers" name="makers" placeholder="" autocomplete="on" />
                    <?php endif; ?>
                    <span class="modal-form-span"> Makers/Artiesten </span>
                </label>
                <label class="modal-form-label">
                    <?php if( isset( $_SESSION["page-data"]["serie-dupl"] ) ) : ?>
                        <input type="text" class="modal-form-input" id="seriem-form-opmerking" name="opmerking" value="<?=$_SESSION["page-data"]["serie-dupl"]["opmerking"]?>" placeholder="" autocomplete="on" />
                    <?php else : ?>
                    <input type="text" class="modal-form-input" id="seriem-form-opmerking" name="opmerking" placeholder="" autocomplete="on" />
                    <?php endif; unset( $_SESSION["page-data"]["serie-dupl"] ); ?>
                    <span class="modal-form-span"> Opmerking/Notitie </span>
                </label>
                <input class="modal-form-button" id="seriem-form-button" type="submit" value="Bevestigen">
            </form>
        </div>
        
    </div>
</div>
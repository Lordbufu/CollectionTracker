<div id="serieb-pop-in" class="modal-container">
    <div class="modal-content-container">
          
        <div class="modal-header">  
            <h1 class="modal-header-text"> Serie Bewerken </h1>
            <form class="modal-header-close-form" method="post" action="/beheer" >
                <input class="modal-header-input" name="close-pop-in" value="" hidden/>
                <input class="modal-header-close" type="submit" value="&times;" />
            </form>
        </div>
        
        <?php
            // Check if store is set, and find a match with the requested series to edit.
            if(isset($_SESSION['page-data']['edit-serie'])) :
                foreach($_SESSION['page-data']['series'] as $key => $value) :
                    if($_SESSION['page-data']['edit-serie'] == $value['Serie_Index']) :
        ?>
        <div class="modal-body">
            <form class="modal-form" id="serieb-form" method="post" action="/serieBew">
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="serieb-form-serieNaam" name="naam" placeholder="" value="<?=$value['Serie_Naam']?>" autocomplete="on" required />
                    <span class="modal-form-span"> Serie Naam </span>
                </label>
                <input type="text" class="modal-form-input" id="serieb-form-index" name="index" value="<?=$value['Serie_Index']?>" hidden />
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="serieb-form-makers" name="makers" placeholder="" value="<?=$value['Serie_Maker']?>" autocomplete="on" />
                    <span class="modal-form-span"> Makers/Artiesten </span>
                </label>
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="serieb-form-opmerking" name="opmerking" placeholder="" value="<?=$value['Serie_Opmerk']?>" autocomplete="on" />
                    <span class="modal-form-span"> Opmerking/Notitie </span>
                </label>
                <input class="modal-form-button" id="serieb-form-button" type="submit" value="Bevestigen" />
            </form>
        </div>
        <?php
            
                    endif;
                endforeach;
            unset($_SESSION['page-data']['edit-serie']);
            elseif(!isset($_SESSION['page-data']['edit-serie'])) :
        ?>
        <!-- For script reasons i need a empty pop-in, might remove this later -->
        <div class="modal-body">
            <form class="modal-form" id="serieb-form" method="post" action="/serieBew">
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="serieb-form-serieNaam" name="naam" placeholder="" value="" autocomplete="on" required />
                    <span class="modal-form-span"> Serie Naam </span>
                </label>
                <input type="text" class="modal-form-input" id="serieb-form-index" name="index" value="" hidden />
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="serieb-form-makers" name="makers" placeholder="" value="" autocomplete="on" />
                    <span class="modal-form-span"> Makers/Artiesten </span>
                </label>
                <label class="modal-form-label">
                    <input type="text" class="modal-form-input" id="serieb-form-opmerking" name="opmerking" placeholder="" value="" autocomplete="on" />
                    <span class="modal-form-span"> Opmerking/Notitie </span>
                </label>
                <input class="modal-form-button" id="serieb-form-button" type="submit" value="Bevestigen" />
            </form>
        </div>
        <?php
            endif;
        ?>

    </div>
</div>
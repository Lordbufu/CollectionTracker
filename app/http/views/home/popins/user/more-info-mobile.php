<?php
if(isset($_SESSION['page-data']['mobile-details'])) {
    $store = $_SESSION['page-data']['mobile-details'];
    unset($_SESSION['page-data']['mobile-details']);
}
?>
<div id="more-info-album" class="modal-cont">
    <div class="modal-content-cont">
        <div class="modal-header-cont">
            <div class="modal-header-text"> <h3><?= inpFilt($store['Album_Naam']) ?? '' ?></h3> </div>
            <a class="modal-header-close" href="/gebruik">&times;</a>
        </div>
        <div class="modal-body">
            <table class="extra-info-tafel">
                <tr class="first-data-set">
                    <th class="first-data-name">Uitgave-Nr:</th>
                    <th class="first-data-val"><?=$store['Album_Nummer']?></th>
                </tr>
                <tr class="second-data-set">
                    <th class="second-data-name">Uitgave Datum:</th>
                    <th class="second-data-val"><?=$store['Album_UitgDatum']?></th>
                </tr>
                <tr class="third-data-set">
                    <th class="third-data-name">Album Cover:</th>
                    <th class="third-data-val">
                        <img class="album-cover-img "src="<?=$store['Album_Cover']?>">
                    </th>
                </tr>
                <tr class="fourth-data-set">
                    <th class="fourth-data-name">Album ISBN:</th>
                    <th class="fourth-data-val"><?=$store['Album_ISBN']?></th>
                </tr>
                <tr class="fith-data-set">
                    <th class="fith-data-name">Opmerking:</th>
                    <th class="fith-data-val"><?=inpFilt($store['Album_Opm'])?></th>
                </tr>
            </table>
        </div>
    </div>
</div>
<script>
    /* fetchRequest(uri, method, data): This function sends the request for album details to PhP, and returns the anwser. */
    async function fetchRequest(uri, method, data) {
        try { const response = await fetch(uri, { method: method, body: data });
        if(!response.ok) { throw new Error(`Response status: ${response.status}`); } else { return response; }
        } catch(error) { return console.error(error.message); }
    }
    /* viewDetails(e): This function creates new form data, and sends a request to the server, so the details of said request can be displayed. */
    function viewDetails(e) {
        data = new FormData(), data.append('album-index', e.target.id);
        fetchRequest('/details', 'POST', data)
        .then((resp) => resp.text())
        .then((text) => { if(text === 'display') { location.replace('#' + 'more-info-album'), location.reload(); } else { displayMessage(text); } });
    }
</script>
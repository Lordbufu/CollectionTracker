<?php if(isset($_SESSION['page-data']['reeks'])) { $rStore = $_SESSION['page-data']['reeks']; } ?>

<div class="table-header">
    <h2 class="gebruik-weerg-header">Selecteer een Reeks</h2>
</div>

<div class="reeks-image-grid">
    <form id="open-reeks-form" action="/selReeks" method="post" hidden>
        <input class="reeks-form-ind-inp" id="reeks-index-inp" name="index" value="" hidden>
    </form>

    <?php foreach($rStore as $key => $object) {
            if(isset($object['Reeks_Plaatje']) && !empty($object['Reeks_Plaatje'])) {
                $preview = "
                <div class='preview-cont'>
                    <img class='reeks-preview' id='{$object['Reeks_Index']}' src='{$object['Reeks_Plaatje']}'>
                    <p class='reeks-pr-name'>{$object['Reeks_Naam']}</p>
                </div>";
            } else {
                $preview = "
                <div class='preview-cont'>
                    <img class='reeks-preview' id='{$object['Reeks_Index']}' src='images/No-Image_Avail.png'>
                    <p class='reeks-pr-name'>{$object['Reeks_Naam']}</p>
                </div>";
            }

            if(isset($preview)) {
                echo $preview;
            }
        }
    ?>
</div>

<style>
    .reeks-image-grid { display: inline-flex; flex-wrap: wrap; justify-content: space-evenly; text-align: center; width: 100%; }
    .preview-cont { min-height: 10em; max-Width: 7em; max-height: fit-content; }
    .reeks-pr-name { height: 40%; }
    .reeks-preview { width: 100%; height: 60%; align-content: center; padding: 0.1em; } .reeks-preview:hover{ cursor: pointer; }
</style>

<script>
    const preview = document.getElementsByClassName('reeks-preview'); const preArr = Array.from(preview);
    preArr.forEach((item, index, arr) => { arr[index].addEventListener('click', openReeks); });
    function openReeks(e) { form = document.getElementById('open-reeks-form'); input = document.getElementById('reeks-index-inp'); input.value = e.target.id; form.submit(); }
</script>
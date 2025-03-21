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
                $preview = "<img class='reeks-preview' id='{$object['Reeks_Index']}' src={$object['Reeks_Plaatje']}>";
            } else {
                $preview = "<div class='reeks-preview' id='{$object['Reeks_Index']}'>{$object['Reeks_Naam']}</div>";
            }

            if(isset($preview)) {
                echo $preview;
            }
        }
    ?>
</div>

<style>
    .reeks-image-grid {
        display: flex;
        flex-flow: wrap;
        width: 100%;
    }

    .reeks-preview {
        height: 50%;
        width: 15%;
        text-align: center;
        align-content: center;
        padding: 0.1em;
    }

    .reeks-preview:hover{ cursor: pointer; }
</style>

<script>
    const preview = document.getElementsByClassName('reeks-preview');
    const preArr = Array.from(preview);

    preArr.forEach((item, index, arr) => {
        arr[index].addEventListener('click', test);
    });

    function test(e) {
        form = document.getElementById('open-reeks-form');
        input = document.getElementById('reeks-index-inp');
        input.value = e.target.id;
        form.submit();
    }
</script>
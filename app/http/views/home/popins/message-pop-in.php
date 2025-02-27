<div class="placement-container">
    <div id="message-pop-in" class="message-container">
        <?php foreach($_SESSION['_flash']['feedback'] as $key) : ?>
        <h1 class="response-message"><?=$key?></h1>
        <?php endforeach; unset($_SESSION['_flash']['feedback']); ?>
    </div>
</div>

<style>
    /* Feedback message container */
    .placement-container {
        display: grid;
    }

    .message-container {
        z-index: 4;
        display: none;
        position: fixed;
        text-align: center;
        justify-content: center;
        justify-self: center;
        top: -10%;
        margin: 0.2em;
        padding: 0.2em;
        width: max-content;
        background-color: var(--main-butt-hov);
        border: var(--main-border-body);
        border-radius: var(--main-border-rad);
    }

    .response-message {
        font-size: smaller;
    }
</style>

<script>
    /* change the main container style to be displayed on screen. */
    const container = document.getElementById('message-pop-in');
    container.style.display = 'flex';
    container.style.top = '0%';

    /* Set a 3 second time-out, and hide the main container after the timeout. */
    setTimeout(
        function() {
            container.style.display = 'none';
            container.style.top = '-10%';
            for(const child of container.children) {
                child.innerHTML = '';
            }
        },
    5000 );
</script>
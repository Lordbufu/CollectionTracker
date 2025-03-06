<main class="bsod container">
    <h1 class="neg title">
        <span class="bg">Error - 404</span>
    </h1>
    <p>An error has occured, to continue:</p>
    <p>* Return to our homepage.<br />
       * Send us an e-mail about this error and try later.</p>
    <nav class="nav">
        <a href="/" class="link">Home</a>&nbsp;|&nbsp;<a href="/" class="link">Home</a>
    </nav>
</main>

<style>

@import 'https://fonts.googleapis.com/css?family=VT323';

:root {
    --light-grey: #e0e2f4;
    --grey: #aaaaaa;
    --blue: #0414a7;
    --base-font-size: 20px;
    --font-stack: 'VT323', monospace;
}


body, h1, h2, h3, h4, p, a { color: var(--light-grey); }
body, p { font: normal var(--base-font-size)/1.25rem var(--font-stack); }
h1 { font: normal 2.75rem/1.05em var(--font-stack); }
h2 { font: normal 2.25rem/1.25em var(--font-stack); }
h3 { font: lighter 1.5rem/1.25em var(--font-stack); }
h4 { font: lighter 1.125rem/1.2222222em var(--font-stack); }
body { background: var(--blue); }

.container {
    width: 90%;
    margin: auto;
    max-width: 640px;
}

.bsod { padding-top: 10%; }

.neg {
    text-align: center;
    color: var(--blue);
}

.bg {
    background: var(--grey);
    padding: 0 15px 2px 13px;
}

.title { margin-bottom: 50px; }

.nav {
    margin-top: 35px;
    text-align: center;
}

.link {
    text-decoration: none;
    padding: 0 9px 2px 8px;
}

.link:hover, .link:focus {
    background: var(--grey);
    color: var(--blue);
}
</style>
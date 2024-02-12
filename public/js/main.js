//  TODO: Review if i should expand on the dispatchInputEvent function, to dispatch various request events instead of only input.
//  TODO: Review if the caller check in dispatchInputEvent is actually required/usefull or not.
// Required to make the banner sticky across all pages
let header, sticky;

// Wait for document to be loaded.
document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        let gebrForm, gebrData;

        /* On scroll code for the title banner */
        window.onscroll = function() { onScroll() };

        header = document.getElementById("title-banner");
        sticky = header.offsetTop;

        // If we are still on the landingpage, init the required code for that page.
        if(window.location.pathname === '/') {
            initLanding();
        // If we are on the user (/gebruik) page,
        } else if(window.location.pathname === '/gebruik') {
            // check if a user update was requested, and process said request;
            if(sessionStorage.updateUser != null && sessionStorage.updateUser) {
                gebrForm = document.getElementById("gebr-data-form");
                gebrData = sessionStorage.gebruiker;
                document.getElementById("gebr-form-input").value = gebrData;

                sessionStorage.removeItem("updateUser");
                gebrForm.submit();
            }

            // Init the required code for the user page
            initGebruik();
        // If we are on the admnin (/beheer) page,
        } else if(window.location.pathname === '/beheer') {
            // check if a user update was requested, and process said request;
            if(sessionStorage.updateUser != null && sessionStorage.updateUser) {
                gebrForm = document.getElementById("gebr-data-form");
                gebrData = sessionStorage.gebruiker;
                document.getElementById("gebr-form-input").value = gebrData;

                sessionStorage.removeItem("updateUser");
                gebrForm.submit();
            }

            // Init the required code for the admin page
            initBeheer();
        // If we are changing a album state, and there is a page reload requested.
        } else if(window.location.pathname == "/albSta") {
            if(localStorage.reloadPage != null && localStorage.reloadPage) {
                // We process said request, and remove the request from storage.
                localStorage.removeItem('reloadPage');
                postForm('/gebruik', localStorage.huidigeSerie);
            }
        // Check for errors during account registration, and redirect accordingly.
        } else if(window.location.pathname == "/register") {
            if(localStorage.userError1 != null) {
                window.location.href = '/#account-maken-pop-in';
            }

            if(localStorage.userError2 != null) {
                window.location.href = '/#account-maken-pop-in';
            }

            if(localStorage.userCreated != null) {
                window.location.href = "/#login-pop-in";
            }
        }
    }
}

// async fetch request to avoid page reloading.
async function fetchRequest(url=null, method=null, data=null ) {
    const response = await fetch(url, {
        method: method,
        body: data
    })

    // return the response.
    return response.json();
}

//  dispatchInputEvent(caller):
//      caller (object) - The event from the caller function, so we can get the callers name.
function dispatchInputEvent(caller) {
    // Create new event
    let inputEvent = new Event('input', {
        'bubbles': true,
        'cancelable': false
    });

    // Check if caller was set, not sure if really required.
    if(caller !== "" || caller !== null || caller !== undefined) {
        // Dispatch event to the right element based on the caller its class name.
        switch(caller.target.className) {
            case "album-bewerken-butt":
                document.getElementById("albumb-form-alb-naam").dispatchEvent(inputEvent);
                document.getElementById("albumb-form-alb-isbn").dispatchEvent(inputEvent);
                return;
            case "serie-maken-subm":
                document.getElementById("seriem-form-serieNaam").dispatchEvent(inputEvent);
                return;
            case "serie-bewerken-butt":
                document.getElementById("serieb-form-serieNaam").dispatchEvent(inputEvent);
                return;
        }
    }
}

// Very simple logoff and page redirect.
function logoff() {
    sessionStorage.clear();
    localStorage.clear();
    window.location.assign('/');
}

// onScroll function
function onScroll() {
    // Remove or add class if scrolling or not, magical CSS does the rest.
    if(window.scrollY > sticky) {
        header.classList.add("sticky");
    } else {
        header.classList.remove("sticky");
    }
}

// postForm(path, param):
//  path (string)   - The path the form needs to be submitted to.
//  param (string)  - The name of the serie that was selected.
function postForm(path, param) {
    let method = 'post', form, hiddenField1, hiddenField2;

    form = document.createElement('form');
    form.setAttribute('method', method);
    form.setAttribute('action', path);

    hiddenField1 = document.createElement('input');
    hiddenField1.hidden = true;
    hiddenField1.setAttribute('name', 'serie-selecteren');
    hiddenField1.setAttribute('value', param);

    hiddenField2 = document.createElement('input');
    hiddenField2.hidden = true;
    hiddenField2.setAttribute('name', 'gebr-email');
    hiddenField2.setAttribute('value', sessionStorage.gebruiker);

    form.appendChild(hiddenField1);
    form.appendChild(hiddenField2);
    document.body.appendChild(form);

    form.submit();
}

// replaceSpecChar(text):
//  text (string)   - The text that needs to be cleaned/filtered.
function replaceSpecChar(text) {
    return text.replaceAll('&amp;', '&').replaceAll('$lt;', '<').replaceAll('&gt;', '>').replaceAll('$quot;', '"').replaceAll('&#039;', "'");
}

// displayMessage(text1, text2):
//  text1 (string)  - The first feedback text that needs to be displayed.
//  text2 (string)  - The second feedback text that needs to be displayed.
function displayMessage(text1="", text2="") {
    let container = document.getElementById("message-pop-in");
    let header1 = document.getElementById("response-message1");
    let header2 = document.getElementById("response-message2");

    if(text1 !== "" || text2 !== "") {
        // Making sure the element is visable on the page
        container.style.display = "block";
        container.style.top = "0%";
        container.style.zIndex = "3";

        if(text1 !== "") { header1.innerHTML = text1; }
        if(text2 !== "") { header2.innerHTML = text2; }

        // Start a timeout to hide the element and remove the feedback text.
        setTimeout( function() {
            container.style.display = "none";
            container.style.top = "-10%";
            container.style.zIndex = "1";
            header1.innerHTML = "";
            header2.innerHTML = "";
        }, 3000);
    }
}
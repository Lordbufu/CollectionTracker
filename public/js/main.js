//  TODO: Review if i should expand on the dispatchInputEvent function, to dispatch various request events instead of only input.
//  TODO: Review if the caller check in dispatchInputEvent is actually required/usefull or not.
// Required to make the banner sticky across all pages
let header, sticky;

// Wait for document to be loaded.
document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        /* On scroll code for the title banner */
        window.onscroll = function() {
            onScroll()
        };

        header = document.getElementById("title-banner");
        sticky = header.offsetTop;

        // If we are still on the landingpage, init the required code for that page.
        if(window.location.pathname === '/') {
            // Required feedback loop, for when account authentication fails.
            if(localStorage.fetchResponse) {
                displayMessage(localStorage.fetchResponse);
                localStorage.removeItem('fetchResponse');
            }
            
            initLanding();
        // If we are on the user (/gebruik) page, we init that the code required there.
        } else if(window.location.pathname === '/gebruik') {
            initGebruik();
        // If we are on the admnin (/beheer) page, we init that the code required there.
        } else if(window.location.pathname === '/beheer') {
            initBeheer();
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
//      caller (object) - The event from the caller function.
function dispatchInputEvent(caller) {
    let inputEvent = new Event('input', {                                                               // Create new input event
        'bubbles': true,
        'cancelable': false
    });

    if(caller !== "" || caller !== null || caller !== undefined) {                                      // Check if caller was set, not sure if really required.
        switch(caller.target.className) {                                                               // Switch the callers element class name.
            case "album-bewerken-butt":
                document.getElementById("albumb-form-alb-naam").dispatchEvent(inputEvent);              // Assign event to the desired input element
                document.getElementById("albumb-form-alb-isbn").dispatchEvent(inputEvent);              // Assign event to the desired input element
                return;                                                                                 // Return to caller.
            case "serie-maken-subm":
                document.getElementById("seriem-form-serieNaam").dispatchEvent(inputEvent);
                return;
            case "serie-bewerken-butt":
                document.getElementById("serieb-form-serieNaam").dispatchEvent(inputEvent);
                return;
        }
    }
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
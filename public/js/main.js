/* Globals for the sticky header/banner */
let header, sticky;

/* Code that triggers when a page is loaded */
document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        /* On scroll code for the title banner */
        window.onscroll = function() { onScroll() };

        header = document.getElementById("title-banner");
        sticky = header.offsetTop;

        /* Page specific init and feedback code */
        if(window.location.pathname === '/') {
            if(localStorage.fetchResponse) {
                displayMessage(localStorage.fetchResponse);
                localStorage.removeItem('fetchResponse');
            }
            
            initLanding();
        } else if(window.location.pathname === '/gebruik') {
            initGebruik();
        } else if(window.location.pathname === '/beheer') { initBeheer(); }
    }
}

/* Fetch function, used in certain cases to avoid a page reload */
async function fetchRequest(url=null, method=null, data=null ) {
    const response = await fetch(url, {
        method: method,
        body: data
    })

    return response.json();
}

/*  dispatchInputEvent(caller):
        This function creates a new event, and dispaches said event, when the expected input is already filled in.
            caller (string) - A string that allows me to see what called the event.
 */
function dispatchInputEvent(caller) {
    let inputEvent = new Event( 'input', { 'bubbles': true, 'cancelable': false } );

    switch(caller) {
        case "album-bew":
            document.getElementById("albumb-form-alb-naam").dispatchEvent(inputEvent);
            document.getElementById("albumb-form-alb-isbn").dispatchEvent(inputEvent);
        case "serie-maken":
            document.getElementById("seriem-form-serieNaam").dispatchEvent(inputEvent);
        case "serie-bew":
            document.getElementById("serieb-form-serieNaam").dispatchEvent(inputEvent);
        case "album-maken":
            document.getElementById("albumt-form-alb-naam").dispatchEvent(inputEvent);
            document.getElementById("albumt-form-alb-isbn").dispatchEvent(inputEvent);
    }
}

/*  saveScroll(e):
        This function saves the vertical scroll position before a page-reload, so the page is set back to that once fully loaded.
        And it also stores a trigger in the browser session storage, to help dispatch events since most code is session based now.
            e (object)  - The event that was assigned to this listen event, used to see what element triggered it.
 */
function saveScroll(e) {
    sessionStorage.setItem("scrollPos", window.scrollY);

    if(e.target.className === "album-bewerken-butt") {
        localStorage.setItem("event", "album-bew");
    } else if(e.target.className === "serie-bewerken-butt") {
        localStorage.setItem("event", "serie-bew");
    } else if(e.target.className === "serie-maken-subm") {
        localStorage.setItem("event", "serie-maken");
    }
}

/*  onScroll():
        Simple function to make the page header/banner sticky or not, depending on the vertical scroll position.
 */
function onScroll() {
    if(window.scrollY > sticky) {
        header.classList.add("sticky");
    } else { header.classList.remove("sticky"); }
}

/*  replaceSpecChar(text):
        This function replaces special characters, so certain names are displayed correctly on the webpage.
            text (string)   - The text that needs to be cleaned/filtered.
 */
function replaceSpecChar(text) {
    return text.replaceAll("&amp;", "&").replaceAll("$lt;", "<").replaceAll("&gt;", ">").replaceAll("$quot;", '"').replaceAll("&#039;", "'");
}

/*  displayMessage(text1, text2):
        This function briefly displays feedback and error messages at the top of the browser window.
        Current timeout is 3 seconds, for now this seems long enough, but i might increase based on feedback.
            text1 (string)  - The first feedback text that needs to be displayed.
            text2 (string)  - The second feedback text that needs to be displayed.
 */
function displayMessage(text1="", text2="") {
    let container = document.getElementById("message-pop-in");
    let header1 = document.getElementById("response-message1");
    let header2 = document.getElementById("response-message2");

    if(text1 !== "" || text2 !== "") {
        container.style.display = "block";
        container.style.top = "0%";
        container.style.zIndex = "3";

        if(text1 !== "") { header1.innerHTML = text1; }
        if(text2 !== "") { header2.innerHTML = text2; }

        setTimeout( function() {
            container.style.display = "none";
            container.style.top = "-10%";
            container.style.zIndex = "1";
            header1.innerHTML = "";
            header2.innerHTML = "";
        }, 3000);
    }
}
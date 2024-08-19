/* Globals for the sticky header/banner */
let header, control, sticky;
let html5QrcodeScanner; // Temp code

/* Code that triggers when a page is loaded */
document.onreadystatechange = () => {
    if( document.readyState === 'complete' ) {
        /* On scroll code for the title banner */
        window.onscroll = function() {
            onScroll()
        };

        header = document.getElementById( "title-banner" );
        control = document.getElementById( "contr-cont" );
        if(header.offsetTop) { sticky = header.offsetTop; }

        if(window.location.pathname === "/beheer" || window.location.pathname === "/beheer") {
            if(control.offsetTop) {
                sticky = control.offsetTop;
            }
        }

        /* Page specific init and feedback code */
        if(window.location.pathname === '/') {
            if( localStorage.fetchResponse ) {
                displayMessage( localStorage.fetchResponse ), localStorage.removeItem( "fetchResponse" );
            }
            return initLanding();
        } else if( window.location.pathname === "/gebruik" ) {
            return initGebruik();
        } else if( window.location.pathname === "/beheer" ) {
            return initBeheer();
        }
    }
}

/* Fetch function, used in certain cases to avoid a page reload */
async function fetchRequest( url, method, data ) {
    const response = await fetch( url, { method: method, body: data } );
    return response.json();
}

/*  dispatchInputEvent(caller):
        This function creates a new event, and dispaches said event, when the expected input is already filled in.
            caller (string) - A string that allows me to see what called the event.
 */
function dispatchInputEvent( caller ) {
    let inputEvent = new Event( "input", { "bubbles": true, "cancelable": false } );
    switch( caller ) {
        case "album-toev":
            return document.getElementById( "albumt-form-alb-naam" ).dispatchEvent( inputEvent ), document.getElementById( "albumt-form-alb-isbn" ).dispatchEvent( inputEvent );
        case "album-bew":
            return document.getElementById( "albumb-form-alb-naam" ).dispatchEvent( inputEvent ), document.getElementById( "albumb-form-alb-isbn" ).dispatchEvent( inputEvent );
        case "serie-maken":
            return document.getElementById( "seriem-form-serieNaam" ).dispatchEvent( inputEvent );
        case "serie-bew":
            return document.getElementById( "serieb-form-serieNaam" ).dispatchEvent( inputEvent );
    }
}

/*  saveScroll(e):
        This function saves the vertical scroll position before a page-reload, so the page is set back to that once fully loaded.
        And it also stores a trigger in the browser session storage, to help dispatch events since most code is session based now.
            e (object)  - The event that was assigned to this listen event, used to see what element triggered it.
 */
function saveScroll( e ) {
    sessionStorage.setItem( "scrollPos", window.scrollY );

    // Forward event if album-bewerken button is pressed, but also if the form-submit button was pressed, or a isbn search was triggered.
    if( e.target.className === "album-bewerken-butt" || e.target.id === "albumb-form-button" || e.target.id === "modal-form-isbnSearch") {
        return localStorage.setItem( "event", "album-bew" );
    // Forward event if album-toevoegen button is pressed, but also if the form-submit button was pressed.
    } else if( e.target.id === "album-toev-subm" || e.target.id === "albumt-form-button" ) {
        return localStorage.setItem( "event", "album-toev" );
    } else if( e.target.className === "serie-bewerken-butt" || e.target.id === "serieb-form-button" ) {
        return localStorage.setItem( "event", "serie-bew" );
    } else if( e.target.className === "serie-maken-subm" || e.target.id === "seriem-form-button" ) {
        return localStorage.setItem( "event", "serie-maken" );
    } else {
        return;
    }
}

/*  onScroll():
        Simple function to make the page header/banner sticky or not, depending on the vertical scroll position.
 */
function onScroll() {
    if( window.scrollY > sticky ) {
        // Ensure the admin controle container also moves when scrolling on the admin page.
        if(window.location.pathname === '/beheer' || window.location.pathname === '/gebruik') {
            control.classList.add( "sticky" );
            control.style.top = "5.5REM";
        }

        return header.classList.add( "sticky" );
    } else {
        // Ensure the admin controle container is reset when we are back at the top of the page.
        if(window.location.pathname === '/beheer' || window.location.pathname === '/gebruik') {
            control.classList.remove( "sticky" );
            control.removeAttribute( "style" );
        }

        return header.classList.remove( "sticky" );
    }
}

/*  replaceSpecChar(text):
        This function replaces special characters, so certain names are displayed correctly on the webpage.
            text (string)   - The text that needs to be cleaned/filtered.
 */
function replaceSpecChar( text ) {
    return text.replaceAll( "&amp;", "&") .replaceAll( "$lt;", "<" ).replaceAll( "&gt;", ">" ).replaceAll( "$quot;", '"' ).replaceAll( "&#039;", "'" );
}

/*  displayMessage(text1, text2):
        This function briefly displays feedback and error messages at the top of the browser window.
        Current timeout is 3 seconds, for now this seems long enough, but i might increase based on feedback.
            text1 (string)  - The first feedback text that needs to be displayed.
            text2 (string)  - The second feedback text that needs to be displayed.
 */
function displayMessage( text1="", text2="" ) {
    const container = document.getElementById( "message-pop-in" ), header1 = document.getElementById( "response-message1" ), header2 = document.getElementById( "response-message2" );

    if( text1 !== "" || text2 !== "" ) {
        container.style.display = "block", container.style.top = "0%", container.style.zIndex = "3";

        if( text1 !== "" ) { header1.innerHTML = text1; }
        if( text2 !== "" ) { header2.innerHTML = text2; }

        return setTimeout( function() {
            container.style.display = "none";
            container.style.top = "-10%";
            container.style.zIndex = "1";
            header1.innerHTML = "";
            header2.innerHTML = "";
        }, 3000 );
    }
}
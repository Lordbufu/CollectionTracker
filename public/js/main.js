/*  The main JavaScript code:
        Here i have collected all functions/variables, that are shared across pages, or incase of variables are required to be set globally.
        All code has been minimized manually, to still be somewhat readable, but as short as possible.
        Most initial issue have been tested for bugs, and corrected if the result was not as expected, so all large changes have been made at this point.
        There are likely way to many 'return' statements, there are to either attempt to reduce processing time, or prevent unexpected loops/results.
 */
let localDevice, html5QrcodeScanne, zoekInp, chb1, chb2, chb3;                              /* Globals for shared scripts */

/* Check the documents ready state, and start loop if ready state is complete. */
document.onreadystatechange = () => {
    if( document.readyState === "complete" ) {
        /* Check if a device tag was passed on from PhP, store that globally, and clean the storage. */
        if( localStorage.device ) {
            localDevice = localStorage.device;
            localStorage.removeItem( "device" );
        }

        /* If the location is the landing page, check if there was a fetch response, display the fetch response message, and delete it from the storage, and trigger the init function. */
        if( window.location.pathname === "/" ) {
            if( localStorage.fetchResponse ) {
                displayMessage( localStorage.fetchResponse );
                localStorage.removeItem( "fetchResponse" );
            }

            initLanding();
            return;
        /* If the location is the user page, trigger the correct init function, and return to caller (optional). */
        } else if( window.location.pathname === "/gebruik" ) {
            /* If the device tag is desktop, we load the static banner/controller script */
            if( localDevice === "desktop" ) {
                initStatic();
            }

            initGebruik();
            return;
        /* If the location is the admin page, trigger the correct init function, and return to caller (optional). */    
        } else if( window.location.pathname === "/beheer" ) {
            /* If the device tag is desktop, we load the static banner/controller script */
            if( localDevice === "desktop" ) {
                initStatic();
            }

            initBeheer();
            return;
        }
    }
}

/*  dispatchInputEvent(caller):
        This function creates a new event, and dispaches said event, when the expected input is already filled in.
            caller (string) - A string that allows me to see what called the event.
 */
function dispatchInputEvent( caller ) {
    /* Create a new basic input event */
    let inputEvent = new Event (
        "input", {
            "bubbles": true,
            "cancelable": false
        }
    );

    /* Switch the caller id from the pop-in, and trigger the required input events, with a return to close the switch. */
    switch( caller ) {
        case "album-toev":
            document.getElementById( "albumt-form-alb-isbn" ).dispatchEvent( inputEvent );
            document.getElementById( "albumt-form-alb-naam" ).dispatchEvent( inputEvent );
            return;
        case "album-bew":
            document.getElementById( "albumb-form-alb-isbn" ).dispatchEvent( inputEvent );
            document.getElementById( "albumb-form-alb-naam" ).dispatchEvent( inputEvent );
            return;
        case "serie-maken":
            document.getElementById( "seriem-form-serieNaam" ).dispatchEvent( inputEvent );
            return;
        case "serie-bew":
            document.getElementById( "serieb-form-serieNaam" ).dispatchEvent( inputEvent );
            return;
    }
}

/*  saveScroll(e):
        This function saves the vertical scroll position before a page-reload, so the page is set back to that once fully loaded.
        And it also stores a trigger in the browser session storage, to help dispatch events since most code is session based now.
            e (object)  - The event that was assigned to this listen event, used to see what element triggered it.
 */
function saveScroll( e ) {
    /* Store the scroll position of the user in the session storage, for later use */
    sessionStorage.setItem( "scrollPos", window.scrollY );

    /* If the function was called from either; .... */
    if( e.target.className === "album-bewerken-butt" || e.target.id === "albumb-form-button" || e.target.id === "modal-form-isbnSearch") {
        localStorage.setItem( "event", "album-bew" );
        return;
    /* If the function was called from either; .... */
    } else if( e.target.id === "album-toev-subm" || e.target.id === "albumt-form-button" ) {
        localStorage.setItem( "event", "album-toev" );
        return;
    /* If the function was called from either; .... */
    } else if( e.target.className === "serie-bewerken-butt" || e.target.id === "serieb-form-button" ) {
        localStorage.setItem( "event", "serie-bew" );
        return;
    /* If the function was called from either; .... */
    } else if( e.target.id === "serie-maken-subm" || e.target.id === "seriem-form-button" ) {
        localStorage.setItem( "event", "serie-maken" );
        return;
    }
}

/*  replaceSpecChar(text):
        This function replaces special characters, so certain names are displayed correctly on the webpage.
            text (string)   - The text that needs to be cleaned/filtered.
 */
function replaceSpecChar( text ) {
    text.replaceAll( "&amp;", "&") .replaceAll( "$lt;", "<" ).replaceAll( "&gt;", ">" ).replaceAll( "$quot;", '"' ).replaceAll( "&#039;", "'" );
    return;
}

/*  displayMessage(text1, text2):
        This function briefly displays feedback and error messages at the top of the browser window.
        Current timeout is 3 seconds, for now this seems long enough, but i might increase based on feedback.
            text1 (string)  - The first feedback text that needs to be displayed.
            text2 (string)  - The second feedback text that needs to be displayed.
 */
function displayMessage( text1 = "", text2 = "" ) {
    /* Get the elements we need, to display the user feedback. */
    const container = document.getElementById( "message-pop-in" );
    const header1 = document.getElementById( "response-message1" )
    const header2 = document.getElementById( "response-message2" );

    /* If either input is not empty, change the main container style to be displayed on screen. */
    if( text1 !== "" || text2 !== "" ) {
        container.style.display = "block";
        container.style.top = "0%";
        container.style.zIndex = "4";

        /* Check witch text has content, and display said text. */
        if( text1 !== "" ) {
            header1.innerHTML = text1;
        }

        if( text2 !== "" ) { 
            header2.innerHTML = text2;
        }

        /* Set a 3 second time-out, and hide the main container after the timeout. */
        setTimeout(
            function() {
                container.style.display = "none";
                container.style.top = "-10%";
                container.style.zIndex = "1";
                header1.innerHTML = "";
                header2.innerHTML = "";
            },
        3000 );
    }
    
    return;
}
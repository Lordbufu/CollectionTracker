/* The init function triggred on page-load. */
function initGebruik() {
    /* Elements and Events for the search controller */
    zoekInp = document.getElementById( "album-zoek-inp" );
    chb1 = document.getElementById( "album-zoek-naam-inp" );
    chb2 = document.getElementById( "album-zoek-nr-inp" );
    chb3 = document.getElementById( "album-zoek-isbn-inp" );
    chb1.addEventListener( "change", checkBoxSearch );
    chb2.addEventListener( "change", checkBoxSearch );
    chb3.addEventListener( "change", checkBoxSearch );
    zoekInp.disabled = true;

    /* Assing a listenEvent to all checkboxes on the page */
    const chBox = document.getElementsByClassName( "album-aanwezig-checkbox" );
    chBoxArr = Array.from( chBox );
    chBoxArr.forEach( ( item, index, arr ) => {
        arr[index].addEventListener( "change", checkBox );
    } );

    /* Required elements and event for the serie-select controller */
    formButt = document.getElementById( "serie-sel-subm" );
    formInput = document.getElementById( "serie-sel" );
    formInput.addEventListener( "change", selectEvent );
    formButt.disabled = true;

    /* Listen Event for the album search option */
    const albZoekInp = document.getElementById( "album-zoek-inp" );
    albZoekInp.addEventListener( "input", albumZoek );

    /* Elements and listen events for the isbn search function */
    const zoekButt = document.getElementById("album-scan-subm");
    zoekButt.addEventListener("click", saveScroll);

    let config = {
        fps: 10
        // supportedScanTypes: [
        //     Html5QrcodeScanType.SCAN_TYPE_CAMERA
        // ]
    };

    html5QrcodeScanner = new Html5QrcodeScanner( "reader", config );
    html5QrcodeScanner.render( onScanSuccess, onScanError );

    /* Loop for detecting, displaying and removing welcome messages for the user */
    if( localStorage.welcome ) {
        displayMessage( localStorage.welcome );
        localStorage.removeItem( "welcome" );
    }
    
    /* Loop for detecting, displaying and removing fetchResponse (populated via the session now) */
    if( localStorage.fetchResponse ) {
        displayMessage( localStorage.fetchResponse );
        localStorage.removeItem( "fetchResponse" );
    }

    /* Restore a previously set checkState */
    if( localStorage.checkState ) {
        /* To read the array, i need to JSON.parse the JSON.string data i stored. */
        document.getElementById( JSON.parse( localStorage.checkState )[0] ).checked = JSON.parse( localStorage.checkState )[1];

        /* Set the correct inner text for the input, based on the stored element id and checkbox state. */
        if( JSON.parse( localStorage.checkState )[0] === "album-zoek-naam-inp" && JSON.parse( localStorage.checkState )[1] ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album naam";
        } else if( JSON.parse( localStorage.checkState )[0] === "album-zoek-nr-inp" && JSON.parse( localStorage.checkState )[1] ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album nummer";
        } else if( JSON.parse( localStorage.checkState )[0] === "album-zoek-isbn-inp" && JSON.parse( localStorage.checkState )[1] ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album isbn";
        }

        /* If the stored checkbox state is false (off), disable the input and change its inner tekst. */
        if( !JSON.parse( localStorage.checkState )[1] ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Selecteer een zoek optie ..";
            zoekInp.disabled = true;
        }

        /* Clear the stored item. */
        localStorage.removeItem( "checkState" );
    }

    /* Trigger for the window scroll position, to restore the window postion after a user action. */
    if( sessionStorage.scrollPos ) {
        window.scrollTo( 0, sessionStorage.scrollPos );
        sessionStorage.removeItem( "scrollPos" );
    }

    /* Make a event for mobile only, so you can view all album details (function is located in mobile-specific.js). */
    if( localDevice === "mobile" ) {
        const nameEl = document.getElementsByClassName( "album-naam" );
        let tempEl = Array.from( nameEl );

        tempEl.forEach( ( item, index, arr ) => {
            arr[index].addEventListener( "click", viewDetails );
        } );
    }
}

/*  selectEvent(e): Enable or Disable form submit button, based on the formInput value from the serie-select dropdown */
function selectEvent(e) {
    if( formInput.value === "" ) {
        formButt.disabled = true;
        return;
    } else {
        formButt.disabled = false;
        return;
    }
}

/*  selectSubm(): Simply submits the serie-select form. */
function selectSubm() {
    document.getElementById( "serie-sel-form" ).submit();
    return;
}

/*  checkBoxSearch(event):
        This function makes sure the search option checkboxes, cant all be active at the same time, and changes the inner input text.
        If there is an input value when changing search options, it will dispatch an input event, so the search code is triggered/updated.
            checkArr    (JS Array)                  - An new JS Array, that stored the element id and checked state of the checkbox that was changed.
            inputEvent  (JS Event)                  - An new JS Event, that can be used to trigger a input event used for the search function.
            checkState  (JS Array -> JSON String)   - The checkArr converted with JSON Stringify, and stored in the local browser storage for later use.
        
        Return Value: None.
 */
function checkBoxSearch( event ) {
    const checkArr = new Array( event.target.id, event.target.checked );
    localStorage.setItem( "checkState", JSON.stringify( checkArr ) );

    if( event.target.checked === true ) {
        let inputEvent = new Event (
            "input", {
                "bubbles": true,
                "cancelable": false
            }
        );

        if( zoekInp.disabled ) {
            zoekInp.disabled = false;
        }

        if( event.target.id === "album-zoek-naam-inp" ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album naam";
            chb2.checked = false;
            chb3.checked = false;

            if( zoekInp.value ) {
                zoekInp.dispatchEvent( inputEvent );
            }
            return;
        } else if( event.target.id === "album-zoek-nr-inp" ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album nummer";
            chb1.checked = false;
            chb3.checked = false;

            if( zoekInp.value ) {
                zoekInp.dispatchEvent( inputEvent );
            }
            return;
        } else if( event.target.id === "album-zoek-isbn-inp" ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album isbn";
            chb1.checked = false;
            chb2.checked = false;
            
            if( zoekInp.value ) {
                zoekInp.dispatchEvent( inputEvent );
            }
            return;
        }
    } else {
        document.getElementById( "album-zoek-span" ).innerHTML = "Selecteer een zoek optie ..";
        zoekInp.disabled = true;
        return;
    }
}

/*  albumZoek(event): Searches the albums on page, matching them on a letter by letter basis. */
function albumZoek( event ) {
    const filter = zoekInp.value.toUpperCase();
    const tafelRows = document.querySelectorAll( "#album-tafel-inhoud" );

    /* We are searching on a name basis */
    if( chb1.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumNaam = item.children[1].innerHTML;

            if( albumNaam.toUpperCase().indexOf( filter ) > -1 ) {
                tafelRows[index].style.display = "";
                return;
            } else {
                tafelRows[index].style.display = "none";
                return;
            }
        } );

    /* We are searching on a album nr basis */
    } else if( chb2.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumNr = item.children[2].innerHTML;

            if( albumNr.toUpperCase().indexOf( filter ) > -1 ) {
                tafelRows[index].style.display = "";
                return;
            } else {
                tafelRows[index].style.display = "none";
                return;
            }
        } );

    /* We are searching on a album isbn basis */
    } else if( chb3.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumIsbn = item.children[5].innerHTML;

            if( albumIsbn.toUpperCase().indexOf( filter ) > -1 ) {
                tafelRows[index].style.display = "";
                return;
            } else {
                tafelRows[index].style.display = "none";
                return;
            }
        } );
    }
}

/*  checkBoc(e): Checkbox listenEvent that simply submits the form. */
function checkBox(e) {
    saveScroll(e);
    e.target.closest( "form" ).submit();
    return;
}

/*  onScanSuccess(decodedText, decodedResult):
        Using the qrcode scanning API, i take the ISBN/EAN number of a barcode.
        And store that in the form, so the backend code can querry google, and then parse any usefull data
 */
function onScanSuccess( decodedText, decodedResult ) {
    formatName = decodedResult["result"]["format"]["formatName"];
    document.getElementById("albumSc-form-isbn").value = decodedText;
    html5QrcodeScanner.clear();
    document.getElementById("modal-form-gebr-scan").submit();
    return;
}

/*  onScanError(errorMessage):
        For now, i simply log the errorMessage, because i dunno how to handle these atm.
 */
function onScanError( errorMessage ) {
    console.log( errorMessage );
    return;
}
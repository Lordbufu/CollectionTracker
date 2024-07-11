//  TODO: Review if i want/need to clear the checkState used for the search options, atm its always there to prevent data loss on page-refreshes (line:56).
/* Global variables required for serie-select controller */
let formButt, formInput, zoekInp, chb1, chb2, chb3;

/* The init function triggred on page-load. */
function initGebruik() {
    /* Test code for the search option checkboxes */
    zoekInp = document.getElementById( "album-zoek-inp" );
    chb1 = document.getElementById( "album-zoek-naam-inp" ), chb1.addEventListener( "change", checkBoxSearch );
    chb2 = document.getElementById( "album-zoek-nr-inp" ), chb2.addEventListener( "change", checkBoxSearch );
    chb3 = document.getElementById( "album-zoek-isbn-inp" ), chb3.addEventListener( "change", checkBoxSearch );

    /* Assing a listenEvent to all checkboxes on the page */
    const chBox = document.getElementsByClassName( "album-aanwezig-checkbox" ), chBoxArr = Array.from( chBox );
    chBoxArr.forEach( ( item, index, arr ) => {
        arr[index].addEventListener( "change", checkBox );
    } );

    /* Required elements and event for the serie-select controller */
    formButt = document.getElementById( "serie-sel-subm" ), formButt.disabled = true;
    formInput = document.getElementById( "serie-sel" ), formInput.addEventListener( "change", selectEvent );

    /* Listen Event for the album search option */
    document.getElementById( "album-zoek-inp" ).addEventListener( "input", albumZoek );

    /* Loop for detecting, displaying and removing welcome messages for the user */
    if( localStorage.welcome ) {
        displayMessage( localStorage.welcome ), localStorage.removeItem( "welcome" );
    }
    
    /* Loop for detecting, displaying and removing fetchResponse (populated via the session now) */
    if( localStorage.fetchResponse ) {
        displayMessage( localStorage.fetchResponse ), localStorage.removeItem( "fetchResponse" );
    }

    /* Restore a previously set checkState */
    if( localStorage.checkState ) {
        // To read the array, i need to JSON.parse the JSON.string data i stored.
        document.getElementById( JSON.parse( localStorage.checkState )[0] ).checked = JSON.parse( localStorage.checkState )[1];

        // Set the correct inner text for the input, based on the stored element id and checkbox state.
        if( JSON.parse( localStorage.checkState )[0] === "album-zoek-naam-inp" && JSON.parse( localStorage.checkState )[1] ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album naam";
        } else if( JSON.parse( localStorage.checkState )[0] === "album-zoek-nr-inp" && JSON.parse( localStorage.checkState )[1] ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album nummer";
        } else if( JSON.parse( localStorage.checkState )[0] === "album-zoek-isbn-inp" && JSON.parse( localStorage.checkState )[1] ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album isbn";
        }

        // If the stored checkbox state is false (off), disable the input and change its inner tekst.
        if( !JSON.parse( localStorage.checkState )[1] ) {
            zoekInp.disabled = true, document.getElementById( "album-zoek-span" ).innerHTML = "Selecteer een zoek optie ..";
        }

        // Clear the stored item.
        //localStorage.removeItem( "checkState" );
    }
}

/*  selectEvent(e): Enable or Disable form submit button, based on the formInput value from the serie-select dropdown */
function selectEvent(e) {
    if( formInput.value === "" ) {
        return formButt.disabled = true;
    } else {
        return formButt.disabled = false;

    }
}

/*  selectSubm(): Simply submits the serie-select form. */
function selectSubm() {
    return document.getElementById( "serie-sel-form" ).submit();
}


//  TODO: Refactoring in progress, concept code finished !!
//          Need to test if this works properly atm, for the most part its copy and pasting of the old code, and while that seems to work it might have unexpected behavior.
// The search function.
/*  albumZoek(event): Searches the albums on page, matching them on a letter by letter basis. */
function albumZoek(event) {
    const filter = zoekInp.value.toUpperCase(), tafelRows = document.querySelectorAll( "#album-tafel-inhoud" );

    // We are searching on a name basis
    if( chb1.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumNaam = item.children[1].innerHTML;
            if( albumNaam.toUpperCase().indexOf( filter ) > -1 ) {
                return tafelRows[index].style.display = "";
            } else {
                return tafelRows[index].style.display = "none";
            }
        } );
    // We are searching on a album nr basis
    } else if( chb2.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumNr = item.children[2].innerHTML;
            if( albumNr.toUpperCase().indexOf( filter ) > -1 ) {
                return tafelRows[index].style.display = "";
            } else {
                return tafelRows[index].style.display = "none";
            }
        } );
    // We are searching on a album isbn basis
    } else if( chb3.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumIsbn = item.children[5].innerHTML;
            if( albumIsbn.toUpperCase().indexOf( filter ) > -1 ) {
                return tafelRows[index].style.display = "";
            } else {
                return tafelRows[index].style.display = "none";
            }
        } );
    }
}

/*  checkBoc(e): Checkbox listenEvent that simply submits the form. */
function checkBox(e) {
    return e.target.closest( "form" ).submit();
}

/*  checkBoxSearch(event):
        This function makes sure the search option checkboxes, cant all be active at the same time, and changes the inner input text.
        If there is an input value when changing search options, it will dispatch an input event, so the search code is triggered/updated.
            checkArr    (JS Array)                  - An new JS Array, that stored the element id and checked state of the checkbox that was changed.
            inputEvent  (JS Event)                  - An new JS Event, that can be used to trigger a input event used for the search function.
            checkState  (JS Array -> JSON String)   - The checkArr converted with JSON Stringify, and stored in the local browser storage for later use.
        
        Return Value: None.
 */
function checkBoxSearch(event) {
    const checkArr = new Array( event.target.id, event.target.checked );
    localStorage.setItem( "checkState", JSON.stringify( checkArr ) );

    if( event.target.checked === true ) {
        let inputEvent = new Event( "input", { "bubbles": true, "cancelable": false } );
        if( zoekInp.disabled ) { zoekInp.disabled = false; }
        
        // Detect what option was selected.
        if( event.target.id === "album-zoek-naam-inp" ) {
            chb2.checked = false, chb3.checked = false, document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album naam";
            // I need to dispatch here, otherwhise i get unexpected results (delay in the event actually triggering).
            if( zoekInp.value ) { zoekInp.dispatchEvent( inputEvent ); }
            return;
        } else if( event.target.id === "album-zoek-nr-inp" ) {
            chb1.checked = false, chb3.checked = false, document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album nummer";
            // I need to dispatch here, otherwhise i get unexpected results (delay in the event actually triggering).
            if( zoekInp.value ) { zoekInp.dispatchEvent( inputEvent ); }
            return;
        } else if( event.target.id === "album-zoek-isbn-inp" ) {
            chb1.checked = false, chb2.checked = false, document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album isbn";
            // I need to dispatch here, otherwhise i get unexpected results (delay in the event actually triggering).
            if( zoekInp.value ) { zoekInp.dispatchEvent( inputEvent ); }
            return;
        }
    } else {
        zoekInp.disabled = true, document.getElementById( "album-zoek-span" ).innerHTML = "Selecteer een zoek optie ..";
        return;
    }


}
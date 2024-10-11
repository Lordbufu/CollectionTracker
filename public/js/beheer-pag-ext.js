/* Globals for the input listen events */
let naamChecked = false, isbnChecked = false;

/* On-pageload init function, triggered from main.js */
function initBeheer() {
    /* Elements, button states and listen events for creating a serie */
    const serieCreateNameInput = document.getElementById( "seriem-form-serieNaam" );
    const serieCreateButt = document.getElementById( "serie-maken-subm" );
    createSerieSubm = document.getElementById( "seriem-form-button" );
    serieCreateNameInput.addEventListener( "input", naamCheck );
    serieCreateButt.addEventListener( "click", saveScroll );
    createSerieSubm.disabled = true;

    /* Elements, button states and listen events for editing a serie */
    const serieEditNameInput = document.getElementById( "serieb-form-serieNaam" );
    const serieBewButt = document.getElementsByClassName( "serie-bewerken-butt" );
    const serieBewButtArr = Array.from( serieBewButt );
    editSerieSubm = document.getElementById( "serieb-form-button" );
    serieEditNameInput.addEventListener( "input", naamCheck );
    editSerieSubm.addEventListener( "click", saveScroll );
    editSerieSubm.disabled = true;

    for( key in serieBewButtArr ) {
        serieBewButtArr[key].addEventListener( "click", saveScroll );
    }

    /* Elements and listen events for removing series */
    const serieVerButt = document.getElementsByClassName( "serie-verwijderen-butt" );
    const serieVerButtArr = Array.from( serieVerButt );

    for( key in serieVerButtArr ) {
        serieVerButtArr[key].addEventListener( "click", saveScroll );
    }

    /* Elements, button states and listen events for creating a album */
    const naamInpToev = document.getElementById( "albumt-form-alb-naam" );
    const isbnInpToev = document.getElementById( "albumt-form-alb-isbn" );
    const coverInp = document.getElementById( "albumt-form-alb-cov" );
    createAlbumSubm = document.getElementById( "albumt-form-button" );
    isbnInpToev.addEventListener( "input", isbnCheck );
    naamInpToev.addEventListener( "input", naamCheck );
    coverInp.addEventListener( "change", coverInpCheck );
    createAlbumSubm.addEventListener( "click", saveScroll );
    createAlbumSubm.disabled = true;

    /* Required elements for editing a album */
    const naamInpBew = document.getElementById( "albumb-form-alb-naam" );
    isbnInpBew = document.getElementById( "albumb-form-alb-isbn" );
    covInpBew = document.getElementById( "albumb-form-alb-cov" );
    albBewButt = document.getElementsByClassName( "album-bewerken-butt" );
    albBewButtArr = Array.from( albBewButt );
    isbnInpBew.addEventListener( "input", isbnCheck );
    naamInpBew.addEventListener( "input", naamCheck );
    covInpBew.addEventListener( "change", coverInpCheck );
    editAlbumSubm = document.getElementById( "albumb-form-button" );
    editAlbumSubm.addEventListener( "click", saveScroll );
    editAlbumSubm.disabled = true;

    for( key in albBewButtArr ) {
        albBewButtArr[key].addEventListener( "click", saveScroll );
    }

    /* Elements and listen events for removing a album */
    const verwButt = document.getElementsByClassName( "album-verwijderen-butt" );
    const verwButtArr = Array.from( verwButt );

    for( key in verwButtArr ) {
        verwButtArr[key].addEventListener( "click", saveScroll );
    }

    /* Elements and listen events for pop-in buttons */
    const modalFormButt = document.getElementsByClassName( "modal-form-button" );
    const modalFormButtArr = Array.from( modalFormButt );

    for( key in modalFormButtArr ) {
        modalFormButtArr[key].addEventListener( "click", saveScroll );
    }

    const popInClButt = document.getElementsByClassName( "modal-header-close" );
    const clButtArr = Array.from( popInClButt );
    
    for( key in clButtArr ) {
        clButtArr[key].addEventListener( "click", saveScroll );
    }

    /* Elements and listen events for the user password reset */
    const resetVeld2 = document.getElementById( "resetVeld2" );
    resetVeld2.addEventListener( "input", pwChecker );
    pwSubButt = document.getElementById( "reset-submit" );
    pwSubButt.disabled = true;

    /* Elements and listen events for the isbn search function */
    const isbnButt = document.getElementById( "album-isbn-search" );
    isbnButt.addEventListener( "click", saveScroll );

    const config = {
        fps: 10,
        supportedScanTypes: [
            Html5QrcodeScanType.SCAN_TYPE_CAMERA
        ]
    };

    html5QrcodeScanner = new Html5QrcodeScanner( "reader", config );
    html5QrcodeScanner.render( onScanSuccess, onScanError );

    /* Triggers based on browser storage variables */
    if( localStorage.welcome ) {
        displayMessage( localStorage.welcome );
        localStorage.removeItem( "welcome" );
    }

    if( localStorage.fetchResponse !== null ) {
        displayMessage( localStorage.fetchResponse );
        localStorage.removeItem( "fetchResponse" );
    }

    if( localStorage.isbnSearch ) {
        if( window.location.hash === "#albumt-pop-in" ) {
            dispatchInputEvent( "album-toev" );
            localStorage.removeItem( "isbnSearch" );
        }

        if( window.location.hash === "#albumb-pop-in" ) {
            dispatchInputEvent( "album-bew" );
            localStorage.removeItem( "isbnSearch" );
        }
    }

    // Requires a review, i dont think i can edit and scan atm ?
    if( localStorage.isbnScan ) {
        if( window.location.hash === "#albumt-pop-in" ) {
            dispatchInputEvent( "album-toev" );
            localStorage.removeItem( "isbnScan" );
        }

        if( window.location.hash === "#albumb-pop-in" ) {
            dispatchInputEvent( "album-bew" );
            localStorage.removeItem( "isbnScan" );
        }
    }

    if( sessionStorage.scrollPos ) {
        window.scrollTo( 0, sessionStorage.scrollPos );

        if( window.location.hash === "#albumb-pop-in" ) {
            dispatchInputEvent( localStorage.event );
        } else if( window.location.hash === "#serieb-pop-in" ) {
            dispatchInputEvent( localStorage.event );
        } else if( window.location.hash === "#albumt-pop-in" ) {
            dispatchInputEvent( localStorage.event );
        } else if( window.location.hash === "#seriem-pop-in" ) {
            dispatchInputEvent( localStorage.event );
        }

        localStorage.removeItem( "event" );
        sessionStorage.removeItem( "scrollPos" );
    }

    /* Listen Event for the album search option */
    const albZoekInp = document.getElementById( "album-zoek-inp" );
    albZoekInp.addEventListener( "input", albumZoek );

    /* Test code for the search option checkboxes */
    zoekInp = document.getElementById( "album-zoek-inp" );
    chb1 = document.getElementById( "album-zoek-naam-inp" );
    chb2 = document.getElementById( "album-zoek-nr-inp" );
    chb3 = document.getElementById( "album-zoek-isbn-inp" );

    chb1.addEventListener( "change", checkBoxSearch );
    chb2.addEventListener( "change", checkBoxSearch );
    chb3.addEventListener( "change", checkBoxSearch )

    if( !zoekInp.disabled ) {
        zoekInp.disabled = true;
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
            document.getElementById( "album-zoek-span" ).innerHTML = "Selecteer een zoek optie ..";
            zoekInp.disabled = true;
        }

        // Clear the stored item.
        localStorage.removeItem( "checkState" );
    }

    /* Test code for the isbn search button */ 
    const searchSubm = document.getElementById( "modal-form-albAdd-isbn-triger" );
    searchSubm.addEventListener( "click", submitIsbnSearch );
}

/*  naamCheck(e):
        This function listens to input changes in serie/album name fields, and evaluates if valid and enables/disabled the submit button.
        It also change the input style, based on the evaluation, and works in tandem with isbnCheck(e) if there is a isbn input field.
            e       - The listen event object.
            uInp    - The user input, set from the listen event object.
            elStyle - The element style of the input element, set from the listen event object.

        External Variables (defined globally, instanced in the init):
            createSerieSubm / editSerieSubm - Create/Edit subm buttons for series.
            createAlbumSubm / editAlbumSubm - Create/Edit subm buttons for albums.
        
        Return Value: None.
 */
function naamCheck( e ) {
    const uInp = e.target.value;
    const elStyle = e.target.style;

    if( uInp !== "" && uInp !== null && uInp !== undefined ) {
        elStyle.outline = "3px solid green";
        naamChecked = true;

        if( e.target.id === "serieb-form-serieNaam" || e.target.id === "seriem-form-serieNaam" ) {
            editSerieSubm.disabled = false;
            createSerieSubm.disabled = false;
            return;
        }

        if( e.target.id === "albumb-form-alb-naam" || e.target.id === "albumt-form-alb-naam" ) {
            if( isbnChecked ) {
                createAlbumSubm.disabled = false;
                editAlbumSubm.disabled = false;
                return;
            }
        }

    } else {
        naamChecked = false;
        editSerieSubm.disabled = true;
        editAlbumSubm.disabled = true;
        createSerieSubm.disabled = true;
        createAlbumSubm.disabled = true;
        elStyle.outline = "3px solid red";
        return;
    }
}

/*  isbnCheck(e):
        This function listens to input changes in album isbn fields, and evaluates if valid and enables/disabled the submit button.
        It also change the input style, based on the evaluation, and works in tandem with naamCheck(e).
            e       - The listen event object.
            uInp    - The user input, set from the listen event object.
            elStyle - The element style of the input element, set from the listen event object.

        External Variables (defined globally, instanced in the init):
            createSerieSubm / editSerieSubm - Create/Edit subm buttons for series.
            createAlbumSubm / editAlbumSubm - Create/Edit subm buttons for albums.

        Return Value: None.
 */
function isbnCheck( e ) {
    const uInp = e.target.value;
    const elStyle = e.target.style;

    if( uInp !== "" && uInp !== null && uInp !== undefined ) {
        const isbn = uInp.replace(/-/g, "");
        const filter = /[a-zA-z]/g;
        const letters = filter.test(isbn);

        if( !letters && isbn === "0" || isbn.length === 10 || isbn.length === 13 ) {
            elStyle.outline = "3px solid green";
            e.target.value = isbn;
            isbnChecked = true;

            if( naamChecked ) {
                createAlbumSubm.disabled = false;
                editAlbumSubm.disabled = false;
                return;
            }

        } else {
            isbnChecked = false;
            editAlbumSubm.disabled = true;
            createAlbumSubm.disabled = true;
            elStyle.outline = "3px solid red";
            return;
        }

    } else {
        isbnChecked = false;
        editAlbumSubm.disabled = true;
        createAlbumSubm.disabled = true;
        elStyle.outline = "3px solid red";
        return;
    }
}

/*  albCovCheck(e):
        This function simply checks the files size, and is triggered with the on-change coverInpCheck.
            e       - The submit button listen event object, passed on via the covInpCheck.
            file    - The file that has been selected by the user.
        
        Return Value: Boolean.
 */
function albCovCheck( e ) {
    const file = e.target.files;

    if( file[0].size > 4096000 ) {
        displayMessage("Bestand is te groot, graag iets van 4MB of kleiner.");
        e.target.value = "";
        return false;
    }

    return true;
}

/*  coverInpCheck(e):
        The Event function for the cover input, to change the preview and text in related pop-ins.
        It also checks if the Image file is not larger then 4MB, using the albCovCheck.
            divCov      - The div container that should include the preview image.
            imageFile   - The uploaded file its temp location (in blob format).
            imgEl       - The new image element for the cover preview.
            labEl       - The label element from the cover input.
        
        Return Value: None.
 */
function coverInpCheck( e ) {
    const imgEl = document.createElement("img");
    const imageFile = e.target.files[0];
    const check = albCovCheck(e);
    let labEl = "";
    let triggerEl = ""

    if( check ) {
        imgEl.src = URL.createObjectURL(imageFile);
        imgEl.className = "modal-album-cover-img";
        imgEl.id = "albumb-cover-img";

        if( e.target.id === "albumb-form-alb-cov" ) {
            const divCov = document.getElementById("albumB-cover");
            divCov.innerHTML = "";
            labEl = document.getElementById("modal-albumb-cov-lab");
            divCov.appendChild(imgEl);
            triggerEl = document.getElementById( "modal-form-albEdit-cov-trigger" )

            if( triggerEl.hidden ) {
                triggerEl.hidden = false;
            }

        } else if( e.target.id === "albumt-form-alb-cov" ) {
            const divCov = document.getElementById("albumT-cover");
            divCov.innerHTML = "";
            divCov.appendChild( imgEl );
            labEl = document.getElementById( "modal-albumt-cov-lab" );
            triggerEl = document.getElementById( "modal-form-albAdd-cov-trigger" );
            triggerEl.hidden = false;
        }

        labEl.innerHTML = "Nieuwe Cover Selecteren";
        labEl.appendChild(e.target);
        return;
    }
}

/*  serieVerwijderen(e:
        A simple confirmation check, that displays the serie name, and triggers the submit button base on said confirmation.
            rowCol  - The table row in witch the button was pressed.
            rowArr  - The table row in array format for easier access.
            conf    - The confirmation box when the button is pressed.

        Return Value: Boolean.
 */
function serieVerwijderen( e ) {
    const rowCol = document.getElementsByClassName( "serie-tafel-inhoud-" + e.target.id );
    const rowArr = Array.from(rowCol);
    const conf = confirm( "Weet u zeker dat de Serie: " + rowArr[0].children[3].innerHTML + "\n En al haar albums wilt verwijderen ?" );

    if( conf ) {
        return true;
    } else {
        if( sessionStorage.scrollPos)  {
            sessionStorage.removeItem("scrollPos");
        }

        return false;
    }
}

/*  wwResetClick(): Redirect to the password reset pop-in. */
function wwResetClick() {
    window.location.assign('#ww-reset-pop-in');
    return;
}

/*  pwChecker(e):
        For visual confirmation, that both password entered are equal, and allowing submit only if they are.
            resetVeld1  - The first password input field.
        
        Return Value: None.
 */
function pwChecker( e ) {
    const resetVeld1 = document.getElementById("resetVeld1");

    if( e.target.value === resetVeld1.value ) {
        e.target.style.outline = "3px solid green";
        pwSubButt.disabled = false;
        return;
    } else {
        e.target.style.outline = "3px solid red";
        pwSubButt.disabled = true;
        return;
    }
}

/*  aResetBev(e):
        This function asks for user confirmation, before submitting the Admin password reset form.
            e       - The submit button listen event object.
            conf    - The result of the user confirmation.

        Return value: Boolean.
 */
function aResetBev( e ) {
    const conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?");

    if( conf ) {
        return true;
    } else {
        return false;
    }
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
            "input",
            {
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

        } else if( event.target.id === "album-zoek-nr-inp" ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album nummer";
            chb1.checked = false;
            chb3.checked = false;

            if( zoekInp.value ) {
                zoekInp.dispatchEvent( inputEvent );
            }

        } else if( event.target.id === "album-zoek-isbn-inp" ) {
            document.getElementById( "album-zoek-span" ).innerHTML = "Zoek op album isbn";
            chb1.checked = false;
            chb2.checked = false;

            if( zoekInp.value ) {
                zoekInp.dispatchEvent( inputEvent );
            }

        }

    } else {
        document.getElementById( "album-zoek-span" ).innerHTML = "Selecteer een zoek optie ..";
        zoekInp.disabled = true;
    }

    return;
}

//  TODO: Refactoring in progress, concept code finished !!
//          Need to test if this works properly atm, for the most part its copy and pasting of the old code, and while that seems to work it might have unexpected behavior.
// The search function.
/*  albumZoek(event): Searches the albums on page, matching them on a letter by letter basis. */
function albumZoek( event ) {
    const filter = zoekInp.value.toUpperCase();
    const tafelRows = document.querySelectorAll( "#album-tafel-inhoud" );

    // We are searching on a name basis
    if( chb1.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumNaam = item.children[2].innerHTML;

            if( albumNaam.toUpperCase().indexOf( filter ) > -1 ) {
                tafelRows[index].style.display = "";
                return;
            } else {
                tafelRows[index].style.display = "none";
                return;
            }

        } );

    // We are searching on a album nr basis
    } else if( chb2.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumNr = item.children[3].innerHTML;

            if( albumNr.toUpperCase().indexOf( filter ) > -1 ) {
                tafelRows[index].style.display = "";
                return;
            } else {
                tafelRows[index].style.display = "none";
                return;
            }

        } );

    // We are searching on a album isbn basis
    } else if( chb3.checked === true ) {
        tafelRows.forEach( ( item, index ) => {
            const albumIsbn = item.children[7].innerHTML;

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

// Test code for the qr scanner
//  TODO: Figure out how to properly use the scan information, and see how i can combine that with my PhP Isbn class.
//          And also figure out what todo when a code is not useable.
function onScanSuccess( decodedText, decodedResult ) {
    formatName = decodedResult["result"]["format"]["formatName"];
    document.getElementById("albumS-form-isbn").value = decodedText;
    html5QrcodeScanner.clear();
    //console.log( formatName );
    document.getElementById("modal-form-scan").submit();
    return;
}

//  TODO: Figure out what todo with the errorMesage, for now i just console log it.
//          Log the error for now, so i can debug issues a bit better.
function onScanError( errorMessage ) {
    console.log( errorMessage );
    return;
}

/*  submitIsbnSearch(e):
        This function adds to inputs to a extra form, fills in the data from the hidden form.
        And then submits the extra form, so the hidden data can be submitted to search for isbn data.
 */
function submitIsbnSearch( e ) {
    const formEl = document.getElementById( "isbn-trigger-form" );
    const inputInEl = document.createElement("input");
    const inputIsbnEl = document.createElement("input");

    inputInEl.setAttribute( "hidden", true );
    inputInEl.setAttribute( "name", "serie-index" );
    inputInEl.setAttribute( "value", document.getElementById( "modal-form-hidden" ).value );

    inputIsbnEl.setAttribute( "hidden", true );
    inputIsbnEl.setAttribute( "name", "album-isbn" );
    inputIsbnEl.setAttribute( "value", document.getElementById( "albumt-form-alb-isbn" ).value );

    formEl.appendChild(inputInEl);
    formEl.appendChild(inputIsbnEl);
    formEl.submit();
    return;
}
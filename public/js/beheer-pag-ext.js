/* Globals for the input listen events */
let naamChecked = false, isbnChecked = false;
let createAlbumSubm, editAlbumSubm, createSerieSubm, editSerieSubm;

/* On-pageload init function, triggered from main.js */
function initBeheer() {
    /* Elements, button states and listen events for creating a serie */
    const serieCreateNameInput = document.getElementById("seriem-form-serieNaam");
    const serieCreateButt = document.getElementById("serie-maken-subm");
    createSerieSubm = document.getElementById("seriem-form-button");
    serieCreateNameInput.addEventListener("input", naamCheck);
    serieCreateButt.addEventListener("click", saveScroll);
    createSerieSubm.disabled = true;

    /* Elements, button states and listen events for editing a serie */
    const serieEditNameInput = document.getElementById("serieb-form-serieNaam");
    const serieBewButt = document.getElementsByClassName("serie-bewerken-butt");
    const serieBewButtArr = Array.from(serieBewButt);
    editSerieSubm = document.getElementById("serieb-form-button");
    serieEditNameInput.addEventListener("input", naamCheck);
    editSerieSubm.disabled = true;

    for(key in serieBewButtArr) { serieBewButtArr[key].addEventListener("click", saveScroll); }

    /* Elements and listen events for removing series */
    const serieVerButt = document.getElementsByClassName("serie-verwijderen-butt");
    const serieVerButtArr = Array.from(serieVerButt);

    for(key in serieVerButtArr) { serieVerButtArr[key].addEventListener("click", saveScroll); }

    /* Elements, button states and listen events for creating a album */
    const naamInpToev = document.getElementById("albumt-form-alb-naam");
    const isbnInpToev = document.getElementById("albumt-form-alb-isbn");
    const coverInp = document.getElementById("albumt-form-alb-cov");
    createAlbumSubm = document.getElementById("albumt-form-button");
    isbnInpToev.addEventListener("input", isbnCheck);
    naamInpToev.addEventListener("input", naamCheck);
    coverInp.addEventListener("change", coverInpCheck);
    createAlbumSubm.addEventListener("click", saveScroll);
    createAlbumSubm.disabled = true;

    /* Required elements for editing a album */
    editAlbumSubm = document.getElementById("albumb-form-button");
    const naamInpBew = document.getElementById("albumb-form-alb-naam");
    const isbnInpBew = document.getElementById("albumb-form-alb-isbn");
    const covInpBew = document.getElementById('albumb-form-alb-cov');
    const albBewButt = document.getElementsByClassName("album-bewerken-butt");
    const albBewButtArr = Array.from(albBewButt);
    isbnInpBew.addEventListener("input", isbnCheck);
    naamInpBew.addEventListener("input", naamCheck);
    covInpBew.addEventListener("change", coverInpCheck);
    editAlbumSubm.disabled = true;

    for(key in albBewButtArr) { albBewButtArr[key].addEventListener("click", saveScroll); }

    /* Elements and listen events for removing a album */
    const verwButt = document.getElementsByClassName("album-verwijderen-butt");
    const verwButtArr = Array.from(verwButt);

    for(key in verwButtArr) { verwButtArr[key].addEventListener("click", saveScroll); }

    /* Elements and listen events for buttons */
    const modalFormButt = document.getElementsByClassName("modal-form-button");
    const modalFormButtArr = Array.from(modalFormButt);

    for(key in modalFormButtArr) { modalFormButtArr[key].addEventListener("click", saveScroll); }

    const popInClButt = document.getElementsByClassName("modal-header-close");
    const clButtArr = Array.from(popInClButt);

    for(key in clButtArr) { clButtArr[key].addEventListener("click", saveScroll); }

    /* Triggers based on browser storage variables */
    if(localStorage.welcome) {
        displayMessage(localStorage.welcome);
        localStorage.removeItem("welcome");
    }

    if(localStorage.fetchResponse !== null) {
        displayMessage(localStorage.fetchResponse);
        localStorage.removeItem("fetchResponse");
    }

    if(sessionStorage.scrollPos) {
        window.scrollTo(0, sessionStorage.scrollPos);

        if(window.location.hash === "#albumb-pop-in") {
            dispatchInputEvent(localStorage.event);
        } else if(window.location.hash === "#serieb-pop-in") {
            dispatchInputEvent(localStorage.event);
        } else if(window.location.hash === "#albumt-pop-in") {
            dispatchInputEvent(localStorage.event);
        } else if(window.location.hash === "#seriem-pop-in") {
            dispatchInputEvent(localStorage.event);
        }

        localStorage.removeItem("event");
        sessionStorage.removeItem("scrollPos");
    }
}

/*  naamCheck(e):
        This function listens to input changes in serie/album name fields, and evaluates if valid and enables/disabled the submit button.
        It also change the input style, based on the evaluation, and works in tandem with isbnCheck(e).
            e       - The listen event object.
            uInp    - The user input, set from the listen event object.
            elStyle - The element style of the input element, set from the listen event object.

        External Variables (defined globally, instanced in the init):
            createSerieSubm / editSerieSubm - Create/Edit subm buttons for series.
            createAlbumSubm / editAlbumSubm - Create/Edit subm buttons for albums.
        
        Return Value: None.
 */
function naamCheck(e) {
    const uInp = e.target.value;
    let elStyle = e.target.style;

    if(uInp !== "" && uInp !== null && uInp !== undefined) {
        elStyle.outline = "3px solid green";
        naamChecked = true;

        if(isbnChecked) {
            createAlbumSubm.disabled = false, editAlbumSubm.disabled = false;
        } else { createAlbumSubm.disabled = true, editAlbumSubm.disabled = true, createSerieSubm.disabled = false, editSerieSubm.disabled = false; }

    } else {
        elStyle.outline = "3px solid red";
        naamChecked = false, editAlbumSubm.disabled = true, createAlbumSubm.disabled = true, createSerieSubm.disabled = true, editSerieSubm.disabled = true;
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
function isbnCheck(e) {
    const uInp = e.target.value;
    let elStyle = e.target.style;

    if(uInp !== "" && uInp !== null && uInp !== undefined) {
        const isbn = uInp.replace(/-/g, ""), filter = /[a-zA-z]/g, letters = filter.test(isbn);

        if(!letters) {
            if(isbn === '0' || isbn.length === 10 || isbn.length === 13) {
                elStyle.outline = "3px solid green", e.target.value = isbn, isbnChecked = true;
                
                if(naamChecked) { createAlbumSubm.disabled = false, editAlbumSubm.disabled = false; }

            } else { elStyle.outline = "3px solid red", createAlbumSubm.disabled = true, editAlbumSubm.disabled = true, isbnChecked = false; }

        } else { elStyle.outline = "3px solid red", createAlbumSubm.disabled = true, editAlbumSubm.disabled = true, isbnChecked = false; }

    } else { elStyle.outline = "3px solid red", createAlbumSubm.disabled = true, editAlbumSubm.disabled = true, isbnChecked = false; }
}

// I check if the cover file size isnt larger then 4MB
function albCovCheck(e) {
    const file = e.target.files;

    if(file[0].size > 4096000) {
        displayMessage("Bestand is te groot, graag iets van 4MB of kleiner.");
        e.target.value = "";
        return false;
    }

    return true;
}

/*  coverInpCheck(e):
        The Event function for the cover input, to change the preview and text in related pop-ins.
            divCov      - The div container that should include the preview image.
            imageFile   - The uploaded file its temp location (in blob format).
            imgEl       - The new image element for the cover preview.
            labEl       - The label element from the cover input.
        
        Return Value: None.
 */
function coverInpCheck(e) {
    const imgEl = document.createElement('img'), imageFile = e.target.files[0], check = albCovCheck(e);
    let labEl;

    if(check) {
        imgEl.src = URL.createObjectURL(imageFile);
        imgEl.id = "albumb-cover-img";
        imgEl.className = "modal-album-cover-img";

        if(e.target.id === "albumb-form-alb-cov") {
            const divCov = document.getElementById("albumB-cover");
            labEl = document.getElementById("modal-form-albumB-cov-lab");
            divCov.appendChild(imgEl);
        } else if(e.target.id === "albumt-form-alb-cov") {
            const divCov = document.getElementById("albumT-cover");
            labEl = document.getElementById("modal-form-albumt-cov-lab");
            divCov.appendChild(imgEl);
        }

        labEl.innerHTML = "Nieuwe Cover Selecteren";
        labEl.appendChild(e.target);
    }
}

/*  serieVerwijderen(e:
        A simple confirmation check, that displays the serie name, and triggers the submit button base on said confirmation.
            rowCol  - The table row in witch the button was pressed.
            rowArr  - The table row in array format for easier access.
            conf    - The confirmation box when the button is pressed.

        Return Value: Boolean.
 */
function serieVerwijderen(e) {
    const rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    const rowArr = Array.from(rowCol);
    const conf = confirm("Weet u zeker dat de Serie: " + rowArr[0].children[3].innerHTML + "\n En al haar albums wilt verwijderen ?");

    if(conf) {
        return true;
    } else {
        // Ensure the scrollPos is removed, as its obsolete in this case.
        if(sessionStorage.scrollPos) { sessionStorage.removeItem("scrollPos"); }
        return false;
    }
}

/*  wwResetClick(): Redirect to the password reset pop-in. */
function wwResetClick() { window.location.assign('#ww-reset-pop-in') }

// Password reset pop-in button
function aResetBev(e) {
    const conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?");

    if(conf) {
        return true;
    } else { return false; }
}

// OLD CODE THAT IS DEPRICATED NOW
    // OBSOLETE CONSTRUCTOR CODE:
    //  TODO: Needs some kind of trigger, that also checks the input on pageload rather then only input change.
    //      Removed for now, untill a proper solution has been found.
    //const inpTIndex = document.getElementById("albumt-form-indexT"); I think this one is obsolete now ?
    // Elements, states and events required for editing a serie.
    // let serieEditNameInput = document.getElementById("serieb-form-serieNaam");
    // editSerieSubm = document.getElementById("serieb-form-button");
    // editSerieSubm.disabled = true;
    // serieEditNameInput.addEventListener('input', naamCheck);

    // Check for the series name error from the controller
    // if(localStorage.sNaamFailed !== null) {
    //     displayMessage(localStorage.sNaamFailed);
    //     localStorage.removeItem('sNaamFailed');
    // }

    // Ensure the series index is also used for adding a album to a series.
    // if(localStorage.albumToevIn != null) {
    //     inpTIndex.value = localStorage.albumToevIn;
    //     localStorage.removeItem('albumToevIn');
    // }

    // Check if create serie had duplication issues
    // if(localStorage.makers) {
    //     const nameInp = document.getElementById("seriem-form-serieNaam");
    //     const makerInp = document.getElementById("seriem-form-makers");
    //     const commentInp = document.getElementById("seriem-form-opmerking");

    //     nameInp.value = localStorage.serieNaam;
    //     makerInp.value = localStorage.makers;
    //     commentInp.value = localStorage.opmerking;

    //     localStorage.removeItem("serieNaam");
    //     localStorage.removeItem("makers");
    //     localStorage.removeItem("opmerking");
    // }

    // Check if there was error and returned input data with creating a album, and repopulate the form with said data.
    // if( localStorage.getItem("album-nummer") && window.location.hash === "#albumt-pop-in" ) {
    //     const tempForm = document.getElementById("albumt-form");
    //     const arrayForm = Array.from(tempForm);

    //     // Check what input was returned, and set them in the associated fields.
    //     if(localStorage.getItem("album-naam")) { arrayForm[1].value = localStorage.getItem("album-naam"); }
    //     if(localStorage.getItem("album-nummer")) { arrayForm[2].value = localStorage.getItem("album-nummer"); }
    //     if(localStorage.getItem("album-datum")) { arrayForm[3].value = localStorage.getItem("album-datum"); }
    //     if(localStorage.getItem("album-isbn")) { arrayForm[5].value = localStorage.getItem("album-isbn"); }

    //     // Remove the items from the browser storage
    //     localStorage.removeItem("album-naam");
    //     localStorage.removeItem("album-nummer");
    //     localStorage.removeItem("album-datum");
    //     localStorage.removeItem("album-isbn");

    //     // The serie-index is also part of the returned POST data, so we remove that aswell for now.
    //     localStorage.removeItem("serie-index");
    // }

    // OBSOLETE CODE FROM THE REST OF THE PAGE:
    // Function to close pop-ins while on the '/beheer' page.
    // function popInClose() {
    //     // If a series index was stored, i need to request a new view from PhP
    //     if(localStorage.huidigeIndex !== "" && localStorage.huidigeIndex !== null && localStorage.huidigeIndex !== undefined) {
    //         let serieNaam = document.getElementById('beheer-albView-text').innerHTML;

    //         let form = document.createElement('form');
    //         form.setAttribute('method', 'post');
    //         form.setAttribute('action', '/beheer');

    //         let fInp1 = document.createElement('input');
    //         fInp1.setAttribute('type', 'text');
    //         fInp1.setAttribute('name', 'serie-index');
    //         fInp1.setAttribute('value', localStorage.huidigeIndex);
    //         fInp1.hidden = true;

    //         let fInp2 = document.createElement('input');
    //         fInp2.setAttribute('type', 'text');
    //         fInp2.setAttribute('name', 'serie-naam');
    //         fInp2.setAttribute('value', serieNaam);
    //         fInp2.hidden = true;

    //         form.appendChild(fInp1);
    //         form.appendChild(fInp2);
    //         document.body.appendChild(form);

    //         form.submit();
    //     // If there wasnt, we can just return to the '/beheer' page
    //     } else {
    //         window.location.assign('/beheer');
    //     }
    // }

    // Create series controller button
    // function serieSelSubmit(e) {
    //     // Get input, create new FormData, and prevent the default submit.
    //     let inp = document.getElementById("serie-maken-inp");
    //     let formData = new FormData();
    //     e.preventDefault();

    //     // If there is input selected, add input to FormData and send said input to PhP,
    //     if(inp.value !== "") {
    //         formData.append('naam-check', inp.value);

    //         fetchRequest('serieM', 'POST', formData)
    //         // Check for errors, and provide feedback about said errors.
    //         .then((data) => {
    //             if(data !== "Serie-Maken") {
    //                 displayMessage(data);
    //             // If no errors
    //             } else {
    //                 // cast serie name into the form input first,
    //                 document.getElementById('seriem-form-serieNaam').value = inp.value;
    //                 // then dispatch the event so the serie name is actually checked,
    //                 dispatchInputEvent(e);
    //                 // and then redirect to the pop-in.
    //                 window.location.assign('/beheer#seriem-pop-in');
    //             }
    //         })
    //     // If there was no input selection for some reason, we provide user feedback.
    //     } else {
    //         displayMessage("Zonder opgegeven naam, kan er geen serie gemaakt worden!");
    //     }
    // }

    // Creat series pop-in button
    // function serieMakSubm(e) {
    //     // Get form element, create new FormData from it, and prevent the default submit.
    //     let form = document.getElementById('seriem-form');
    //     let formData = new FormData(form);
    //     e.preventDefault();

    //     // Send form to PhP, check for error, store user feedback, and redirect back to the main view.
    //     fetchRequest('serieM', 'POST', formData)
    //     .then((data) => {
    //         if(typeof(data) !== "object") {
    //             localStorage.setItem('fetchResponse', data);
    //             window.location.assign('/beheer');
    //         } else {
    //             displayMessage(data['Serie_Naam']);
    //         }
    //     })
    // }

    // View Series button
    // function serieBekijken(e) {
    //     // Get the required elements, and set the series data in the hidden form.
    //     let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    //     let rowArr = Array.from(rowCol);
    //     let inp1 = document.getElementById("serie-bekijken-form-index-"+e.target.id);
    //     inp1.value = e.target.id;

    //     // Trigger submit based on if there was a valid input.
    //     if(inp1.value === "" && inp2.value === "") {
    //         return false;
    //     } else {
    //         return true;
    //     }
    // }

    // Edit serie controller button
    // function serieBewerken(e) {
    //     // Get the current table row, and the serie-bewerken pop-in form
    //     let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    //     let rowArr = Array.from(rowCol);
    //     let form = document.getElementById('serieb-form');

    //     // Cast row data into the form inputs, and redirect to said form.
    //     form[0].value = replaceSpecChar(rowArr[0].children[3].innerHTML);
    //     form[1].value = e.target.id;
    //     form[2].value = replaceSpecChar(rowArr[0].children[4].innerHTML);
    //     form[3].value = replaceSpecChar(rowArr[0].children[5].innerHTML);

    //     // Dispatch event to 
    //     dispatchInputEvent(e);

    //     window.location.assign('#serieb-pop-in');
    // }

    // Edit serie pop-in button
    // function serieBewSubm(e) {
    //     // Get form element, create FormData from it, and prevent the default submit.
    //     let form = document.getElementById("serieb-form");
    //     let formData = new FormData(form);
    //     e.preventDefault();
        
    //     // Send FormData to PhP, check for errors, store the user feedback and redirect to default view.
    //     fetchRequest('serieBew', 'POST', formData)
    //     .then((data) => {
    //         if(typeof data != 'object') {
    //             localStorage.setItem('fetchResponse', data);
    //             window.location.assign('/beheer');
    //         } else {
    //             displayMessage(data['Serie_Naam']);
    //         }
    //     })
    // }

    // Add album function to open the pop-in
    // function albumToevInv() {
    //     let uInp = document.getElementById("album-toev").value;
    //     let ind = localStorage.huidigeIndex;

    //     // If there is a series index, i save it in the pop-in form, along with the name.
    //     if(ind !== "" || ind !== null || ind !== undefined) {
    //         document.getElementById("albumt-form-indexT").value = localStorage.huidigeIndex;
    //         document.getElementById("albumt-form-seNaam").value = uInp;
    //     // If there isnt i just pass on the serie name.
    //     } else {
    //         document.getElementById("albumt-form-seNaam").value = uInp;
    //     }

    //     // If a selection is made i open the pop-in, if not is give user feedback.
    //     if(uInp !== "" && uInp !== null && uInp !== undefined) {
    //         window.location.assign('/beheer#albumt-pop-in');
    //     } else {
    //         displayMessage("U kan geen album toevoegen, als u geen selectie maakt!");
    //     }
    // }

    // Add Album Submit function
    // function albumToevSubm(e) {
    //     // Get form, store as FormData, and prevent the default form submit
    //     let form = document.getElementById('albumt-form');
    //     let formData = new FormData(form);
    //     e.preventDefault();

    //     // Send fetch request to PhP, check for errors, store user feedback before closing the pop-in
    //     fetchRequest('albumT', 'POST', formData)
    //     .then((data) => {
    //         if(typeof data != 'object') {
    //             localStorage.setItem('fetchResponse', data);
    //             popInClose();
    //         // If we have errors, we ensure they are all displayed properly.
    //         } else {
    //             if(data.aNaamFailed != "") {
    //                 if(data.aIsbnFailed != "") {
    //                     displayMessage(data.aNaamFailed, data.aIsbnFailed);
    //                 } else {
    //                     displayMessage(data.aNaamFailed);
    //                 }
    //             } else {
    //                 displayMessage(data.aIsbnFailed);
    //             }
    //         }
    //     })
    // }

    // Remove Album function.
    // function albumVerwijderen(e) {
    //     // Get all Album info, and make new empty FormData
    //     let rowCol = document.getElementsByClassName('album-bewerken-inhoud-'+e.target.id);
    //     let rowArr = Array.from(rowCol);
    //     let formData = new FormData();
    //     let conf = confirm("Weet u zeker dat u het album: " + rowArr[0].children[2].innerHTML + " wilt verwijderen ?");

    //     // Add relevant album info to the FormData
    //     formData.append('serie-index', localStorage.huidigeIndex);
    //     formData.append('album-index', e.target.id);
    //     formData.append('album-naam', rowArr[0].children[2].innerHTML);

    //     if(conf) {
    //         // Send Request to PhP
    //         fetchRequest('albumV', 'POST', formData)
    //         // Store the response and request a page reload for the current view.
    //         .then((data) => {
    //             localStorage.setItem('fetchResponse', data);
                        
    //             let form = document.createElement('form');
    //             form.setAttribute('method', 'post');
    //             form.setAttribute('action', '/beheer');

    //             let fInp1 = document.createElement('input');
    //             fInp1.setAttribute('type', 'text');
    //             fInp1.setAttribute('name', 'serie-index');
    //             fInp1.setAttribute('value', localStorage.huidigeIndex);

    //             let fInp2 = document.createElement('input');
    //             fInp2.setAttribute('type', 'text');
    //             fInp2.setAttribute('name', 'serie-naam');
    //             fInp2.setAttribute('value', document.getElementById('beheer-albView-text').innerHTML);

    //             form.appendChild(fInp1);
    //             form.appendChild(fInp2);
    //             document.body.appendChild(form);

    //             form.submit();
    //         });
    //     }
    // }

    // Edit Album button
    // function albumBewerken(e) {
    //     // Load all data required to display the current album info
    //     let rowCol = document.getElementsByClassName("album-bewerken-inhoud-"+e.target.id);
    //     let rowArr = Array.from(rowCol);
    //     let div = rowArr[0].children[5];
    //     let form = document.getElementById("albumb-form");
    //     let covLab = document.getElementById("modal-form-albumB-cov-lab");
    //     let covInp = document.getElementById("modal-form-albumB-cov-lab").children[0];
    //     let albCov = document.getElementById("albumb-cover");

    //     // Enable the button by default
    //     editAlbumSubm.disabled = false;

    //     // Inject all album info in to the pop-in form.
    //     form[0].value = e.target.id;
    //     form[1].value = rowArr[0].children[2].innerHTML;
    //     form[2].value = rowArr[0].children[3].innerHTML;
    //     form[3].value = rowArr[0].children[4].innerHTML;
    //     form[4].value = "";
    //     form[5].value = rowArr[0].children[6].innerHTML;
    //     form[6].value = rowArr[0].children[7].innerHTML;

    //     dispatchInputEvent(e);

    //     // Extra check for the album cover.
    //     if(rowArr[0].children[5].innerHTML.trim() != "") {
    //         let imgEl = document.createElement('img');
    //         imgEl.src = div.children[0].src;
    //         imgEl.id = "albumb-cover-img";
    //         imgEl.className = "modal-album-cover-img";
    //         albCov.appendChild(imgEl);
    //         covLab.innerHTML = "Nieuwe Cover Selecteren";
    //         covLab.appendChild(covInp);
    //     } else {
    //         albCov.innerHTML = "Geen cover gevonden, u kunt een cover selecteren, maar dit is niet verplicht.";
    //         covLab.innerHTML = "Album Cover Selecteren";
    //         covLab.appendChild(covInp);
    //     }

    //     // Display the pop-in.
    //     window.location.assign('#albumb-pop-in');
    // }

    // Edit Album pop-in submit button.
    // function albumBewSubm(e) {
    //     // Get form, create FormData from it, and prevent the default form submit.
    //     let form = document.getElementById('albumb-form');
    //     let formData = new FormData(form);
    //     e.preventDefault();

    //     // Send request to PhP, check for errors, and store user feedback before closing the pop-in
    //     fetchRequest('albumBew', 'POST', formData)
    //     .then((data) => {
    //         if(typeof(data) !== 'object') {
    //             localStorage.setItem('fetchResponse', data);
    //             popInClose();
    //         // If there are errors, display them properly to the user.
    //         } else {
    //             if(data.albumNaam != "") {
    //                 if(data.albumIsbn != "") {
    //                     displayMessage(data.albumNaam, data.albumIsbn);
    //                 } else {
    //                     displayMessage(data.albumNaam);
    //                 }
    //             } else {
    //                 displayMessage(data.albumIsbn);
    //             }
    //         }
    //     });
    // }
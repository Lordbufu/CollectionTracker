// TODO: Review if i can off-load form creation and submit functions to a gobal function in main.js
// Check states for name and isbn user inputs.
let naamChecked = false, isbnChecked = false;
// Create and edit series/albums form submit buttons.
let createAlbumSubm, editAlbumSubm, createSerieSubm, editSerieSubm;
// Elements for switching between serie/album view mode.
let serieView, albView, titleCont, buttBan, backButt;

// Default page init function
function initBeheer() {
    /* All elementens for viewing series */
    titleBan = document.getElementById("title-banner");
    titleCont = document.getElementById("title-cont");
    buttCont = document.getElementById("title-buttons");
    backButt = document.getElementById("beheer-back-butt");
    serieView = document.querySelector("#beheer-weerg-repl-cont");
    albView = document.querySelector("#beheer-albView-content-container");
    backButt.style.display = 'none';

    // If the admin wants to view a Series
    if(localStorage.serieWeerg) {
        // I just replace the entire container
        serieView.replaceWith(albView);
        // I enable the back button, and set it to a fixed position.
        backButt.style.display = 'flex';

        // I check if the serie name is stored, and replace the title of the container, and remove the storage
        if(localStorage.huidigeSerie != null) {
            document.getElementById('beheer-albView-text').innerHTML = localStorage.huidigeSerie;
            localStorage.removeItem('huidigeSerie');
        }

        // remove the serieWeerg and index item as well.
        localStorage.removeItem("serieWeerg");
    }

    // Elements, states and events required for creating a serie.
    let serieCreateNameInput = document.getElementById('seriem-form-serieNaam');
    createSerieSubm = document.getElementById("seriem-form-button");
    createSerieSubm.disabled = true;
    serieCreateNameInput.addEventListener('input', naamCheck);

    // Elements, states and events required for editing a serie.
    let serieEditNameInput = document.getElementById("serieb-form-serieNaam");
    editSerieSubm = document.getElementById("serieb-form-button");
    editSerieSubm.disabled = true;
    serieEditNameInput.addEventListener('input', naamCheck);

    // Check for the series name error from the controller
    if(localStorage.sNaamFailed !== null) {
        displayMessage(localStorage.sNaamFailed);
        localStorage.removeItem('sNaamFailed');
    }

    // Elements required for adding and editing albums.
    createAlbumSubm = document.getElementById("albumt-form-button");
    let inpTIndex = document.getElementById("albumt-form-indexT");
    let naamInpToev = document.getElementById("albumt-form-alb-naam");
    let isbnInpToev = document.getElementById("albumt-form-alb-isbn");
    editAlbumSubm = document.getElementById("albumb-form-button");
    let naamInpBew = document.getElementById("albumb-form-alb-naam");
    let isbnInpBew = document.getElementById("albumb-form-alb-isbn");
    let albCovInp = document.getElementById('albumb-form-alb-cov');

    // Elements and events associated with adding and editing albums.
    isbnInpToev.addEventListener("input", isbnCheck);
    naamInpToev.addEventListener("input", naamCheck);
    createAlbumSubm.disabled = true;
    isbnInpBew.addEventListener("input", isbnCheck);
    naamInpBew.addEventListener("input", naamCheck);
    editAlbumSubm.disabled = true;
    albCovInp.addEventListener('change', albCovCheck);

    // Ensure the series index is also used for adding a album to a series.
    if(localStorage.albumToevIn != null) {
        inpTIndex.value = localStorage.albumToevIn;
        localStorage.removeItem('albumToevIn');
    }

    // Display and remove the welcome message on login.
    if(localStorage.welcome) {
        displayMessage(localStorage.welcome);
        localStorage.removeItem("welcome");
    }

    // Display feedback messages, that are stored before a page refresh.
    if(localStorage.fetchResponse !== null) {
        displayMessage(localStorage.fetchResponse);
        localStorage.removeItem("fetchResponse");
    }
}

// Check the name input
function naamCheck(e) {
    // Get user input,
    let uInp = e.target.value;
    // get element style,
    let elStyle = e.target.style;
    // get element id,
    let elId = e.target.id;

    // evaluate if there was a input at all,
    if(uInp !== "" && uInp !== null && uInp !== undefined) {
        // make the outline green for user feedback,
        elStyle.outline = "3px solid green";

        // Check if we are doing something with series,
        if(elId === "serieb-form-serieNaam" || elId === "seriem-form-serieNaam") {
            // disabled all create and edit serie form submit buttons.
            createSerieSubm.disabled = false, editSerieSubm.disabled = false;
        // We are doing things with albums instead,
        } else {
            // set the checked state to true,
            naamChecked = true;

            // if the isbn was also checked,
            if(isbnChecked) {
                // enable the create and edit form submit buttons.
                createAlbumSubm.disabled = false, editAlbumSubm.disabled = false;
            // If the isbn was not checked,
            } else {
                // disable the create and edit form submit buttons.
                createAlbumSubm.disabled = true, editAlbumSubm.disabled = true;
            }
        }
    // If there is not valid input,
    } else {
        // make the outline red for user feedback,
        elStyle.outline = "3px solid red";

        // Check if we are in a series or albums pop-in,
        if(elId === "serieb-form-serieNaam" || elId === "seriem-form-serieNaam") {
            // diabled all serie form submit buttons
            createSerieSubm.disabled = true, editSerieSubm.disabled = true;
        } else {
            // set checked state to false,
            naamChecked = false;
            // disabled all album form submit buttons,
            editAlbumSubm.disabled = true, createAlbumSubm.disabled = true;
        }
    }
}

// Check and filter the isbn input
function isbnCheck(e) {
    // Get user input,
    let uInp = e.target.value;
    // get element style,
    let elStyle = e.target.style;

    // if there is an input,
    if(uInp !== "" && uInp !== null && uInp !== undefined) {
        // replace any '-' from the input,
        let isbn = uInp.replace(/-/g, "");
        // set filter to all lower- and uppercase letters,
        let filter = /[a-zA-z]/g;
        // test filter against input creating a true/false value,
        let letters = filter.test(isbn);

        // evaluate if the filter found letters,
        if(!letters) {
            // and if the filtered isbn value is 0 or the length is 10 or 13,
            if(isbn === '0' || isbn.length === 10 || isbn.length === 13) {
                // we change the border color,
                elStyle.outline = "3px solid green";
                // set the input to the filtered value,
                e.target.value = isbn;
                // set global checked state to true,
                isbnChecked = true;
                // if the name was also valid
                if(naamChecked) {
                    // enable the create and edit album form submit buttons,
                    createAlbumSubm.disabled = false, editAlbumSubm.disabled = false;
                }
            // if the isbn is not valid,
            } else {
                // change the border color,
                elStyle.outline = "3px solid red";
                // disable all buttons,
                createAlbumSubm.disabled = true, editAlbumSubm.disabled = true;
                // and set the global checked state to false.
                isbnChecked = false;
            }
        // If the filter found letters,
        } else {
            // change the border color,
            elStyle.outline = "3px solid red";
            // disable all buttons,
            createAlbumSubm.disabled = true, editAlbumSubm.disabled = true;
            // and set the global checked state to false.
            isbnChecked = false;
        }
    // If there is no input,
    } else {
        // change the border color,
        elStyle.outline = "3px solid red";
        // disable all buttons,
        createAlbumSubm.disabled = true, editAlbumSubm.disabled = true;
        // and set the global checked state to false.
        isbnChecked = false;
    }
}

// I check if the cover file size isnt larger then 4MB
function albCovCheck(e) {
    let file = e.target.files;

    if(file[0].size > 4096000) {
        displayMessage("Bestand is te groot, graag iets van 4MB of kleiner.");
        e.target.value = "";
    }
}

// Add album function to open the pop-in
function albumToevInv() {
    let uInp = document.getElementById("album-toev").value;
    let ind = localStorage.huidigeIndex;

    // If there is a series index, i save it in the pop-in form, along with the name.
    if(ind !== "" || ind !== null || ind !== undefined) {
        document.getElementById("albumt-form-indexT").value = localStorage.huidigeIndex;
        document.getElementById("albumt-form-seNaam").value = uInp;
    // If there isnt i just pass on the serie name.
    } else {
        document.getElementById("albumt-form-seNaam").value = uInp;
    }

    // If a selection is made i open the pop-in, if not is give user feedback.
    if(uInp !== "" && uInp !== null && uInp !== undefined) {
        window.location.assign('/beheer#albumt-pop-in');
    } else {
        displayMessage("U kan geen album toevoegen, als u geen selectie maakt!");
    }
}

// Function to close pop-ins while on the '/beheer' page.
function popInClose() {
    // If a series index was stored, i need to request a new view from PhP
    if(localStorage.huidigeIndex !== "" && localStorage.huidigeIndex !== null && localStorage.huidigeIndex !== undefined) {
        let serieNaam = document.getElementById('beheer-albView-text').innerHTML;

        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/beheer');

        let fInp1 = document.createElement('input');
        fInp1.setAttribute('type', 'text');
        fInp1.setAttribute('name', 'serie-index');
        fInp1.setAttribute('value', localStorage.huidigeIndex);
        fInp1.hidden = true;

        let fInp2 = document.createElement('input');
        fInp2.setAttribute('type', 'text');
        fInp2.setAttribute('name', 'serie-naam');
        fInp2.setAttribute('value', serieNaam);
        fInp2.hidden = true;

        form.appendChild(fInp1);
        form.appendChild(fInp2);
        document.body.appendChild(form);

        form.submit();
    // If there wasnt, we can just return to the '/beheer' page
    } else {
        window.location.assign('/beheer');
    }
}

// Add Album Submit function
function albumToevSubm(e) {
    // Get form, store as FormData, and prevent the default form submit
    let form = document.getElementById('albumt-form');
    let formData = new FormData(form);
    e.preventDefault();

    // Send fetch request to PhP, check for errors, store user feedback before closing the pop-in
    fetchRequest('albumT', 'POST', formData)
    .then((data) => {
        if(typeof data != 'object') {
            localStorage.setItem('fetchResponse', data);
            popInClose();
        // If we have errors, we ensure they are all displayed properly.
        } else {
            if(data.aNaamFailed != "") {
                if(data.aIsbnFailed != "") {
                    displayMessage(data.aNaamFailed, data.aIsbnFailed);
                } else {
                    displayMessage(data.aNaamFailed);
                }
            } else {
                displayMessage(data.aIsbnFailed);
            }
        }
    })
}

// Edit Album button
function albumBewerken(e) {
    // Load all data required to display the current album info
    let rowCol = document.getElementsByClassName('album-bewerken-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let div = rowArr[0].children[5];
    let form = document.getElementById('albumb-form');
    let covLab = document.getElementById("modal-form-albumB-cov-lab");
    let covInp = document.getElementById("modal-form-albumB-cov-lab").children[0];
    let albCov = document.getElementById("albumb-cover");

    // Enable the button by default
    editAlbumSubm.disabled = false;

    // Inject all album info in to the pop-in form.
    form[0].value = e.target.id;
    form[1].value = rowArr[0].children[2].innerHTML;
    form[2].value = rowArr[0].children[3].innerHTML;
    form[3].value = rowArr[0].children[4].innerHTML;
    form[4].value = "";
    form[5].value = rowArr[0].children[6].innerHTML;
    form[6].value = rowArr[0].children[7].innerHTML;

    dispatchInputEvent(e);

    // Extra check for the album cover.
    if(rowArr[0].children[5].innerHTML.trim() != "") {
        let imgEl = document.createElement('img');
        imgEl.src = div.children[0].src;
        imgEl.id = 'albumb-cover-img';
        imgEl.className = 'modal-album-cover-img';
        albCov.appendChild(imgEl);
        covLab.innerHTML = 'Nieuwe Cover Selecteren';
        covLab.appendChild(covInp);
    } else {
        albCov.innerHTML = "Geen cover gevonden, u kun een cover selecteren, maar dit is niet verplicht.";
        covLab.innerHTML = 'Album Cover Selecteren';
        covLab.appendChild(covInp);
    }

    // Display the pop-in.
    window.location.assign('#albumb-pop-in');
}

// Edit Album pop-in submit button.
function albumBewSubm(e) {
    // Get form, create FormData from it, and prevent the default form submit.
    let form = document.getElementById('albumb-form');
    let formData = new FormData(form);
    e.preventDefault();

    // Send request to PhP, check for errors, and store user feedback before closing the pop-in
    fetchRequest('albumBew', 'POST', formData)
    .then((data) => {
        if(typeof(data) !== 'object') {
            localStorage.setItem('fetchResponse', data);
            popInClose();
        // If there are errors, display them properly to the user.
        } else {
            if(data.albumNaam != "") {
                if(data.albumIsbn != "") {
                    displayMessage(data.albumNaam, data.albumIsbn);
                } else {
                    displayMessage(data.albumNaam);
                }
            } else {
                displayMessage(data.albumIsbn);
            }
        }
    });
}

// Remove Album function.
function albumVerwijderen(e) {
    // Get all Album info, and make new empty FormData
    let rowCol = document.getElementsByClassName('album-bewerken-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let formData = new FormData();
    let conf = confirm("Weet u zeker dat u het album: " + rowArr[0].children[2].innerHTML + " wilt verwijderen ?");

    // Add relevant album info to the FormData
    formData.append('serie-index', localStorage.huidigeIndex);
    formData.append('album-index', e.target.id);
    formData.append('album-naam', rowArr[0].children[2].innerHTML);

    if(conf) {
        // Send Request to PhP
        fetchRequest('albumV', 'POST', formData)
        // Store the response and request a page reload for the current view.
        .then((data) => {
            localStorage.setItem('fetchResponse', data);
                    
            let form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', '/beheer');

            let fInp1 = document.createElement('input');
            fInp1.setAttribute('type', 'text');
            fInp1.setAttribute('name', 'serie-index');
            fInp1.setAttribute('value', localStorage.huidigeIndex);

            let fInp2 = document.createElement('input');
            fInp2.setAttribute('type', 'text');
            fInp2.setAttribute('name', 'serie-naam');
            fInp2.setAttribute('value', document.getElementById('beheer-albView-text').innerHTML);

            form.appendChild(fInp1);
            form.appendChild(fInp2);
            document.body.appendChild(form);

            form.submit();
        });
    }
}

// Create series controller button
function serieSelSubmit(e) {
    // Get input, create new FormData, and prevent the default submit.
    let inp = document.getElementById("serie-maken-inp");
    let formData = new FormData();
    e.preventDefault();

    // If there is input selected, add input to FormData and send said input to PhP,
    if(inp.value !== "") {
        formData.append('naam-check', inp.value);

        fetchRequest('serieM', 'POST', formData)
        // Check for errors, and provide feedback about said errors.
        .then((data) => {
            if(data !== "Serie-Maken") {
                displayMessage(data);
            // If no errors
            } else {
                // cast serie name into the form input first,
                document.getElementById('seriem-form-serieNaam').value = inp.value;
                // then dispatch the event so the serie name is actually checked,
                dispatchInputEvent(e);
                // and then redirect to the pop-in.
                window.location.assign('/beheer#seriem-pop-in');
            }
        })
    // If there was no input selection for some reason, we provide user feedback.
    } else {
        displayMessage("Zonder opgegeven naam, kan er geen serie gemaakt worden!");
    }
}

// Creat series pop-in button
function serieMakSubm(e) {
    // Get form element, create new FormData from it, and prevent the default submit.
    let form = document.getElementById('seriem-form');
    let formData = new FormData(form);
    e.preventDefault();

    // Send form to PhP, check for error, store user feedback, and redirect back to the main view.
    fetchRequest('serieM', 'POST', formData)
    .then((data) => {
        if(typeof(data) !== "object") {
            localStorage.setItem('fetchResponse', data);
            window.location.assign('/beheer');
        } else {
            displayMessage(data['Serie_Naam']);
        }
    })
}

// View Series button
function serieBekijken(e) {
    // Get the required elements, and set the series data in the hidden form.
    let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let inp1 = document.getElementById("serie-bekijken-form-index-"+e.target.id);
    inp1.value = e.target.id;

    // Trigger submit based on if there was a valid input.
    if (inp1.value === "" && inp2.value === "") {
        return false;
    } else {
        return true;
    }
}

// Back button for the series view
function beheerBackButt() {
    // Revert the series view back to the main view, and hide the back button.
    albView.replaceWith(serieView);
    backButt.style.display = 'flex';
    buttCont.style.position = "absolute";
    document.getElementById('serie-bekijken-form-'+localStorage.huidigeIndex).reset();
    document.getElementById('serie-bekijken-form-'+localStorage.huidigeIndex).submit();
    localStorage.removeItem('huidigeIndex');
}

// Edit serie controller button
function serieBewerken(e) {
    // Get the current table row, and the serie-bewerken pop-in form
    let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let form = document.getElementById('serieb-form');

    // Cast row data into the form inputs, and redirect to said form.
    form[0].value = replaceSpecChar(rowArr[0].children[3].innerHTML);
    form[1].value = e.target.id;
    form[2].value = replaceSpecChar(rowArr[0].children[4].innerHTML);
    form[3].value = replaceSpecChar(rowArr[0].children[5].innerHTML);

    // Dispatch event to 
    dispatchInputEvent(e);

    window.location.assign('#serieb-pop-in');
}

// Edit serie pop-in button
function serieBewSubm(e) {
    // Get form element, create FormData from it, and prevent the default submit.
    let form = document.getElementById("serieb-form");
    let formData = new FormData(form);
    e.preventDefault();
    
    // Send FormData to PhP, check for errors, store the user feedback and redirect to default view.
    fetchRequest('serieBew', 'POST', formData)
    .then((data) => {
        if(typeof data != 'object') {
            localStorage.setItem('fetchResponse', data);
            window.location.assign('/beheer');
        } else {
            displayMessage(data['Serie_Naam']);
        }
    })
}

// Remove serie button
function serieVerwijderen(e) {
    // Get table row, create new FormData, and ask for confirmation of the remove action.
    let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let formData = new FormData();
    let conf = confirm("Weet u zeker dat de Serie: " + rowArr[0].children[3].innerHTML + "\n En al haar albums wilt verwijderen ?");

    // If the confirm was made, add serie data to the FormData, and remove the table row.
    if(conf) {
        formData.append('serie-index', e.target.id);
        formData.append('serie-naam', rowArr[0].children[3].innerHTML);
        rowCol[0].remove();
        
        // Send request to PhP, and provide user feedback.
        fetchRequest('serieVerw', 'POST', formData)
        .then((data) => {
            displayMessage(data);
        });
    }
}

// Password reset controller reset button, redirecting to the pop-in.
function wwResetClick() {
    window.location.assign('#ww-reset-pop-in');
}

// Password reset pop-in button
function aResetBev(e) {
    // Get form element, create new FormData from it and prevent the default submit.
    let form = document.getElementById('ww-reset-form');
    let formData = new FormData(form);
    e.preventDefault();

    // Check if the form has inputs, and see if the passwords are identical.
    if(form[0].value != '' && form[1].value != '' && form[2].value != '') {
        if(form[1].value === form[2].value) {
            // Ask for confirmation since its a remove action.
            let conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?");

            // When confirmed, send request to PhP, provide user feedback and close the pop-in.
            if(conf) {
                fetchRequest('aReset', 'POST', formData)
                .then((data) => {
                    localStorage.setItem('fetchResponse', data);
                    popInClose();
                })
            // If not confirmend provide feedback.
            } else {
                displayMessage("Reset afgbroken, verander de gegevens en probeer het nogmaals.");
            }
        // If passwords are not equal, provide feedback.
        } else {
            displayMessage("De opgegeven wachtwoorden zijn niet gelijk, probeer het nogmaals.");
        }
    // If inputs are missing, provide feedback
    } else {
        displayMessage("Niet alles is ingevuld, vul de juiste gegevens in, en probeer het nogmaals");
    }
}
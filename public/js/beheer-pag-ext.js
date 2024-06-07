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
        It also change the input style, based on the evaluation, and works in tandem with isbnCheck(e) if there is a isbn input field.
            e       - The listen event object.
            uInp    - The user input, set from the listen event object.
            elStyle - The element style of the input element, set from the listen event object.

        External Variables (defined globally, instanced in the init):
            createSerieSubm / editSerieSubm - Create/Edit subm buttons for series.
            createAlbumSubm / editAlbumSubm - Create/Edit subm buttons for albums.
        
        Return Value: None.
 */
function naamCheck(e) {
    const uInp = e.target.value, elStyle = e.target.style;
    if(uInp !== "" && uInp !== null && uInp !== undefined) {
        elStyle.outline = "3px solid green", naamChecked = true;
        if(e.target.id === "serieb-form-serieNaam" || e.target.id === "seriem-form-serieNaam") { return editSerieSubm.disabled = false, createSerieSubm.disabled = false; }
        if(e.target.id === "albumb-form-alb-naam" || e.target.id === "albumt-form-alb-naam") { if(isbnChecked) { return editAlbumSubm.disabled = false, createAlbumSubm.disabled = false; } }
    } else { return elStyle.outline = "3px solid red", naamChecked = false, editSerieSubm.disabled = true, createSerieSubm.disabled = true,  editAlbumSubm.disabled = true, createAlbumSubm.disabled = true; }
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
    const uInp = e.target.value, elStyle = e.target.style;
    if(uInp !== "" && uInp !== null && uInp !== undefined) {
        const isbn = uInp.replace(/-/g, ""), filter = /[a-zA-z]/g, letters = filter.test(isbn);
        if(!letters && isbn === "0" || isbn.length === 10 || isbn.length === 13) {
            elStyle.outline = "3px solid green", e.target.value = isbn, isbnChecked = true;
            if(naamChecked) { return createAlbumSubm.disabled = false, editAlbumSubm.disabled = false; }
        } else { return elStyle.outline = "3px solid red", isbnChecked = false,  createAlbumSubm.disabled = true, editAlbumSubm.disabled = true; }
    } else { return elStyle.outline = "3px solid red", isbnChecked = false, editAlbumSubm.disabled = true, createAlbumSubm.disabled = true; }
}

/*  albCovCheck(e):
        This function simply checks the files size, and is triggered with the on-change coverInpCheck.
            e       - The submit button listen event object, passed on via the covInpCheck.
            file    - The file that has been selected by the user.
        
        Return Value: Boolean.
 */
function albCovCheck(e) {
    const file = e.target.files;
    if(file[0].size > 4096000) {
        displayMessage("Bestand is te groot, graag iets van 4MB of kleiner."),  e.target.value = "";
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
function coverInpCheck(e) {
    const imgEl = document.createElement('img'), imageFile = e.target.files[0], check = albCovCheck(e);
    let labEl;
    if(check) {
        imgEl.src = URL.createObjectURL(imageFile), imgEl.id = "albumb-cover-img",  imgEl.className = "modal-album-cover-img";
        if(e.target.id === "albumb-form-alb-cov") {
            const divCov = document.getElementById("albumB-cover");
            labEl = document.getElementById("modal-form-albumB-cov-lab"),  divCov.appendChild(imgEl);
        } else if(e.target.id === "albumt-form-alb-cov") {
            const divCov = document.getElementById("albumT-cover");
            labEl = document.getElementById("modal-form-albumt-cov-lab"),  divCov.appendChild(imgEl);
        }
        labEl.innerHTML = "Nieuwe Cover Selecteren",  labEl.appendChild(e.target);
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
function serieVerwijderen(e) {
    const rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    const rowArr = Array.from(rowCol);
    const conf = confirm("Weet u zeker dat de Serie: " + rowArr[0].children[3].innerHTML + "\n En al haar albums wilt verwijderen ?");
    if(conf) {
        return true;
    } else {
        if(sessionStorage.scrollPos) { sessionStorage.removeItem("scrollPos"); }
        return false;
    }
}

/*  wwResetClick():
        Redirect to the password reset pop-in.
 */
function wwResetClick() {
    return window.location.assign('#ww-reset-pop-in');
}

/*  aResetBev(e):
        This function asks for user confirmation, before submitting the Admin password reset form.
            e       - The submit button listen event object.
            conf    - The result of the user confirmation.

        Return value: Boolean.
 */
function aResetBev(e) {
    const conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?");
    if(conf) {
        return true;
    } else { return false; }
}
let submitButtToev, submitButtBew, naamChecked = false, isbnChecked = false;
let serieView, albView, buttCont, backButt;

function initBeheer() {
    /* album-bewerken submit form */
    let albCovInp = document.getElementById('albumb-form-alb-cov');
    albCovInp.addEventListener('change', albCovCheck);

    /* All elementens for serie-bekijken */
    buttCont = document.getElementById("title-buttons");
    backButt = document.getElementById("beheer-back-butt");
    serieView = document.querySelector("#beheer-weerg-repl-cont");
    albView = document.querySelector("#beheer-albView-content-container");
    buttCont.style.position = "absolute";

    // If the admin wants to view a Series
    if(localStorage.serieWeerg) {
        // I just replace the entire container
        serieView.replaceWith(albView);
        // I enable the back button, and set it to a fixed position.
        backButt.hidden = false;
        buttCont.style.position = "fixed";

        // I check if the serie name is stored, and replace the title of the container
        if(localStorage.huidigeSerie != null && localStorage.huidigeSerie != "") {
            document.getElementById('beheer-albView-text').innerHTML = localStorage.huidigeSerie;
        }

        // Clean up storage.
        localStorage.removeItem("serieWeerg");
        localStorage.removeItem('huidigeSerie');
    }

    // Elements and checks require for creating a series.
    let inp = document.getElementById('seriem-form-serieNaam');

    if(localStorage.sNaamFailed !== null) {
        displayMessage(localStorage.sNaamFailed);
        localStorage.removeItem('sNaamFailed');
    }

    if(localStorage.sNaam !== null) {
        inp.value = localStorage.sNaam;
        localStorage.removeItem('sNaam');
    }

    // Elements required for adding and editing albums.
    submitButtToev = document.getElementById("albumt-form-button");
    let inpTIndex = document.getElementById("albumt-form-indexT");
    let naamInpToev = document.getElementById("albumt-form-alb-naam");
    let isbnInpToev = document.getElementById("albumt-form-alb-isbn");
    submitButtBew = document.getElementById("albumb-form-button");
    let naamInpBew = document.getElementById("albumb-form-alb-naam");
    let isbnInpBew = document.getElementById("albumb-form-alb-isbn");

    // Events associated with adding and editing albums.
    isbnInpToev.addEventListener("input", isbnCheck);
    naamInpToev.addEventListener("input", naamCheck);
    submitButtToev.disabled = true;
    isbnInpBew.addEventListener("input", isbnCheck);
    naamInpBew.addEventListener("input", naamCheck);
    submitButtBew.disabled = true;

    // Ensure the series index is also used for adding a album to a series.
    if(localStorage.albumToevIn != null && localStorage.albumToevIn != undefined && localStorage.albumToevIn != "") {
        inpTIndex.value = localStorage.albumToevIn;
        localStorage.removeItem('albumToevIn');
    }

    // Both validations need to be re-evaluated, seems not a bit off/useless atm.
    // Extra user validation.
    if(sessionStorage.gebruiker === null && sessionStorage.gebruiker === "" && sessionStorage.gebruiker != 'admin@colltrack.nl') {
        sessionStorage.removeItem('gebruiker');
        window.location.assign('/');
    }

    // More user validation.
    if(sessionStorage.gebruiker === null || sessionStorage.gebruiker === undefined) {
        window.location.assign('/');
    } else {
        let formData = new FormData();
        formData.append('gebr_email', sessionStorage.gebruiker);

        fetchRequest('valUsr', 'POST', formData)
        .then((data) => {
            if(data !== "Valid User") {
                window.location.assign('/');
                sessionStorage.removeItem('gebruiker');
            } else if (data === "Valid User" && sessionStorage.gebruiker !== "admin@colltrack.nl") {
                window.location.assign('/gebruik');
            }
        });
    }

    // Display feedback messages, that are stored before a page refresh.
    if(localStorage.fetchResponse !== "") {
        displayMessage(localStorage.fetchResponse);
        localStorage.removeItem("fetchResponse");
    }
}

// Check the name input
function naamCheck(e) {
    // Evaluate if there was a input at all, and change the check state and input outline.
    if(e.target.value !== "" && e.target.value !== null && e.target.value !== undefined) {
        naamChecked = true;
        e.target.style.outline = "3px solid green";
        // If the isbn was also checked,
        if(isbnChecked) {
            // enable the button that use these elements in there pop-in/form
            submitButtToev.disabled = false, submitButtBew.disabled = false;
        // Disable said button if not
        } else { submitButtToev.disabled = true, submitButtBew.disabled = true; }
    // If there is not valid input, change the check state and input outline, and also disabled the buttons.
    } else {
        naamChecked = false;
        e.target.style.outline = "3px solid red";
        submitButtToev.disabled = true, submitButtBew.disabled = true;
    }
}

// Check and sanitise the isbn input
function isbnCheck(e) {
    // If there is an input
    if(e.target.value !== "" && e.target.value !== null && e.target.value !== undefined) {
        // i remove any '-' and check if there are no letters in it.
        let isbn = e.target.value.replace(/-/g, "");
        let expression = /[a-zA-z]/g;
        let letters = expression.test(isbn);

        // If the input is incorrect, change the outline, disable buttons and store the checked state.
        if(isbn.length !== 10 || letters) {
            e.target.style.outline = "3px solid red";
            submitButtToev.disabled = true, submitButtBew.disabled = true;
            isbnChecked = false;
        // If correct, store the state, change the outline and set the filtered value.
        } else {
            e.target.style.outline = "3px solid green";
            e.target.value = isbn;
            isbnChecked = true;
            // If the name was also valid, enable all buttons.
            if(naamChecked) {
                submitButtToev.disabled = false, submitButtBew.disabled = false;
            }
        }
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
    let inp = document.getElementById("album-toev").value;
    let ind = localStorage.huidigeIndex;

    // If there is a series index, i save it in the pop-in form, along with the name.
    if(ind !== "" || ind !== null || ind !== undefined) {
        document.getElementById("albumt-form-indexT").value = localStorage.huidigeIndex;
        document.getElementById("albumt-form-seNaam").value = inp;
    // If there isnt i just pass on the serie name.
    } else {
        document.getElementById("albumt-form-seNaam").value = inp;
    }

    // If a selection is made i open the pop-in, if not is give user feedback.
    if(inp !== "" && inp !== null && inp !== undefined) {
        window.location.assign('/beheer#albumt-pop-in');
    } else { displayMessage("U kan geen album toevoegen, als u geen selectie maakt!"); }
}

// TODO: Re-factor using other means if possible ?
// Function to close pop-in while on the '/beheer' page/
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
    } else { window.location.assign('/beheer'); }
}

// Add Album Submit function
function albumToevSubm(e) {
    // Get form, store as FormData, and prevent the default form submit
    let form = document.getElementById('albumt-form');
    let formData = new FormData(form);
    e.preventDefault();

    // Send fetch request to PhP
    fetchRequest('albumT', 'POST', formData)
    .then((data) => {
        // If we have no errors, we store the feedback and close to pop-in
        if(typeof data != 'object') {
            localStorage.setItem('fetchResponse', data);
            popInClose();
        // If we have erros, we ensure they are all displayed properly.
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

// Edit Album function
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
    submitButtBew.disabled = false;

    // Inject all album info in to the pop-in form.
    form[0].value = e.target.id;
    form[1].value = rowArr[0].children[2].innerHTML;
    form[2].value = rowArr[0].children[3].innerHTML;
    form[3].value = rowArr[0].children[4].innerHTML;
    form[4].value = "";
    form[5].value = rowArr[0].children[6].innerHTML;
    form[6].value = rowArr[0].children[7].innerHTML;
    // Extra check for the album cover.
    if(rowArr[0].children[5].hasChildNodes()) {
        let imgEl = document.createElement('img');
        imgEl.src = div.children[0].src;
        imgEl.id = 'album-cover-img';
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

// Edit Album Submit button.
function albumBewSubm(e) {
    // Get form, create FormData from it, and prevent the default form submit.
    let form = document.getElementById('albumb-form');
    let formData = new FormData(form);
    e.preventDefault();

    // Send request to PhP
    fetchRequest('albumBew', 'POST', formData)
    .then((data) => {
        // Check if there are no errors, and store the feedback a before closing the pop-in.
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

    // Add relevant album info to the FormData
    formData.append('serie-index', localStorage.huidigeIndex);
    formData.append('album-index', e.target.id);
    formData.append('album-naam', rowArr[0].children[2].innerHTML);

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

/* Controlle functies voor Serie Maken/Bekijken/Bewerken/Verwijderen */
/*  serieSelSubmit(e):
        Dit is de functie voor de 'Bevestigen' knop in de controller.
        Deze checkt via JS fetch, of de naam al aanwezig is in de database, en voer de juiste handeling uit.
        Als de naam al aanwezig is, krijgt de gebruiker een melding in beeeld.
        Als de naam niet aanwezig is, slaan we de input op in de pop-in form, en openen we de form.
        Als er helemaal geen input is, krijgt de gebruiker daat ook melding over.
 */
function serieSelSubmit(e) {
    e.preventDefault();

    let inp = document.getElementById("serie-maken-inp");
    let formData = new FormData();

    if(inp.value !== "") {
        formData.append('naam-check', inp.value);

        fetchRequest('serieM', 'POST', formData)
        .then((data) => {
            if(data !== "Serie-Maken") {
                displayMessage(data);
            } else {
                document.getElementById('seriem-form-serieNaam').value = inp.value;
                window.location.assign('/beheer#seriem-pop-in');
            }
        })
    } else { displayMessage("Zonder opgegeven naam, kan er geen serie gemaakt worden!"); }
}

/*  serieMakSubm(e):
        Deze functie is voor de submit knop van de serie maken pop-in.
        Ik zet de form om in JS FormData, en gebruik mijn fetch functie om die data naar PhP te sturen.
        Als er geen foutmelding terug komt (data != object), sla ik de melding op in de local storage, en redirect ik terug naar beheer.
        Als er wel een foutmelding is terug gekomen, dan geef ik die melding weer voor de de gebruiker.
 */
function serieMakSubm(e) {
    e.preventDefault();

    let form = document.getElementById('seriem-form');
    let formData = new FormData(form);

    fetchRequest('serieM', 'POST', formData)
    .then((data) => {
        if(typeof(data) !== "object") {
            localStorage.setItem('fetchResponse', data);
            window.location.assign('/beheer');
        } else { displayMessage(data['Serie_Naam']); }
    })
}

/*  serieBekijken(e):
        Deze functie zorgt er voor dat serie inhoud te bekijken is, via een verstopte form.
        Ik zet de serie dat uit de tafel rij, in de juiste input velden.
        En als die input velden een waarde hebben, zorg ik dat de form submit mag worden (true/false).
        Het vervangen van de pagina inhoud, gebeurd in de init functie.
 */
function serieBekijken(e) {
    let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let inp1 = document.getElementById("serie-bekijken-form-index-"+e.target.id);
    let inp2 = document.getElementById("serie-bekijken-form-naam-"+e.target.id);

    inp1.value = e.target.id;
    inp2.value = rowArr[0].children[3].innerHTML;

    if (inp1.value === "" && inp2.value === "") {
        return false;
    } else {
        return true;
    }
}

/*  beheerBackButt():
        Deze functie zorgt ervoor, dat de serie inhoud data die inbeeld is gekomen, weer terug word gezet naar de lijst met series.
        Dit doe ik door de hele tafel te vervangen, omdat de serie data altijd aanwezig is, is een extra server verzoek niet nodig.
        De '< Series' back button, word verstopt, en de container wordt weer vast gezet op de pagina.
        Ik zorg er ook voor, dat de form data reset word en opnieuwe verzonden word, zodat met een pagina refresh de gebruiker niet terug gaat.
        En als laatst verwijder ik de huidigeIndex dat uit de browser storage.
 */
function beheerBackButt() {
    albView.replaceWith(serieView);
    backButt.hidden = true;
    buttCont.style.position = "absolute";
    document.getElementById('serie-bekijken-form-'+localStorage.huidigeIndex).reset();
    document.getElementById('serie-bekijken-form-'+localStorage.huidigeIndex).submit();
    localStorage.removeItem('huidigeIndex');
}

/*  serieBewerken(e):
        Deze functie is lijkt complexer dan hij is, maar eigenlijk copieer ik aleen de row data naar de bewerken pop-in.
        En zorg ik ervoor, dat er een redirect is naar de 'serieb-pop-in'.
 */
function serieBewerken(e) {
    let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let form = document.getElementById('serieb-form');

    form[0].value = replaceSpecChar(rowArr[0].children[3].innerHTML);
    form[1].value = e.target.id;
    form[2].value = replaceSpecChar(rowArr[0].children[4].innerHTML);
    form[3].value = replaceSpecChar(rowArr[0].children[5].innerHTML);

    window.location.assign('#serieb-pop-in');
}

/*  serieBewSubm(e):
        Deze onclick functie, stuurt de FormData naar PhP, via mijn eigen fetchRequest functie.
        Ik zet de hele form direct om naar een FormData object, en voorkom de default submit.
        Dan stuur ik de data door naar PhP, en wacht ik de response af.
        Als die response geen object is (assoc array), sla ik die op in de localStorage, en geef ik een redirect naar de beheer pagina.
        Als het wel een object is, geef ik de melding weer via mijn displayMessage functie.
 */
function serieBewSubm(e) {
    let form = document.getElementById("serieb-form");
    let formData = new FormData(form);
    e.preventDefault();
    
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

/*  serieVerwijderen(e):
        Deze functie is gemaakt voor de verwijder knop, en pak de juiste informatie uit de tafel-rij.
        De informatie wordt in formData gezet, zodat we in PhP makkelijker iets kunnen doen met deze data.
        En de rij wordt uit de tafel verwijderdt, zodat er geen pagina refresh nodig is.
        Vervolgens gebruik ik mijn fetchRequest functie, om de data naar PhP te sturen.
        Voor de response gebruik ik mijn displayMessage functie, zodat er een terugkoppeling is naar de gebruiker.
 */
function serieVerwijderen(e) {
    let rowCol = document.getElementsByClassName('serie-tafel-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let formData = new FormData();
    let conf = confirm("Weet u zeker dat de Serie: " + rowArr[0].children[3].innerHTML + "\n En al haar albums wilt verwijderen ?");
        
    if(conf) {
        formData.append('serie-index', e.target.id);
        formData.append('serie-naam', rowArr[0].children[3].innerHTML);
        
        rowCol[0].remove();
        
        fetchRequest('serieVerw', 'POST', formData)
        .then((data) => {
            displayMessage(data);
        })
    }
}

/* Wachtwoord Reset functies */
/*  wwResetClick():
        Onclick functie, die een redirect geeft naar de ww-reset-pop-in.
 */
function wwResetClick() {
    window.location.assign('#ww-reset-pop-in');
}

// Pas de comments aan voor de nieuwe JS fetch structuur.
/*  aResetBev():
        
 */
function aResetBev(e) {
    let form = document.getElementById('ww-reset-form');
    let formData = new FormData(form);
    e.preventDefault();

    if(form[0].value != '' && form[1].value != '' && form[2].value != '') {
        if(form[1].value === form[2].value) {
            let conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?");

            if(conf) {
                fetchRequest('aReset', 'POST', formData)
                .then((data) => {
                    localStorage.setItem('fetchResponse', data);
                    popInClose();
                })
            } else {
                displayMessage("Reset afgbroken, verander de gegevens en probeer het nogmaals.");
            }
        } else {
            displayMessage("De opgegeven wachtwoorden zijn niet gelijk, probeer het nogmaals.");
        }
    } else {
        displayMessage("Niet alles is ingevuld, vul de juiste gegevens in, en probeer het nogmaals");
    }
}
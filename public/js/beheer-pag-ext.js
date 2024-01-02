/* TODO List:
        - Edit/clean-up comments, to much clutter atm left over from the concept/design stage.
        - Review voor 2.0 versie, loop door alles heen om te kijken wat er nog veranderd moet worden.
 */

// Globale variabelen, die ik nodig heb in en buiten de initBeheer functie, en/of op meerdere plaatsen gebruik.
let submitButtToev, submitButtBew, naamChecked = false, isbnChecked = false;
let serieView, albView, buttCont, backButt;

// Init functie, zodat belangrijke dingen aleen geladen worden als de hele pagina geladen is.
function initBeheer() {
    /* Album Bewerken submit form */
    // De elmenten voor de cover en de form zelf.
    let albCovInp = document.getElementById('albumb-form-alb-cov');
    // De listenevents voor de elementen.
    albCovInp.addEventListener('change', albCovCheck);

    /* Alle nodige elementen voor de serie-bekijken functie */
    buttCont = document.getElementById("title-buttons");
    backButt = document.getElementById("beheer-back-butt");
    serieView = document.querySelector("#beheer-weerg-repl-cont");
    albView = document.querySelector("#beheer-albView-content-container");
    // De container moet altijd op de juiste plek staan.
    buttCont.style.position = "absolute";

    /* Als er aangegeven is dat ik een serie wil bekijken, vervang ik simpel weg de hele container */
    if(localStorage.serieWeerg) {
        serieView.replaceWith(albView);
        // Ik zet de terug knop aan, en de container van de knop op fixed.
        backButt.hidden = false;
        buttCont.style.position = "fixed";

        /* Loop voor het weergeven van de album naam in de albums bekijken pop-in header */
        if(localStorage.huidigeSerie != null && localStorage.huidigeSerie != "") {
            // De title moet veranderen naar de serie naam.
            document.getElementById('beheer-albView-text').innerHTML = localStorage.huidigeSerie;
        }

        // En ik verwijder de waardes die ik check in de localStorage.
        localStorage.removeItem("serieWeerg");
        localStorage.removeItem('huidigeSerie');
    }

    /* Loop om te kijken of er een gebruiker is opgeslagen, en of die gebruiker een Admin is of niet */
    if(sessionStorage.gebruiker === null && sessionStorage.gebruiker === "" && sessionStorage.gebruiker != 'admin@colltrack.nl') {
        // Om problemen te voorkomen, verwijder ik de gebruikers data, en stuur ik de gebruiker terug naar de landingspagina.
        sessionStorage.removeItem('gebruiker');
        window.location.assign('/');
    }

    /* Alle benodigde voor serie-maken */
    let inp = document.getElementById('seriem-form-serieNaam');

    /* Loop om te kijken of PhP een dubble serie-naam heeft gevonden, in de inp van de controller */
    if(localStorage.sNaamFailed !== null) {
        displayMessage(localStorage.sNaamFailed);
        localStorage.removeItem('sNaamFailed');
    }

    /* Loop om de serie-naam van de controller, in de form input te zetten */
    if(localStorage.sNaam !== null) {
        inp.value = localStorage.sNaam;
        localStorage.removeItem('sNaam');
    }

    /* Alle benodigde elementen voor album-toevoegen & album-bewerken */
    submitButtToev = document.getElementById("albumt-form-button");
    let inpTIndex = document.getElementById("albumt-form-indexT");
    let naamInpToev = document.getElementById("albumt-form-alb-naam");
    let isbnInpToev = document.getElementById("albumt-form-alb-isbn");
    submitButtBew = document.getElementById("albumb-form-button");
    let naamInpBew = document.getElementById("albumb-form-alb-naam");
    let isbnInpBew = document.getElementById("albumb-form-alb-isbn");

    /* De Listenevents en state changes voor album-toevoegen & album-bewerken */
    isbnInpToev.addEventListener("input", isbnCheck);
    naamInpToev.addEventListener("input", naamCheck);
    submitButtToev.disabled = true;
    isbnInpBew.addEventListener("input", isbnCheck);
    naamInpBew.addEventListener("input", naamCheck);
    submitButtBew.disabled = true;

    /* Loop voor het mee geven van de serie index bij het toevoegen van een album */
    if(localStorage.albumToevIn != null && localStorage.albumToevIn != undefined && localStorage.albumToevIn != "") {
        inpTIndex.value = localStorage.albumToevIn;
        localStorage.removeItem('albumToevIn');
    }

    // TODO: Deze check klopt niet echt, omdat ik handmatig de admin e-mail kan invoeren, en dus op de '/beheer' pagina kan komen.
    /* Redirect naar de landings\beheer of gebruik pagina, als er geen gebruiker of ongeldige gebruiker is */
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

    // Loop om te kijken of er een response was van een fetch request, en deze in beeld te zetten.
    if(localStorage.fetchResponse !== "") {
        displayMessage(localStorage.fetchResponse);
        localStorage.removeItem("fetchResponse");
    }
}

/* Check functies, die inputs bekijken, en iets doen op basis van die evaluatie */
/*  naamCheck(e):
        Functie van de OnInput ListenEvent, die in de initBeheer() functie is toegewezen.
        Deze functie kijkt of er een input is, en geeft de gebruiker input of die data verzonden kan worden.
        Als er input is, krijgt het veld een groen lijn, en als de isbnChecked ook waar is, gaan de submit knoppen aan (en sla ik op de de naam gechecked is).
        Als er geen input is, kijgt het veld een rode lijn, en blijven de submit knoppen uit staan (en sla ik op de de naam gechecked is).
 */
function naamCheck(e) {
    if(e.target.value !== "" && e.target.value !== null && e.target.value !== undefined) {
        naamChecked = true;
        e.target.style.outline = "3px solid green";
        if(isbnChecked) {
            submitButtToev.disabled = false, submitButtBew.disabled = false;
        } else {
            submitButtToev.disabled = true, submitButtBew.disabled = true;
        }
    } else {
        naamChecked = false;
        e.target.style.outline = "3px solid red";
        submitButtToev.disabled = true, submitButtBew.disabled = true;
    }
}

/*  isbnCheck(e):
        Functie van de OnInput ListenEvent, die in de initBeheer() functie is toegewezen.
        Deze functie kijkt of er een input is gemaakt, en verwijdert '-' uit de input, omdat die niet relevant zijn voor het complete nummer.
        En de functie kijkt ook of er letter in zitten, want die horen niet in een ISBN te staan.

        Dan kijk de functie naar de lengte van de input, zodat er feedback is als de isbn niet lang genoeg is.
        Als de input niet lang genoeg is, komt er een rode lijn om de input heen, en blijven de submit knoppen uitstaan (en sla ik globaal op dat de isbn check false is).
        Als de input wel lang genoeg is, komt er een groene lijn om de input heen, en gaan de submit knoppen aan als de naamChecked waarde ook waar is (en sla ik globaal op dat de isbn check waar is).
 */
function isbnCheck(e) {
    if(e.target.value !== "" && e.target.value !== null && e.target.value !== undefined) {
        let isbn = e.target.value.replace(/-/g, "");
        let expression = /[a-zA-z]/g;
        let letters = expression.test(isbn);

        if(isbn.length !== 10 || letters) {
            e.target.style.outline = "3px solid red";
            submitButtToev.disabled = true, submitButtBew.disabled = true;
            isbnChecked = false;
        } else {
            e.target.style.outline = "3px solid green";
            e.target.value = isbn;
            isbnChecked = true;
            if(naamChecked) {
                submitButtToev.disabled = false, submitButtBew.disabled = false;
            }
        }
    }
}

/*  albCovCheck(e):
        Deze functie kijkt naar het bestand dat geselecteerd is, en vergelijkt die met een bepaalde grote (4 MB).
        Als het bestand te groot is, geeft die een melding, en verwijdert die het bestand uit de input.
 */
function albCovCheck(e) {
    let file = e.target.files;

    if(file[0].size > 4096000) {
        displayMessage("Bestand is te groot, graag iets van 4MB of kleiner.");
        e.target.value = "";
    }
}

/* Controlle functies voor Album Bewerken/Verwijderen/Toevoegen */
/*  albumToevInv():
        Hele eenvoudige functie, die kijkt of er een selectie is gemaakt.
        En als die gemaakt is, dan komt de pop-in in beeld.
        Is er geen selectie, dan geef ik een melding naar de gebruiker toe.
 */
function albumToevInv() {
    let inp = document.getElementById("album-toev").value;
    let ind = localStorage.huidigeIndex;

    // Als de huidigeIndex een geldige waarde heeft, sla ik die en de serie-naam op in de form.
    if(ind !== "" || ind !== null || ind !== undefined) {
        document.getElementById("albumt-form-indexT").value = localStorage.huidigeIndex;
        document.getElementById("albumt-form-seNaam").value = inp;
    // Als die geen geldige waarde heeft sla ik aleen de serie-naam op in de form.
    } else {
        document.getElementById("albumt-form-seNaam").value = inp;
    }

    // Als er een selectie is gemaakt, redirect ik de browser naar de pop-in.
    if(inp !== "" && inp !== null && inp !== undefined) {
        window.location.assign('/beheer#albumt-pop-in');
    // Als er geen selectie is gemaakt, geef ik daar feedback over naar de gebruiker.
    } else {
        displayMessage("U kan geen album toevoegen, als u geen selectie maakt!");
    }
}

/*  albumToevClose():
        Deze functie kijkt of de gebruiker op de normale /beheer pagina zit, of dat er een serie bekeken word.
        Dit kan doordat er in de serie bekijken stand, een huidigeIndex lokaal is opgeslagen, dus als die er niet is zijn we in de normale beheer view.
        Als die huidigeIndex er lokaal is, maak ik een form aan met de juiste gegeven, op exact dezelfde manier als ik de serie view in beeld heb gekregen.
        En die form submit ik naar PhP, zodat we weer terug komen op de beheer pagina, met de juiste data in beeld.
 */
function popInClose() {
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
    } else {
        window.location.assign('/beheer');
    }
}

/*  albumToevSubm():
        Deze functie stuurt de HTML form naar PhP, via de JS fetch functie.
        Dit kan eenvoudig door de form in zijn geheel, aan de FormData mee te geven.
        Als de response van PhP geen object is, is de actie geslaagd, en mag de pop-in gesloten worden nadat de melding is opgeslagen.
        Als de response wel een object is, word(en) de juiste melding(en) weer gegeven en blijft de pop-in open.
 */
function albumToevSubm(e) {
    let form = document.getElementById('albumt-form');
    let formData = new FormData(form);
    e.preventDefault();

    fetchRequest('albumT', 'POST', formData)
    .then((data) => {
        if(typeof data != 'object') {
            localStorage.setItem('fetchResponse', data);
            popInClose();
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

/*  albumBewerken(e):
        Deze functie is voor het bewerken van een album, en zorgt dat de juiste informatie in de pop-in komt te staan.
        Dit doe ik door de rij te pakken waar de knop zit de gebruikt word, en die html collectie om te zetten naar een array.
        Dan kan ik via die array, de innerHTML data pakken van de juiste velden, en ook de cover image string.
        Zodat ik niet een verzoek hoef te doen naar PhP, en de pop-in direct in beeld komt ipv eerst een pagina reload.
        Voor de cover image maak ik wel een nieuw element, en de submit knop krijgt een andere text als er wel of geen cover image is.
        Ook zit er nog een Naam/ISBN check in, om te kijken of de inhoud van die velden klopt, andere blijft de submit knop uit staan.
        Het weer aan zetten van die submit knop, zit in andere functies, naamCheck and isbnCheck.
 */
function albumBewerken(e) {
    let rowCol = document.getElementsByClassName('album-bewerken-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let div = rowArr[0].children[5];
    let form = document.getElementById('albumb-form');
    let covLab = document.getElementById("modal-form-albumB-cov-lab");
    let covInp = document.getElementById("modal-form-albumB-cov-lab").children[0];
    let albCov = document.getElementById("albumb-cover");

    // Normaal gesproken zijn de gegevens altijd aanwezig, en dus kan de submit knop altijd op aan gezet worden.
    submitButtBew.disabled = false;

    form[0].value = e.target.id;
    form[1].value = rowArr[0].children[2].innerHTML;
    form[2].value = rowArr[0].children[3].innerHTML;
    form[3].value = rowArr[0].children[4].innerHTML;
    form[4].value = "";
    form[5].value = rowArr[0].children[6].innerHTML;
    form[6].value = rowArr[0].children[7].innerHTML;
    
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

    window.location.assign('#albumb-pop-in');
}

/*  albumBewSubm(e):
        Deze functie stuurt de HTML form naar PhP, via de JS fetch functie.
        Dit kan eenvoudig door de form in zijn geheel, aan de FormData mee te geven.
        Als de response van PhP geen object is, is de actie geslaagd, en mag de pop-in gesloten worden nadat de melding is opgeslagen.
        Als de response wel een object is, word(en) de juiste melding(en) weer gegeven en blijft de pop-in open.
 */
function albumBewSubm(e) {
    let form = document.getElementById('albumb-form');
    let formData = new FormData(form);
    e.preventDefault();

    fetchRequest('albumBew', 'POST', formData)
    .then((data) => {
        if(typeof(data) !== 'object') {
            localStorage.setItem('fetchResponse', data);
            popInClose();
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

/*  albumVerwijderen(e):
        Onclick functie voor het verwijderen van een album, die de data als FormData naar PhP verstuurt.
        De response word opgeslagen in the localStorage, en er word een form gemaakt voor een pagina refresh.
        Die form bevat de data van de huidig geslecteerd Serie, zodat we terug komen op het zelfde scherm.
 */
function albumVerwijderen(e) {
    let rowCol = document.getElementsByClassName('album-bewerken-inhoud-'+e.target.id);
    let rowArr = Array.from(rowCol);
    let formData = new FormData();

    formData.append('serie-index', localStorage.huidigeIndex);
    formData.append('album-index', e.target.id);
    formData.append('album-naam', rowArr[0].children[2].innerHTML);

    fetchRequest('albumV', 'POST', formData)
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

/* Code refactor sectie, hier ben ik nog mee bezig, voor de nieuwe pagina opmaak. */
// 
//  Index van de tafel rij:
//      e.target.id
//
//  Test Code voor het weergeven van de formData inhoud.
//
//  for (const temp of formData.values()) {
//      console.log(temp);
//  }
//
/* TODO List:
        - Edit/clean-up comments, to much clutter atm left over from the concept/design stage.
 */

/* Globale variable voor in en buiten de init functie */
let formButt, formInput;

/* initGebruik():
        Deze functie word alleen uitgevoerd als de hele pagina is geladen.
        En regelt in principe alle listen events, terugkoppelingen, gebruiker validatie, en wat pagina opmaak.
        In de functie zelf staat wat precies waar voor is, en evt specifieke uitleg indien nodig.
 */
function initGebruik() {
    /* Alle benodigde voor alle Album Aanwezig switches */
    let chBox = document.getElementsByClassName("album-aanwezig-checkbox");
    let chBoxArr = Array.from(chBox);
    chBoxArr.forEach( (item, index, arr) => {
        arr[index].addEventListener("change", checkBox);
    });

    /* Alle benodigde voor de Serie Selecteren controlle */
    formButt = document.getElementById("serie-sel-subm");
    formInput = document.getElementById("serie-sel");
    formInput.addEventListener("change", selectEvent);
    formButt.disabled = true;

    /* Als er aangegeven is dat er een selectie is gemaakt, moet de tabel title ook mee veranderen */
    if(localStorage.huidigeSerie != null) {
        let selOptions = document.getElementsByClassName('serie-sel-opt');

        // Dit zorgt er voor dat de input altijd op de juiste serie staat.
        for(let i = 0; i < selOptions.length; i++) {
            if(selOptions[i].value == localStorage.huidigeSerie) {
                selOptions[i].selected = true;
            }
        }

        let wHeader = document.getElementById('weergave-header');
        wHeader.innerHTML = localStorage.huidigeSerie + ", en alle albums.";
    }

    /* Redirect naar de landings\beheer of gebruik pagina, als er geen gebruiker of ongeldige gebruiker is*/
    if(sessionStorage.gebruiker === null || sessionStorage.gebruiker === undefined) {
        window.location.assign('/');
    } else if(sessionStorage.gebruiker === 'admin@coltrack.nl') {
        window.location.assign('/beheer');
    } else {
        let formData = new FormData();
        formData.append('gebr_email', sessionStorage.gebruiker);

        /* JS fetch voor gebruikers validatie */
        fetchRequest('valUsr', 'POST', formData)
        .then((data) => {
            if(data !== "Valid User") {
                window.location.assign('/');
                sessionStorage.removeItem('gebruiker');
            }
        });
    }
    
    /* Check voor het weergeven van fetch responses na een re-direct */
    if(localStorage.fetchResponse !== "") {
        displayMessage(localStorage.fetchResponse);
        localStorage.removeItem("fetchResponse");
    }
}

/* selectEvent(e):
        Als er een geldige input is, zet ik de submit knop op aan.
        Anders gaat de submit knop op uit.
 */
function selectEvent(e) {
    if(formInput.value === "") {
        formButt.disabled = true;
    } else {
        formButt.disabled = false;
    }
}

/* selectSubm():
        Onclick functie voor het selecteren van een serie uit de controller drop-down.
        Ik gebruik een verstopte gebruikers data form, en in die form zet ik de gebruikers e-mail uit de session storage.
        En ik submit gewoon de hele form via HTML naar PhP, zodat er ook direct een refesh is.
 */
function selectSubm() {
    let form = document.getElementById('serie-sel-form');
    let gebrVeld = document.getElementById("serie-sel-data");
    gebrVeld.value = sessionStorage.gebruiker;

    form.submit();
}

/* albumZoek():
        De zoek functie, die op een per toetsslag event uitgevoerd word.
        Alle word omgezet naar upper case strings, ik kan ook evt nog iets met elke invoer doen via 'event'.
        Ik sla de input op en zet die om ('filter'), en pak alle tafel rijen, en vervolgens loop ik over die tafel rijen heen.
        Dan pak ik de album naam uit elke tafel rij, en vergelijk of er een overeenkomst is met de 'filter'.
        Als die overeenkomst er niet is, zet ik de display style of niks, en als die er wel is haal ik dat weg.
        Dit zorgt er voor dat alle items waar niet naar gezocht word, verstopt lijken, en alleen de overeenkomsten zichtbaar zijn.
        Op deze manier hoef ik met ook niet druk te maken over een minimale of maximale waarde voor het zoeken.
 */
function albumZoek(event) {
    let input = document.getElementById('album-zoek-inp');
    let filter = input.value.toUpperCase();
    let tafelRows = document.querySelectorAll('#album-tafel-inhoud');

    tafelRows.forEach((item, index, arr) => {
        let albumNaam = item.children[1].innerHTML;

        if(albumNaam.toUpperCase().indexOf(filter) > -1) {
            tafelRows[index].style.display = "";
        } else {
            tafelRows[index].style.display = "none";
        }

    });
}

/* checkBox(e):
        Deze functie is voor het luisterevent van de album checkboxes.
        Met de event data die meekomt, pak ik simpel weg de hele tafel rij uit de HTML pagina.
        En gebruik ik die data, om de FormData voor PhP te maken, en vervolgens via JS fetch te versturen.
        De terugkoppeling van in de response, geef ik direct weer via mijn displayMessage functie.
 */
function checkBox(e) {
    let temp = document.getElementsByClassName("album-tafel-inhoud-"+e.target.id);
    let tempArr = Array.from(temp);

    let formData = new FormData();
    formData.append('gebr_email', sessionStorage.gebruiker);
    formData.append('serie_naam', localStorage.huidigeSerie);
    formData.append('album_naam', tempArr[0].children[1].textContent);
    formData.append('aanwezig', e.target.checked);

    fetchRequest('albSta', 'POST', formData)
    .then((data) => {
        displayMessage(data)
    });
}
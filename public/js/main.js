/* Het document listen event:
        Deze functie, kijk of de huidige pagina geladen is, en voert iets uit als dat zo is.
        Voor de landingspagina, is dit heel eenvoudig, en voer ik direct de init functie voor die pagina uit.
        Voor de andere pagina's, kijk ik eerst of er een gebruikers update gedaan moet worden, en voer ik daarna pas de init fucntie uit.
        En voor het specifieke geval van de gebruiker zijn album status, kijk ik of de hele pagina een refresh nodig heeft.

        Dit zorgt er voor dat ik altijd de meest recente data heb uit de database, met het minimaale aantal pagina refreshes.
 */
document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        let gebrForm, gebrData;

        if(window.location.pathname === '/') {
            initLanding();
        } else if(window.location.pathname === '/gebruik') {
            if(sessionStorage.updateUser != null && sessionStorage.updateUser) {
                gebrForm = document.getElementById("gebr-data-form");
                gebrData = sessionStorage.gebruiker;
                document.getElementById("gebr-form-input").value = gebrData;

                sessionStorage.removeItem("updateUser");
                gebrForm.submit();
            }

            initGebruik();
        } else if(window.location.pathname === '/beheer') {
            if(sessionStorage.updateUser != null && sessionStorage.updateUser) {
                gebrForm = document.getElementById("gebr-data-form");
                gebrData = sessionStorage.gebruiker;
                document.getElementById("gebr-form-input").value = gebrData;

                sessionStorage.removeItem("updateUser");
                gebrForm.submit();
            }

            initBeheer();
        } else if(window.location.pathname == "/albSta") {
            if(localStorage.reloadPage != null && localStorage.reloadPage) {
                localStorage.removeItem('reloadPage');
                postForm('/gebruik', localStorage.huidigeSerie);
            }
        }
    }
}

/* fetchRequest(url, method, data):
        Deze functie is voor het aanvragen\versturen van data van/naar PhP, zonder een pagina refresh.
        De response word altijd terug gegeven als json naar de caller, zodat het daar veder verwerkt kan worden.
 */
async function fetchRequest(url=null, method=null, data=null ) {
    const response = await fetch(url, {
        method: method,
        body: data
    })

    return response.json();
}

/* logoff():
        De logoff functie is erg simpel, en verwijdert de gebruikers data, en redirect naar de landings pagina.
*/
function logoff() {
    sessionStorage.clear();
    localStorage.clear();
    window.location.assign('/');
}

/* postForm(path, param):
        Deze functie gebruik ik om form data naar PhP te sturen, voor het geval dat er echt een pagina refresh nodig is.
        Dit is altijd een POST request, omdat het altijd gaat om het vernieuwen van database gegevens.
        De enige toepassing tot dus ver, is als er een serie geselecteerd word.
 */
function postForm(path, param) {
    let method = 'post', form, hiddenField1, hiddenField2;

    form = document.createElement('form');
    form.setAttribute('method', method);
    form.setAttribute('action', path);

    hiddenField1 = document.createElement('input');
    hiddenField1.hidden = true;
    hiddenField1.setAttribute('name', 'serie-selecteren');
    hiddenField1.setAttribute('value', param);

    hiddenField2 = document.createElement('input');
    hiddenField2.hidden = true;
    hiddenField2.setAttribute('name', 'gebr-email');
    hiddenField2.setAttribute('value', sessionStorage.gebruiker);

    form.appendChild(hiddenField1);
    form.appendChild(hiddenField2);
    document.body.appendChild(form);

    form.submit();
}

/* replaceSpecChar(text):
        Deze functie doet niet meer dan speciale characters omzetten, zodat die juist kunnen worden weergegeven in de HTML.
        En word tot nu toe aleen gebruikt bij het bewerken van een serie, omdat ik daar gegevens uit de pagina haal en terug plak in een invoer veld.
 */
function replaceSpecChar(text) {
    return text.replaceAll('&amp;', '&').replaceAll('$lt;', '<').replaceAll('&gt;', '>').replaceAll('$quot;', '"').replaceAll('&#039;', "'");
}

/* displayMessage(text1, text2):
        Deze functie doet de terugkoppeling van PhP meldingen naar de gebruiker, via een verstopt element.
        Het concept is erg simpel, er zijn 2 bericht headers, en die hebben een opmaak die de container buiten beeld zet.
        Als er tekst mee gegeven wordt, dan verander ik de opmaak zodat het element in beeld komt.
        Dan zorg ik dat de meldingen in de header(s) gezet word.
        En dan start ik een timer van 3 seconden, die het element verstopt, en de berichten weer verwijdert.
        In het geval dat er geen meldingen zijn, verstop ik het element ook, dit was nodig voor een onverwachte uitkomst.
 */
function displayMessage(text1="", text2="") {
    let container = document.getElementById("message-pop-in");
    let header1 = document.getElementById("response-message1");
    let header2 = document.getElementById("response-message2");

    if(text1 !== "" || text2 !== "") {
        container.style.display = "block";
        container.style.top = "0%";
        container.style.zIndex = "3";

        if(text1 !== "") {
            header1.innerHTML = text1;
        }

        if(text2 !== "") {
            header2.innerHTML = text2;
        }

        setTimeout( function() {
            container.style.display = "none";
            container.style.top = "-10%";
            container.style.zIndex = "1";
            header1.innerHTML = "";
            header2.innerHTML = "";
        }, 3000);
    }
}
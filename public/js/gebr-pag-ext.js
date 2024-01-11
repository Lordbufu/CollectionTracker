// Variables shared between init and functions
let formButt, formInput;

// Init the required elements and events.
function initGebruik() {
    // Everything required for the Album switches.
    let chBox = document.getElementsByClassName("album-aanwezig-checkbox");
    let chBoxArr = Array.from(chBox);
    chBoxArr.forEach( (item, index, arr) => {
        arr[index].addEventListener("change", checkBox);
    });

    // All required for the Select Serie controlle.
    formButt = document.getElementById("serie-sel-subm");
    formInput = document.getElementById("serie-sel");
    formInput.addEventListener("change", selectEvent);
    formButt.disabled = true;

    // If there was a Serie selection made, i grab all selection options.
    if(localStorage.huidigeSerie != null) {
        let selOptions = document.getElementsByClassName('serie-sel-opt');

        // Loop over the selection options, and ensure its showing the right one as selected.
        for(let i = 0; i < selOptions.length; i++) {
            if(selOptions[i].value == localStorage.huidigeSerie) { selOptions[i].selected = true; }
        }

        // Then the header is changed to the new series name.
        let wHeader = document.getElementById('weergave-header');
        wHeader.innerHTML = localStorage.huidigeSerie + ", en alle albums.";
    }

    // A bunch of user checks/validations.
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
    
    // In same case i need to display a response after a refresh.
    if(localStorage.fetchResponse !== "") {
        displayMessage(localStorage.fetchResponse);
        localStorage.removeItem("fetchResponse");
    }
}

// If there is no input the button has to be disabled, other whise it should be enabled
function selectEvent(e) {
    if(formInput.value === "") {
        formButt.disabled = true;
    } else { formButt.disabled = false; }
}

// Ensure the user data is included in the form, and submit it.
function selectSubm() {
    let form = document.getElementById('serie-sel-form');
    let gebrVeld = document.getElementById("serie-sel-data");
    gebrVeld.value = sessionStorage.gebruiker;

    form.submit();
}

// The search function.
function albumZoek(event) {
    // Store the input, and convert to uppercase and table rows.
    let input = document.getElementById('album-zoek-inp');
    let filter = input.value.toUpperCase();
    let tafelRows = document.querySelectorAll('#album-tafel-inhoud');

    // Loop over the table rows,
    tafelRows.forEach((item, index, arr) => {
        // store each album name
        let albumNaam = item.children[1].innerHTML;

        // Look if any of the letter are also in the search input,
        if(albumNaam.toUpperCase().indexOf(filter) > -1) {
            // ensure the row is displayed if there is a match,
            tafelRows[index].style.display = "";
        // hide the row if there is no match.
        } else { tafelRows[index].style.display = "none"; }

    });
}

// Checkbox function for the listenEvent
function checkBox(e) {
    // Store the row the checkbox is on, and convert it to an array
    let temp = document.getElementsByClassName("album-tafel-inhoud-"+e.target.id);
    let tempArr = Array.from(temp);

    // Make form data for PhP
    let formData = new FormData();
    formData.append('gebr_email', sessionStorage.gebruiker);
    formData.append('serie_naam', localStorage.huidigeSerie);
    formData.append('album_naam', tempArr[0].children[1].textContent);
    formData.append('aanwezig', e.target.checked);

    // Send request to PhP and display user feedback.
    fetchRequest('albSta', 'POST', formData)
    .then((data) => { displayMessage(data) });
}
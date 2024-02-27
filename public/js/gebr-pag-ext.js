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

    // Adding a listen event to the album search input
    let input = document.getElementById('album-zoek-inp');
    input.addEventListener("input", albumZoek);

    // If there was a Serie selection made, i grab all selection options.
    if(localStorage.huidigeSerie) {
        let selOptions = document.getElementsByClassName('serie-sel-opt');

        // Loop over the selection options, and ensure its showing the right one as selected.
        for(let i = 0; i < selOptions.length; i++) {
            if(selOptions[i].value == localStorage.huidigeSerie) {
                selOptions[i].selected = true;
            }
        }

        // Then the header is changed to the new series name.
        let wHeader = document.getElementById('weergave-header');
        wHeader.innerHTML = localStorage.huidigeSerie + ", en alle albums.";
        // And remove the entry from localStorage to prevent artifact behavior.
        localStorage.removeItem('huidigeSerie');
    }

    // Display and remove the welcome message on login.
    if(localStorage.welcome) {
        displayMessage(localStorage.welcome);
        localStorage.removeItem("welcome");
    }
    
    // In same case i need to display a response after a refresh.
    if(localStorage.fetchResponse) {
        displayMessage(localStorage.fetchResponse);
        localStorage.removeItem("fetchResponse");
    }
}

// If there is no input the button has to be disabled, other whise it should be enabled
function selectEvent(e) {
    if(formInput.value === "") {
        formButt.disabled = true;
    } else {
        formButt.disabled = false;
    }
}

// Ensure the user data is included in the form, and submit it.
function selectSubm() {
    let form = document.getElementById('serie-sel-form');

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
        } else {
            tafelRows[index].style.display = "none";
        }
    });
}

// Checkbox function for the listenEvent
function checkBox(e) {
    // Store the row the checkbox is on, and convert it to an array
    let temp = document.getElementsByClassName("album-tafel-inhoud-"+e.target.id);
    let tempArr = Array.from(temp);
    
    // init new formdata, and append required the info.
    let formData = new FormData();
    formData.append('album_naam', tempArr[0].children[1].textContent);
    formData.append('aanwezig', e.target.checked);

    // Send request to PhP and display user feedback.
    fetchRequest('albSta', 'POST', formData)
    .then((data) => {
        displayMessage(data);
    });
}
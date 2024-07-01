/* Global variables required for serie-select controller */
let formButt, formInput;

/* The init function triggred on page-load. */
function initGebruik() {
    /* Assing a listenEvent to all checkboxes on the page */
    const chBox = document.getElementsByClassName("album-aanwezig-checkbox"), chBoxArr = Array.from(chBox);
    chBoxArr.forEach( (item, index, arr) => { arr[index].addEventListener( "change", checkBox ); } );

    /* Required elements and event for the serie-select controller */
    formButt = document.getElementById("serie-sel-subm"),  formInput = document.getElementById("serie-sel"), formInput.addEventListener("change", selectEvent), formButt.disabled = true;

    /* Listen Event for the album search option */
    document.getElementById('album-zoek-inp').addEventListener("input", albumZoek);

    /* Loop for detecting, displaying and removing welcome messages for the user */
    if(localStorage.welcome) { displayMessage(localStorage.welcome), localStorage.removeItem("welcome"); }
    
    /* Loop for detecting, displaying and removing fetchResponse (populated via the session now) */
    if(localStorage.fetchResponse) { displayMessage(localStorage.fetchResponse), localStorage.removeItem("fetchResponse"); }
}

/*  selectEvent(e): Enable or Disable form submit button, based on the formInput value from the serie-select dropdown */
function selectEvent(e) {
    if(formInput.value === "") {
        return formButt.disabled = true;
    } else { return formButt.disabled = false; }
}

/*  selectSubm(): Simply submits the serie-select form. */
function selectSubm() { return document.getElementById("serie-sel-form").submit(); }

// The search function.
/*  albumZoek(event): Searches the albums on page, matching them on a letter by letter basis. */
function albumZoek(event) {
    const input = document.getElementById("album-zoek-inp"), filter = input.value.toUpperCase(), tafelRows = document.querySelectorAll("#album-tafel-inhoud");
    tafelRows.forEach((item, index, arr) => {
        const albumNaam = item.children[1].innerHTML;
        if(albumNaam.toUpperCase().indexOf(filter) > -1) {
            return tafelRows[index].style.display = "";
        } else { return tafelRows[index].style.display = "none"; }
    });
}

/*  checkBoc(e): Checkbox listenEvent that simply submits the form. */
function checkBox(e) { return e.target.closest("form").submit(); }
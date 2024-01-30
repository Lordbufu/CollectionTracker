// temp test code
let html5QrcodeScanner, resultContainer, lastResult, countResults = 0;
// end of temp test code

// Wait for document to be loaded.
document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        let gebrForm, gebrData;

        // Test code for barscanning.
        resultContainer = document.getElementById('qr-reader-results');
        html5QrcodeScanner = new Html5QrcodeScanner( "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        // end of test code

        // If we are still on the landingpage, init the required code for that page.
        if(window.location.pathname === '/') {
            initLanding();
        // If we are on the user (/gebruik) page,
        } else if(window.location.pathname === '/gebruik') {
            // check if a user update was requested, and process said request;
            if(sessionStorage.updateUser != null && sessionStorage.updateUser) {
                gebrForm = document.getElementById("gebr-data-form");
                gebrData = sessionStorage.gebruiker;
                document.getElementById("gebr-form-input").value = gebrData;

                sessionStorage.removeItem("updateUser");
                gebrForm.submit();
            }

            // Init the required code for the user page
            initGebruik();
        // If we are on the admnin (/beheer) page,
        } else if(window.location.pathname === '/beheer') {
            // check if a user update was requested, and process said request;
            if(sessionStorage.updateUser != null && sessionStorage.updateUser) {
                gebrForm = document.getElementById("gebr-data-form");
                gebrData = sessionStorage.gebruiker;
                document.getElementById("gebr-form-input").value = gebrData;

                sessionStorage.removeItem("updateUser");
                gebrForm.submit();
            }

            // Init the required code for the admin page
            initBeheer();
        // If we are changing a album state, and there is a page reload requested.
        } else if(window.location.pathname == "/albSta") {
            if(localStorage.reloadPage != null && localStorage.reloadPage) {
                // We process said request, and remove the request from storage.
                localStorage.removeItem('reloadPage');
                postForm('/gebruik', localStorage.huidigeSerie);
            }
        }
    }
}

// async fetch request to avoid page reloading.
async function fetchRequest(url=null, method=null, data=null ) {
    const response = await fetch(url, {
        method: method,
        body: data
    })

    return response.json();
}

// Very simple logoff and page redirect.
function logoff() {
    sessionStorage.clear();
    localStorage.clear();
    window.location.assign('/');
}

// postForm(path, param):
//  path (string)   - The path the form needs to be submitted to.
//  param (string)  - The name of the serie that was selected.
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

// replaceSpecChar(text):
//  text (string)   - The text that needs to be cleaned/filtered.
function replaceSpecChar(text) {
    return text.replaceAll('&amp;', '&').replaceAll('$lt;', '<').replaceAll('&gt;', '>').replaceAll('$quot;', '"').replaceAll('&#039;', "'");
}

// displayMessage(text1, text2):
//  text1 (string)  - The first feedback text that needs to be displayed.
//  text2 (string)  - The second feedback text that needs to be displayed.
function displayMessage(text1="", text2="") {
    let container = document.getElementById("message-pop-in");
    let header1 = document.getElementById("response-message1");
    let header2 = document.getElementById("response-message2");

    if(text1 !== "" || text2 !== "") {
        // Making sure the element is visable on the page
        container.style.display = "block";
        container.style.top = "0%";
        container.style.zIndex = "3";

        if(text1 !== "") { header1.innerHTML = text1; }
        if(text2 !== "") { header2.innerHTML = text2; }

        // Start a timeout to hide the element and remove the feedback text.
        setTimeout( function() {
            container.style.display = "none";
            container.style.top = "-10%";
            container.style.zIndex = "1";
            header1.innerHTML = "";
            header2.innerHTML = "";
        }, 3000);
    }
}

// Test Code that is required in the init function of the page its being used on, so that the element are loaded.
//let html5QrcodeScanner = new Html5QrcodeScanner( "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
//html5QrcodeScanner.render(onScanSuccess, onScanFailure);

// Test Code fo barcode scanning
function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    if (decodedText !== lastResult) {
        ++countResults;
        lastResult = decodedText;
        resultContainer.innerHTML = JSON.stringify(decodedResult);
        // Handle on success condition with the decoded message.
        console.log(`Scan result ${decodedText}`, decodedResult);
    }
}

function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    // for example:
    console.warn(`Code scan error = ${error}`);
}
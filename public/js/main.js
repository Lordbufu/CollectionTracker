/* Globals for shared scripts, and check states for logic events. */
let user, localDevice, html5QrcodeScanner, naamChecked = false, isbnChecked = false;

document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        /* Store the device & user tag that was handed over from PhP. */
        if(localStorage.device) {
            localDevice = localStorage.device;
            localStorage.removeItem('device');
        }

        if(localStorage.user) {
            user = localStorage.user;
            localStorage.removeItem('user');
        }

        /* In a few specific cases, i need to redirect to the default home page, and also clear the anchor tag. */
        if(localStorage.reset) {
            window.location = '/';
            localStorage.removeItem('reset');
        }

        /* Deal with the scroll position triggers. */
        if(sessionStorage.scrollPos) {
            window.scrollTo(0, sessionStorage.scrollPos);

            if(window.location.hash === '#reeks-maken-pop-in') {
                dispatchInputEvent(localStorage.event);
            } else if(window.location.hash === '#items-maken-pop-in') {
                dispatchInputEvent(localStorage.event);
            }

            localStorage.removeItem('event');
            sessionStorage.removeItem('scrollPos');
        }

        /* Elements and listen events for pop-in buttons */
        const modalFormButt = document.getElementsByClassName('modal-form-button');
        const modalFormButtArr = Array.from(modalFormButt);
        for(key in modalFormButtArr) {
            modalFormButtArr[key].addEventListener('click', saveScroll);
        }

        const popInClButt = document.getElementsByClassName('modal-header-close');
        const clButtArr = Array.from(popInClButt);
        for(key in clButtArr) {
            clButtArr[key].addEventListener('click', saveScroll);
        }

        /* Initialize the static header & controller/menu bar for desktop devices. */
        if(localDevice === 'desktop') {
            initStatic();
        }

        // Temp code, incase i want to add JS based on the browser URL/pathname
        // if(window.location.pathname === '/gebruik') { console.log('gebruik'); }
        // if(window.location.pathname === '/beheer') { console.log('beheer'); }
    }
}

/*  dispatchInputEvent(caller):
        This function creates a new event, and dispaches said event, when the expected input is already filled in.
            caller (string) - A string that allows me to see what called the event.
 */
function dispatchInputEvent(caller) {
    let inputEvent = new Event ('input', {
        'bubbles': true,
        'cancelable': false
    });

    if(caller == 'reeks-maken' && window.location.hash === '#reeks-maken-pop-in') {
        return reeksMakenInput.dispatchEvent(inputEvent);
    } else if(caller == 'item-maken' && window.location.hash === '#items-maken-pop-in') {
        itemNaamInp.dispatchEvent(inputEvent);
        return itemIsbnInp.dispatchEvent(inputEvent);
    }
}

/*  saveScroll(e):
        This function saves the vertical scroll position before a page-reload, so the page is set back to that once fully loaded.
        And it also stores a trigger in the browser session storage, to help dispatch events since most code is session based now.
            e (object)  - The event that was assigned to this listen event, used to see what element triggered it.
 */
function saveScroll(e) {
    caller = e.target.id;
    sessionStorage.setItem('scrollPos', window.scrollY);

    if(e.target.className === 'item-bew-butt button') {
        caller = e.target.className;
    }

    switch(caller) {
        case 'reeks-pop-req':
        case 'reeks-edit-butt':
        case 'reeks-maken-submit':
            localStorage.setItem('event', 'reeks-maken');
            break;
        case 'item-pop-req':
        case 'item-toev-subm':
        case 'item-bew-butt button':
        case 'item-maken-submit':
        case 'prevSubm':
        case 'item-isbn-search-butt':
            localStorage.setItem('event', 'item-maken');
            break;
    }
}

/*  itemVerwijderen(e):
    A simple confirmation check, that displays the reeks name, and triggers the submit button base on said confirmation.
        rowCol  - The table row in witch the button was pressed.
        rowArr  - The table row in array format for easier access.
        conf    - The confirmation box when the button is pressed.

    Return Value: Boolean.
*/
function itemVerwijderen(e) {
    const rowCol = document.getElementsByClassName('item-tafel-inhoud-' + e.target.id);
    const rowArr = Array.from(rowCol);
    const conf = confirm('Weet u zeker dat het Item: ' + rowArr[0].children[2].innerHTML + '\n Verwijderen moet worden ?');

    if(conf) {
        return true;
    } else {
        if(sessionStorage.scrollPos)  {
            sessionStorage.removeItem('scrollPos');
        }
        return false;
    }
}

/*  naamCheck(e):
        This function listens to input changes in serie/item name fields, and evaluates if valid and enables/disabled the submit button.
        It also change the input style, based on the evaluation, and works in tandem with isbnCheck(e) if there is a isbn input field.
            e       - The listen event object.
            uInp    - The user input, set from the listen event object.
            elStyle - The element style of the input element, set from the listen event object.

        External Variables (defined globally, instanced in the init):
            createSerieSubm / editSerieSubm - Create/Edit subm buttons for series.
            createAlbumSubm / editAlbumSubm - Create/Edit subm buttons for albums.
        
        Return Value: None.
 */
function naamCheck(e) { // store current input value and element style.
    const uInp = e.target.value;
    const elStyle = e.target.style;

    if(uInp !== '' && uInp !== null && uInp !== undefined) {    // change style and switch checked state.
        elStyle.outline = '3px solid green';
        naamChecked = true;

        if(e.target.id === 'reeks-maken-naam') {    // enable reeks maken button.
            return reeksMakenSubmit.disabled = false;
        }

        if(e.target.id === 'item-maken-naam') {
            if(isbnChecked) {   // enable item maken button.
                return itemMakenSubm.disabled = false;
            }
    }
    } else {    // disable buttons and change style.
        naamChecked = false;

        if(e.target.id === 'reeks-maken-naam') {
            reeksMakenInput.disabled = true;
        }

        if(e.target.id === 'item-maken-naam') {
            itemMakenSubm.disabled = true;
        }
        
        return elStyle.outline = '3px solid red';
    }
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
function isbnCheck(e) { // store current input value and element style.
    const uInp = e.target.value
    const elStyle = e.target.style;

    if(uInp !== '' && uInp !== null && uInp !== undefined) {    // filter isbn and check for letters.
        const isbn = uInp.replace(/-/g, '');
        const filter = /[a-zA-z]/g;
        const letters = filter.test(isbn);

        if(!letters && isbn === '0' || isbn.length === 10 || isbn.length === 13) {  //change style & state, and store filtered isbn.
            elStyle.outline = '3px solid green';
            e.target.value = isbn;
            isbnChecked = true;
            if(naamChecked) {   // enable button.
                return itemMakenSubm.disabled = false;
            }
            return;
        } else {    // disable button and change style & state.
            isbnChecked = false;
            itemMakenSubm.disabled = true;
            return elStyle.outline = '3px solid red';
        }
    } else {    // disable button and change style & state.
        isbnChecked = false,
        itemMakenSubm.disabled = true;
        return elStyle.outline = '3px solid red';
    }
    
}
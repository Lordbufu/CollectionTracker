/* Globals for shared scripts, and check states for logic events. */
let user, localDevice, html5QrcodeScanner, naamChecked = false, isbnChecked = false;

document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        /* Store the device & user tag that was handed over from PhP. */
        if(localStorage.device) { localDevice = localStorage.device, localStorage.removeItem('device'); }
        if(localStorage.user) { user = localStorage.user, localStorage.removeItem('user'); }
        /* In a few specific cases, i need to redirect to the default home page, and also clear the anchor tag. */
        if(localStorage.reset) { window.location = '/', localStorage.removeItem('reset'); }
        /* Deal with the scroll position triggers, dispatching the correct events, based on the URL anchor tag (hash). */
        if(sessionStorage.scrollPos) { window.scrollTo(0, sessionStorage.scrollPos); if(window.location.hash === '#reeks-maken-pop-in') { dispatchInputEvent(localStorage.event); } else if(window.location.hash === '#items-maken-pop-in') { dispatchInputEvent(localStorage.event); } localStorage.removeItem('event'), sessionStorage.removeItem('scrollPos'); }
        /* Elements and listen events for pop-in submit buttons */
        const modalFormButt = document.getElementsByClassName('modal-form-button');
        const modalFormButtArr = Array.from(modalFormButt);
        for(key in modalFormButtArr) { modalFormButtArr[key].addEventListener('click', saveScroll); }
        /* Elements and listen events for pop-in close buttons */
        const popInClButt = document.getElementsByClassName('modal-header-close');
        const clButtArr = Array.from(popInClButt);
        for(key in clButtArr) { clButtArr[key].addEventListener('click', saveScroll); }
        /* Initialize the static header & controller/menu bar for desktop devices. */
        if(localDevice === 'desktop') { initStatic(); }
    }
}

/*  dispatchInputEvent(caller):
        This function creates a new event, and dispaches said event, when the expected input is already filled in.
            caller (string) - A string that allows me to see what called the event.
 */
function dispatchInputEvent(caller) {
    let inputEvent = new Event ('input',{'bubbles': true,'cancelable': false});
    if(caller == 'reeks-maken' && window.location.hash === '#reeks-maken-pop-in') { return reeksMakenInput.dispatchEvent(inputEvent); } else if(caller == 'item-maken' && window.location.hash === '#items-maken-pop-in') { return itemNaamInp.dispatchEvent(inputEvent), itemIsbnInp.dispatchEvent(inputEvent); }
}

/*  saveScroll(e):
        This function saves the vertical scroll position before a page-reload, so the page is set back to that once fully loaded.
        And it also stores a trigger in the browser session storage, to help dispatch events since most code is session based now.
            e (object)  - The event that was assigned to this listen event, used to see what element triggered it.
 */
function saveScroll(e) {
    caller = e.target.id, sessionStorage.setItem('scrollPos', window.scrollY);
    if(e.target.className === 'item-bew-butt button') { caller = e.target.className; }

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

/* naamCheck(e): Simple validate for the name inputs, with some visual feedback, working in tandem with isbnCheck for items. */
function naamCheck(e) {
    const uInp = e.target.value, elStyle = e.target.style;
    /* Check if there is an input to check. */
    if(uInp !== '' && uInp !== null && uInp !== undefined) {
        /* Only trigger the main path if the input is a similar lenght as PhP is expecting. */
        if(uInp.length >= 7 && uInp.length <= 35) {
            elStyle.outline = '3px solid green', naamChecked = true;
            /* Enable the pop-in submit button, for items only if the isbn checked tag is also true. */
            if(e.target.id === 'reeks-maken-naam') { return reeksMakenSubmit.disabled = false; }
            if(e.target.id === 'item-maken-naam') { if(isbnChecked) { return itemMakenSubm.disabled = false; } }
            return; // required to skip the failed conditions, that used to be in else statements.
        }
    }
    /* Fail condition path, disable pop-in submit buttons depending on target id, set tag to false, and give red outline. */
    naamChecked = false;
    if(e.target.id === 'reeks-maken-naam') { reeksMakenInput.disabled = true; }
    if(e.target.id === 'item-maken-naam') { itemMakenSubm.disabled = true; }
    return elStyle.outline = '3px solid red';
}

/* isbnCheck(e): Validate the isbn input, and allow submition if the value is 0 or the length is 10 or 13 numbers, work in tandem with naamCheck(e). */
function isbnCheck(e) {
    const uInp = e.target.value, elStyle = e.target.style;
    /* Check if there is an input to check. */
    if(uInp !== '' && uInp !== null && uInp !== undefined) {
        /* Replace any any '-' with nothing, create a letter filter, and check if there are no letters. */
        const isbn = uInp.replace(/-/g, ''), filter = /[a-zA-z]/g, letters = filter.test(isbn);
        /* If there are no letters, and the isbn is either '0' or has the length of 10 or 13 numbers. */
        if(!letters && isbn === '0' || isbn.length === 10 || isbn.length === 13) {
            /* Set the checked tag, and change the style and fill in the filtered isbn. */
            elStyle.outline = '3px solid green', e.target.value = isbn, isbnChecked = true;
            /* Only if the name is also validated, the submit button is enabled. */
            if(naamChecked) { return itemMakenSubm.disabled = false; }
            return; // required to skip the failed conditions, that used to be in else statements.
        }
    }
    /* In all other cases the tag is false, the outline is red, and the submit button is disabled */
    return isbnChecked = false, itemMakenSubm.disabled = true, elStyle.outline = '3px solid red';
}
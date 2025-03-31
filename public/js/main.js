/* Globals for shared scripts, and check states for logic events. */
let user, localDevice, html5QrcodeScanner;

document.onreadystatechange = () => {
    if(document.readyState === 'complete') {
        /* Store the device & user tag that was handed over from PhP. */
        if(localStorage.device) { localDevice = localStorage.device, localStorage.removeItem('device'); }
        if(localStorage.user) { user = localStorage.user, localStorage.removeItem('user'); }
        /* In a few specific cases, i need to redirect to the default home page, and also clear the anchor tag. */
        if(localStorage.reset) { window.location = '/', localStorage.removeItem('reset'); }
        /* Deal with the scroll position triggers, dispatching the correct events, based on the URL anchor tag (hash). */
        if(sessionStorage.scrollPos) {
            window.scrollTo(0, sessionStorage.scrollPos);
            if(window.location.hash === '#reeks-maken-pop-in') { dispatchInputEvent(localStorage.event); } else if(window.location.hash === '#items-maken-pop-in') { dispatchInputEvent(localStorage.event); }
            localStorage.removeItem('event'), sessionStorage.removeItem('scrollPos');
        }
        /* Elements and listen events for pop-in submit buttons */
        const modalFormButt = document.getElementsByClassName('modal-form-button'), modalFormButtArr = Array.from(modalFormButt);
        for(key in modalFormButtArr) { modalFormButtArr[key].addEventListener('click', saveScroll); }

        /* Elements and listen events for pop-in close buttons */
        const popInClButt = document.getElementsByClassName('modal-header-close'), clButtArr = Array.from(popInClButt);
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
    let inputEvent = new Event ('input', { 'bubbles': true, 'cancelable': false });
    if(caller == 'reeks-maken' && window.location.hash === '#reeks-maken-pop-in') {
        return reeksMakenInput.dispatchEvent(inputEvent);
    } else if(caller == 'item-maken' && window.location.hash === '#items-maken-pop-in') {
        return itemNaamInp.dispatchEvent(inputEvent), itemIsbnInp.dispatchEvent(inputEvent);
    }
}

/*  saveScroll(e):
        This function saves the vertical scroll position before a page-reload, so the page is set back to that once fully loaded.
        And it also stores a trigger in the browser session storage, to help dispatch events since most code is session based now.
            e (object)  - The event that was assigned to this listen event, used to see what element triggered it.
 */
function saveScroll(e) {
    /* Set the correct caller value, and store a scrollPos event in the browser session storage. */
    caller = e.target.id; if(e.target.className === 'item-bew-butt button') { caller = e.target.className; }
    sessionStorage.setItem('scrollPos', window.scrollY);

    /* Store the correct event in the browser local storage, based on who the caller was. */
    switch(caller) {
        case 'reeks-pop-req':
        case 'reeks-edit-butt':
        case 'reeks-maken-submit':
            return localStorage.setItem('event', 'reeks-maken');
        case 'item-pop-req':
        case 'item-toev-subm':
        case 'item-bew-butt button':
        case 'item-maken-submit':
        case 'prevSubm':
        case 'item-isbn-search-butt':
            return localStorage.setItem('event', 'item-maken');
    }
}

/* naamCheck(e): Simple validate for the name inputs, with some visual feedback, working in tandem with isbnCheck for items. */
function naamCheck(e) {
    const uInp = e.target.value, elStyle = e.target.style;
    /* Check if there is an input to check. */
    if(uInp !== '' && uInp !== null && uInp !== undefined) {
        /* Only trigger the main path if the input is a similar lenght as PhP is expecting. */
        if(valLength('long', uInp.length)) {
            elStyle.outline = '3px solid green';
            if(typeof naamChecked !== 'undefined') { naamChecked = true; }
            /* Disabled the correct submit button, based on the caller id. */
            if(e.target.id === 'reeks-maken-naam') { return reeksMakenSubmit.disabled = false; }
            if(e.target.id === 'item-maken-naam') { if(isbnChecked) { return itemMakenSubm.disabled = false; } }
            return;
        }
    }
    /* Fail condition path, disable pop-in submit buttons depending on target id, set tag to false, and give red outline. */
    if(typeof naamChecked !== 'undefined') { naamChecked = false; }
    if(e.target.id === 'reeks-maken-naam') { reeksMakenSubmit.disabled = true; }
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
        if(!letters && isbn === '0' || valLength('special', isbn.length)) {
            /* Set the checked tag, and change the style and fill in the filtered isbn. */
            elStyle.outline = '3px solid green', e.target.value = isbn, isbnChecked = true;
            /* Only if the name is also validated, the submit button is enabled. */
            if(naamChecked) { return itemMakenSubm.disabled = false; }
            return;
        }
    }
    /* In all other cases the tag is false, the outline is red, and the submit button is disabled */
    return isbnChecked = false, itemMakenSubm.disabled = true, elStyle.outline = '3px solid red';
}

/*  checkBox(e): This function checks the users input on the user agreement checkbox, similar to the pwCheck function. */
function checkBox(e) {
    if(e.target.checked) {
        checkbChecked = e.target.checked;

        if(pwChecked && nameChecked) {
            submButt.disabled = false;
        }
    } else {
        checkbChecked = e.target.checked;
        submButt.disabled = true;
    }

    return;
}

/*  validateInput(e):
        All form validation that i could consolidate, without making a huge JS mess of things.
        Some of these are just simple visual feedback features, while some or there to also prevent form submission.
 */
function validateInput(e) {
    caller = e.target.id, value = e.target.value, style = e.target.style;

    /* If the caller is from a user-name field: */
    if(caller === 'nameInp') {
        /* Check name length, set a green change the checked state, if the other checkstates are also true i enable the submit button. */
        if(valLength('regular', value.length)) {
            style.outline = '3px solid green', nameChecked = true;
            /* If the password and check box are also checked, enable the form submit button. */
            if(checkState('pwChecked') && checkState('checkbChecked')) { submButt.disabled = false; }
        /* If the lenght isnt long enough, set a red outline change the check state, and disable the submit button. */
        } else { style.outline = '3px solid red', nameChecked = false, submButt.disabled = true; }
        return;
    }

    /* If the called was user password input: */
    if(caller === 'pwInp1' || caller === 'pwInp2') {
        /* make sure pwchecked starts as false, if not set before */
        if(typeof pwChecked !== 'undefined') { pwChecked = false; }
        /* always disable the button, so logic will resolve its state correctly. */
        submButt.disabled = true;
        /* Check if it was the first input, */
        if(caller === 'pwInp1') {
            /* when not empty and long enough, */
            if(value !== '' && valLength('regular', value.length)) {
                /* If the second input has not outline set, change that to red to indicate the user should fill it in; */
                if(!pwInp2.style.outline) { pwInp2.style.outline = '3px solid red'; }
                /* If the caller has a outline set, make it green instead of red. */
                if(style.outline) { style.outline = '3px solid green'; }
            }

            /* If the input isnt long enough, */
            if(!valLength('regular', value.length)) {
                /* change the password checked flag (if defined) to false and disable the submit button, */
                if(typeof pwChecked !== 'undefined') { pwChecked = false; }
                submButt.disabled = true;
                /* and this style was already set, change it to red instead; */
                if(style.outline) { style.outline = '3px solid red'; }
            }

            /* If both input value match, we fire allGrPwOutl() ('All Green Password Outlines'). */
            if(pwInp1.value === pwInp2.value && valLength('regular', value.length) && valLength('regular', pwInp2.value.length)) { allGrPwOutl(); }
        }
        /* Check if it was the second input, */
        if(caller === 'pwInp2') {
            /* If the input isnt long enough, */
            if(!valLength('regular', value.length)) {
                /* change the password checked flag to false, */
                if(typeof pwChecked !== 'undefined') { pwChecked = false; }
                /* and this style was already set, change it to red instead; */
                if(style.outline) { style.outline = '3px solid red'; }
                /* disable the submit button. */
                submButt.disabled = true;
            }
            /* If the input length is validated, and its style was already set, change it to green instead. */
            if(valLength('regular', value.length)) { if(style.outline) { style.outline = '3px solid green'; } }
            /* If both input value match, we fire allGrPwOutl() ('All Green Password Outlines'). */
            if(value === pwInp1.value && valLength('regular', value.length) && valLength('regular', pwInp1.value.length)) { allGrPwOutl(); }
        }
        return;
    }

    /* If the caller was the autheur input: */
    if(caller === 'autheurs') {
        /* Check if there is a value, validate its length, and set a green outline. */
        if(value !== '' && valLength('long', value.length)) { style.outline = '3px solid green'; }
        /* If validation failed, set a red outline. */
        if(!valLength('long', value.length)) { style.outline = '3px solid red'; }
        return;
    }

    /* If the caller was the opmerking input: */
    if(caller === 'reeksOpm' || caller === 'itemOpm') {
        /* Check if there is a value, validate its length, and set a green outline. */
        if(value !== '' && valLength('vLong', value.length)) { style.outline = '3px solid green'; }
        /* If validation failed, set a red outline. */
        if(!valLength('vLong', value.length)) { style.outline = '3px solid red'; }
        return;
    }
}

/*  allGrPwOutl():
        This function deal with giving the password inputs the correct feedback color, changing the checked state.
        But also when all states are set and true, it enabled the form submit button.
*/
function allGrPwOutl() {
    pwInp1.style.outline = '3px solid green', pwInp2.style.outline = '3px solid green', pwChecked = true;
    if(checkState('pwChecked') && checkState('checkbChecked') && checkState('nameChecked')) { submButt.disabled = false; }
    return;
}

/*  checkState(name):
        A function to check if certain expected states are not only set, but als if they are true or not.
            name (String)   - The name of the state variable i want to check.
        
        Return Value: Boolean.
 */
function checkState(name) {
    switch(name) {
        case 'pwChecked':
            if(typeof pwChecked !== 'undefined' && pwChecked) { return true; } else { return false; }
        case 'nameChecked':
            if(typeof nameChecked !== 'undefined' && nameChecked) { return true; } else { return false; }
        case 'checkbChecked':
            if(typeof checkbChecked !== 'undefined' && checkbChecked) { return true; } else { return false; }
    }
}

/*  valLength(type, length):
        A function to validate input lengths, to potentially give some extra visual feedback to the user.
            type (String)   - The type of validation i want to use, inline with the PhP form validation lengths.
            length (Int)    - The length of the input string.
        
        Return Value: Boolean.
 */
function valLength(type, length) {
    switch(type) {
        case 'special': // special String (10-13 length): item isbn.
            if(length === 10 || length === 13) { return true; } else { return false; }
        case 'regular': // regular String (7-35 length): user name + passwords.
            if(length >= 7 && length <=35) { return true; } else { return false; }
        case 'long':    // long String (7-50 length): Item\Reeks name + autheur.
            if(length >= 7 && length <= 50) { return true; } else { return false; }
        case 'vLong':   // vLong String (1-254 length): Item\Reeks description.
            if(length >= 1 && length <= 254) { return true; } else { return false; }
    }
}

/* aResetBev(e): This function asks for user confirmation, before submitting the Admin password reset form. */
function aResetBev(e) { const conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?"); if(conf) { return true; } else { return false; } }
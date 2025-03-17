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

        /* Deal with the scroll position triggers, dispatching the correct events, based on the URL anchor tag (hash). */
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

        /* Elements and listen events for pop-in submit buttons */
        const modalFormButt = document.getElementsByClassName('modal-form-button');
        const modalFormButtArr = Array.from(modalFormButt);

        for(key in modalFormButtArr) {
            modalFormButtArr[key].addEventListener('click', saveScroll);
        }

        /* Elements and listen events for pop-in close buttons */
        const popInClButt = document.getElementsByClassName('modal-header-close');
        const clButtArr = Array.from(popInClButt);
        for(key in clButtArr) {
            clButtArr[key].addEventListener('click', saveScroll);
        }

        /* Initialize the static header & controller/menu bar for desktop devices. */
        if(localDevice === 'desktop') {
            initStatic();
        }
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
        reeksMakenInput.dispatchEvent(inputEvent);
        return;
    } else if(caller == 'item-maken' && window.location.hash === '#items-maken-pop-in') {
        itemNaamInp.dispatchEvent(inputEvent);
        itemIsbnInp.dispatchEvent(inputEvent);
        return;
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

/* naamCheck(e): Simple validate for the name inputs, with some visual feedback, working in tandem with isbnCheck for items. */
function naamCheck(e) {
    const uInp = e.target.value;
    const elStyle = e.target.style;

    /* Check if there is an input to check. */
    if(uInp !== '' && uInp !== null && uInp !== undefined) {
        /* Only trigger the main path if the input is a similar lenght as PhP is expecting. */
        if(uInp.length >= 7 && uInp.length <= 50) {
            elStyle.outline = '3px solid green';
            naamChecked = true;

            /* Enable the pop-in submit button, for items only if the isbn checked tag is also true. */
            if(e.target.id === 'reeks-maken-naam') {
                reeksMakenSubmit.disabled = false;
                return;
            }

            if(e.target.id === 'item-maken-naam') {
                if(isbnChecked) {
                    itemMakenSubm.disabled = false;
                    return;
                }
            }

            return; // required to skip the failed conditions, that used to be in else statements.
        }
    }

    /* Fail condition path, disable pop-in submit buttons depending on target id, set tag to false, and give red outline. */
    naamChecked = false;

    if(e.target.id === 'reeks-maken-naam') {
        reeksMakenInput.disabled = true;
    }

    if(e.target.id === 'item-maken-naam') {
        itemMakenSubm.disabled = true;
    }

    elStyle.outline = '3px solid red';
    return;
}

/* isbnCheck(e): Validate the isbn input, and allow submition if the value is 0 or the length is 10 or 13 numbers, work in tandem with naamCheck(e). */
function isbnCheck(e) {
    const uInp = e.target.value;
    const elStyle = e.target.style;

    /* Check if there is an input to check. */
    if(uInp !== '' && uInp !== null && uInp !== undefined) {
        /* Replace any any '-' with nothing, create a letter filter, and check if there are no letters. */
        const isbn = uInp.replace(/-/g, '');
        const filter = /[a-zA-z]/g;
        const letters = filter.test(isbn);

        /* If there are no letters, and the isbn is either '0' or has the length of 10 or 13 numbers. */
        if(!letters && isbn === '0' || isbn.length === 10 || isbn.length === 13) {
            /* Set the checked tag, and change the style and fill in the filtered isbn. */
            elStyle.outline = '3px solid green';
            e.target.value = isbn;
            isbnChecked = true;

            /* Only if the name is also validated, the submit button is enabled. */
            if(naamChecked) {
                itemMakenSubm.disabled = false;
                return;
            }

            return; // required to skip the failed conditions, that used to be in else statements.
        }
    }
    /* In all other cases the tag is false, the outline is red, and the submit button is disabled */
    isbnChecked = false;
    itemMakenSubm.disabled = true;
    elStyle.outline = '3px solid red';
    return;
}

/*  checkBox(e): This function checks the users input on the user agreement checkbox, similar to the pwCheck function. */
function checkBox(e) {
    if(e.target.checked) {
        checkbChecked = e.target.checked;

        if(pwChecked) {
            submButt.disabled = false;
            return;
        }

    } else {
        checkbChecked = e.target.checked;
        submButt.disabled = true;
        return;
    }
}

/* Simple validation event, to change colors or all inputs that dint have a event yet, but are included in php formvalidation. */
function validateInput(e) {
    caller = e.target.id;
    value = e.target.value;
    style = e.target.style;

    /* Check user name length, set a green or red outline based on the validation. */
    if(caller === 'nameInp') {
        if(regStrLength(value.length)) {
            style.outline = '3px solid green'
            return;
        } else {
            style.outline = '3px solid red'
        }
    }

    /* If the called was user password input: */
    if(caller === 'pwInp1' || caller === 'pwInp2') {
        if(typeof pwChecked !== 'undefined') {
            pwChecked = false;
        }
        
        submButt.disabled = true;

        /* Check if it was the first input, */
        if(caller === 'pwInp1') {
            /* when not empty and long enough, set the second input to have a red outline if non was set. */
            if(value !== '' && regStrLength(value.length)) {
                if(!pwInp2.style.outline) {
                    pwInp2.style.outline = '3px solid red';
                }
            }

            /* If the input isnt long enough, */
            if(!regStrLength(value.length)) {
                /* change the password checked flag to false and disable the submit button, */
                if(typeof pwChecked !== 'undefined') {
                    pwChecked = false;
                }
                submButt.disabled = true;
                /* and this style was already set, change it to red instead; */
                if(style.outline) {
                    style.outline = '3px solid red';
                }
            }

            /* If the input is long enough, and the outline was already set, change it to green again. */
            if(regStrLength(value.length)) {
                if(style.outline) {
                    style.outline = '3px solid green';
                }
            }

            /* If both input value match, we fire allGrPwOutl() ('All Green Password Outlines'). */
            if(pwInp1.value === pwInp2.value && regStrLength(value.length) && regStrLength(pwInp2.value.length)) {
                allGrPwOutl();
            }
        }

        /* Check if it was the second input, */
        if(caller === 'pwInp2') {
            /* If the input isnt long enough, */
            if(!regStrLength(pwInp2.value.length)) {
                /* change the password checked flag to false and disable the submit button, */
                if(typeof pwChecked !== 'undefined') {
                    pwChecked = false;
                }
                
                /* and this style was already set, change it to red instead; */
                if(style.outline) {
                    style.outline = '3px solid red';
                }

                submButt.disabled = true;
            }

            /* If both input value match, we fire allGrPwOutl() ('All Green Password Outlines'). */
            if(pwInp1.value === pwInp2.value && regStrLength(value.length) && regStrLength(pwInp1.value.length)) {
                allGrPwOutl();
            }
        }

        return;
    }
}

/*  allGrPwOutl():
        This function sets all password inputs to green, enables the submit button if the checkbox is checked, and set the pwChecked flag to true.
        And should is only used in combination with validateInput(e), to reduce code clutter.
*/
function allGrPwOutl() {
    pwInp1.style.outline = '3px solid green';
    pwInp2.style.outline = '3px solid green';
    pwChecked = true;

    if(typeof checkbChecked !== 'undefined' && checkbChecked) {
        submButt.disabled = false;
    }

    return;
}

/* Validator conditions:
    Special String: 10-13 length.
        - item isbn
    Regular String: 7-35 length.
        - user name + passwords.
    Longer String: 7-50 length.
        - Item\Reeks name + autheur.
    VLong String: 1-254 length.
        - Item/Reeks Description.
*/

/* Function to check isbn string lengths. */
function specStrLength(length) {
    if(length === 10 && length === 13) {
        return true;
    }
    
    return false;
}

/* Function to check user and pw string lengths. */
function regStrLength(length) {
    if(length >= 7 && length <=35) {
        return true;
    }

    return false;
}

/* Function to check item/reeks name and autheur lengths. */
function longStrLength(length) {
    if(length >= 7 && length <= 50) {
        return true;
    }

    return false;
}

/* Function to check item/reeks descriptions. */
function vLongStrLength(length) {
    if(length >= 1 && length <= 254) {
        return true;
    }

    return false;
}

/* aResetBev(e): This function asks for user confirmation, before submitting the Admin password reset form. */
function aResetBev(e) {
    const conf = confirm("Weet u zeker dat het wachtwoord van: "+ emailField.value +" veranderd moet worden ?");
    if(conf) {
        return true;
    } else {
        return false;
    }
}
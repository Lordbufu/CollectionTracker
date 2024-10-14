/* Global variables required for event functions */
let pwField1, pwField2, pwChecked, inputChecked, submButton;

function initLanding() {
    /* Elements, events and states required for account registration */
    pwField1 = document.getElementById("register1");
    pwField2 = document.getElementById("register2");
    submButton = document.getElementById("reg-submit");
    const chBox = document.getElementById("chBox");
    const resetLink = document.getElementById("reset-link");
    pwField2.addEventListener("input", pwCheck);
    chBox.addEventListener("change", checkBox);
    submButton.disabled = true;
    pwChecked, inputChecked = false;

    /* The error loop, for detecting and displaying erors during account registration */
    if(localStorage.userError1 != null || localStorage.userError2 != null) {
        if(localStorage.userError1 != null) {
            if(localStorage.userError2 != null) {
                displayMessage(localStorage.userError1, localStorage.userError2);
                localStorage.removeItem("userError1");
                localStorage.removeItem("userError2");
            } else {
                displayMessage(localStorage.userError1);
                localStorage.removeItem("userError1");
            }
        }
        if(localStorage.userError2 != null) {
            displayMessage(localStorage.userError2);
            localStorage.removeItem("userError2");
        }
    }

    /* Display feedback, if user was created */
    if(localStorage.userCreated != null) {
        displayMessage(localStorage.userCreated);
        localStorage.removeItem("userCreated");
    }

    /* Display login failed message, and show the lorem ipsum resetlink */
    if(localStorage.loginFailed != null) {
        resetLink.style.display = "block";
        if(window.location.hash === "#login-pop-in") {
            displayMessage(localStorage.loginFailed);
            localStorage.removeItem("loginFailed");
        }
    }
}

/*  pwCheck(e): This function checks the users password input, and changes the button states and input style. */
function pwCheck(e) {
    if(pwField1.value === pwField2.value) {
        pwChecked = true, e.target.style.outline = "3px solid green";
        if(inputChecked) { return submButton.disabled = false; }
    } else {
        pwChecked = false, e.target.style.outline = "3px solid red";
        return submButton.disabled = true;
    }
}

/*  checkBox(e): This function checks the users input on the user agreement checkboc, similar to the pwCheck function. */
function checkBox(e) {
    if(e.target.checked) {
        inputChecked = e.target.checked;
        if (pwChecked) { return submButton.disabled = false; }
    } else {
        inputChecked = e.target.checked;
        return submButton.disabled = true;
    }
}
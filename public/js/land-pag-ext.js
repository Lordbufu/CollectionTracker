// Variables shared between init and function
let pwField1, pwField2, pwChecked, inputChecked, submButton;

function initLanding() {
    // Elements required for account registration.
    pwField1 = document.getElementById("register1");
    pwField2 = document.getElementById("register2");
    submButton = document.getElementById("reg-submit");
    let chBox = document.getElementById("chBox");
    let resetLink = document.getElementById("reset-link");

    // Listen events
    pwField2.addEventListener("input", pwCheck);
    chBox.addEventListener("change", checkBox);

    // Button and value check states
    submButton.disabled = true;
    pwChecked, inputChecked = false;

    // Check if registering an account had errors
    if(localStorage.userError1 != null || localStorage.userError2 != null) {
        // If there the first error happened, 
        if(localStorage.userError1 != null) {
            // check for a second and display and remove both.
            if(localStorage.userError2 != null) {
                displayMessage(localStorage.userError1, localStorage.userError2);
                localStorage.removeItem("userError1");
                localStorage.removeItem("userError2");
            // Else just display and remove the one.
            } else {
                displayMessage(localStorage.userError1);
                localStorage.removeItem("userError1");
            }
        }

        // If there as only the second error, display and remove only that.
        if(localStorage.userError2 != null) {
            displayMessage(localStorage.userError2);
            localStorage.removeItem("userError2");
        }
    }

    // Check if user was created during register, and display user feedback.
    if(localStorage.userCreated != null) {
        displayMessage(localStorage.userCreated);
        localStorage.removeItem("userCreated");
    }

    // Check if login failed, and adjust pop-in where required.
    if(localStorage.loginFailed != null) {
        resetLink.style.display = "block";
        // If we are still on the login-pop-in, display the feedback and remove it from storage.
        if(window.location.hash === "#login-pop-in") {
            displayMessage(localStorage.loginFailed);
            localStorage.removeItem("loginFailed");
        }
    }
}

// Function associated with the password input field.
function pwCheck(e) {
    // check if the input matches
    if(pwField1.value === pwField2.value) {
        // set the checked state and create visual user feedback
        pwChecked = true;
        e.target.style.outline = "3px solid green";
        // enable the submit button if checkbox was also checked
        if(inputChecked) {
            submButton.disabled = false;
        }
    } else {
        // set the checked state and create visual user feedback
        pwChecked = false;
        e.target.style.outline = "3px solid red";
        // disable the submit button
        submButton.disabled = true;
    }
}

// Function associated with the user agreement checkbox.
function checkBox(e) {
    // Check the checkbox state
    if(e.target.checked) {
        // pass on the state
        inputChecked = e.target.checked;
        // enable submit button is pw was checked
        if (pwChecked) {
            submButton.disabled = false;
        }
    } else {
        // pass on the state
        inputChecked = e.target.checked;
        // disable the submit button
        submButton.disabled = true;
    }
}
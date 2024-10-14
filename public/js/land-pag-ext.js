/* Global for the landingpage */
let pwField1, pwField2, pwChecked, inputChecked, submButton;

function initLanding() {
    /* Elements required for account registration */
    pwField1 = document.getElementById( "register1" );
    pwField2 = document.getElementById( "register2" );
    submButton = document.getElementById( "reg-submit" );
    const chBox = document.getElementById( "chBox" );
    const resetLink = document.getElementById( "reset-link" );

    /* Events required for account registration */
    pwField2.addEventListener( "input", pwCheck );
    chBox.addEventListener( "change", checkBox );

    /* States required for account registration */
    submButton.disabled = true;
    pwChecked = false;
    inputChecked = false;

    /* The error loop, for detecting and displaying erors during account registration */
    if( localStorage.userError1 != null || localStorage.userError2 != null ) {

        if( localStorage.userError1 != null ) {

            if( localStorage.userError2 != null ) {
                displayMessage( localStorage.userError1, localStorage.userError2 );
                localStorage.removeItem( "userError1" );
                localStorage.removeItem( "userError2" );
                return;
            } else {
                displayMessage( localStorage.userError1 );
                localStorage.removeItem( "userError1" );
                return;
            }

        }

        if(localStorage.userError2 != null) {
            displayMessage(localStorage.userError2);
            localStorage.removeItem("userError2");
            return;
        }

    }

    /* Display feedback, if user was created */
    if( localStorage.userCreated != null ) {
        displayMessage( localStorage.userCreated );
        localStorage.removeItem( "userCreated" );
        return;
    }

    /* Display login failed message, and show the lorem ipsum resetlink */
    if( localStorage.loginFailed != null ) {
        resetLink.style.display = "block";

        if( window.location.hash === "#login-pop-in" ) {
            displayMessage( localStorage.loginFailed );
            localStorage.removeItem( "loginFailed" );
            return;
        }
    }

}

/*  pwCheck(e): This function checks the users password input, and changes the button states and input style. */
function pwCheck( e ) {
    if( pwField1.value === pwField2.value ) {
        pwChecked = true;
        e.target.style.outline = "3px solid green";

        if( inputChecked ) {
            submButton.disabled = false;
            return;
        }

    } else {
        pwChecked = false;
        submButton.disabled = true;
        e.target.style.outline = "3px solid red";
        return;
    }
}

/*  checkBox(e): This function checks the users input on the user agreement checkboc, similar to the pwCheck function. */
function checkBox( e ) {
    if( e.target.checked ) {
        inputChecked = e.target.checked;

        if( pwChecked ) {
            submButton.disabled = false;
            return;
        }

    } else {
        inputChecked = e.target.checked;
        submButton.disabled = true;
        return;
    }
}
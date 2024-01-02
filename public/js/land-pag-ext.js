/* TODO List:
        - Edit/clean-up comments, to much clutter atm left over from the concept/design stage.
 */

/* Globale waardes voor in en buiten de init functie */
let pwField1, pwField2, pwChecked, inputChecked, submButton;

function initLanding() {
    /* De nodige elementen voor de account registratie */
    pwField1 = document.getElementById("register1");
    pwField2 = document.getElementById("register2");
    submButton = document.getElementById("reg-submit");
    let chBox = document.getElementById("chBox");

    /* Luister Events voor de account registratie */
    pwField2.addEventListener("input", pwCheck);
    chBox.addEventListener("change", checkBox);

    /* Submit knop en check variablen voor de registratie */
    submButton.disabled = true;
    pwChecked, inputChecked = false;

    /* Login mislukt condities en terugkoppeling */
    if(localStorage.loginFailed != null) {
        let resetLink = document.getElementById("reset-link");
        resetLink.style.display = "block";

        if(window.location.hash === "#login-pop-in") {
            displayMessage(localStorage.loginFailed);
            localStorage.removeItem("loginFailed");
        }
    }
}

/* pwCheck(e):
        Deze functie vergelijk de ingevoerde wachtwoorden, en doet iets op basis van die evaluatie.
        Als de wachtwoorden gelijk zijn, krijgt de input een groene border, en word pwChecked waar.
        Waneer de inputChecked ook waar is, word de submit knop aangezet.
        Als de wachtwoorden niet gelijk zijn, krijgt de input en rode border we word pwChecked niet waar.
        En voor de goede orde, word de submit knop uitgezet.

        Deze functie werkt in tandem met checkBox(e), zodat die same bepalen waneer de gebruiker de gegevens kan versturen.
 */
function pwCheck(e) {
    if(pwField1.value === pwField2.value) {
        pwChecked = true;
        e.target.style.outline = "3px solid green";
        if(inputChecked) {
            submButton.disabled = false;
        }
    } else {
        pwChecked = false;
        submButton.disabled = true;
        e.target.style.outline = "3px solid red";
    }
}

/* checkBox(e):
        Deze functie kijkt of men akoord ging met de gebruiker overeenkomst.
        Als de event target checked is, sla ik dat op in inputChecked.
        Waneer de pwChecked waarde ook waar is, zet ik de submit knop aan.
        Als de event target niet checkd is, slad ik dat ook op in inputChecked.
        En voor de goed orde, zet ik de submit knop uit.

        Deze functie werkt in tandem met pwCheck(e), zodat die same bepalen waneer de gebruiker de gegevens kan versturen.
*/
function checkBox(e) {
    if(e.target.checked) {
        inputChecked = e.target.checked;
        if (pwChecked) {
            submButton.disabled = false;
        }
    } else {
        inputChecked = e.target.checked;
        submButton.disabled = true;
    }
}
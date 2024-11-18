<?php
/*  TODO\Reminder List:
        - Review how i could best add the other classes i wrote, so i can have all errors in here.
        - Review if switch is the best way to check the error request, so far performance seems good so far.
 */
namespace App\Core;

/*  Reminder of the error array structure, that is required to display the errors:
        [ "error" => [ "fetchResponse" => { Message that needs to be displayed } ] ];
 */

class Errors {
    /* User related errors */
    protected $userNameErr = "Deze gebruiker bestaat al, kies alstublieft een andere gebruikers naam.";
    protected $userEmailErr = "E-mail adres reeds ingebruik, gebruik alstublieft een andere.";
    protected $noUserErr = "Geen gebruikers gevonden, neem contact op met uw Administrator!";
    protected $credError = "Uw inlog gegevens zijn niet correct, probeer het nogmaals!!";
    protected $authFailed = "Toegang geweigerd, Account authentication mislukt !";
    protected $rightsError = "U heeft geen rechten om deze pagina te bezoeken !!";

    /* Load errors */
    protected $loadFail = "Het laad process for deze items is gefaald,neem contact op met de Administrator als dit blijft gebeuren!";
    protected $idToBig = "Er waren te veel indentifiers, neem contact op met de administrator als dit blijft gebeuren!";
    protected $noItems = "Geen items geladen, neem contact op met de Administrator als dit blijft gebeuren!";

    /* Search errors */
    protected $idNotValid = "De id is niet volledige voor het zoeken, neem contact op met de Administrator als dit blijft gebeuren!";
    protected $attrFail = "Kan de gevraagde eigenschap niet vinden, neem contact op met de Administrator als dit blijft gebeuren!";

    /* Errors when checking database entries against provided data */
    protected $duplName = "Deze naam is al in gebruik, kies aub een andere naam!";
    protected $noUserId = "Er is geen user id gevonden, neem contact op met de Administrator als dit blijft gebeuren!";
    protected $noProcess = "Collectie data kan niet verwerkt worden, neem contact op met de Administrator als dit blijft gebeuren!";

    /* Database errors */
    protected $dbFail = "Er was een database error, neem contact op met de administrator als dit blijft gebeuren!";

    /* Default, Generic errors, and single errors */
    protected $defaultErr = "Een onbekend probleem is opgevangen, neem contact op met de Administrator als dit blijft gebeuren!";
    protected $deviceErr = "Onbekend apparaat gevonden, neem contact op met uw Administrator!";
    protected $UsrAgeErr = "Geen user agent gevonden, toegang geweigert!";

    /*  getError($name):
            This function matches a string to error case, so i can return the correct globally stored string.
                $name (string)  - Optional error identifier string, when empty triggers the default switch/error.

            Return value: String
     */
    public function getError( $name=null ) {
        switch( $name ) {
            /* User related errors */
            case "userNameErr":
                return $this->userNameErr;
            case "userEmailErr":
                return $this->userEmailErr;
            case "noUserErr":
                return $this->noUserErr;
            case "credError":
                return $this->credError;
            case "authFailed":
                return $this->authFailed;
            case "rightsError":
                return $this->rightsError;
            /* Album, Serie and Collection related errors */
            case "load":
                return $this->loadFail;
            case "noItems":
                return $this->noItems;
            case "dupl":
                return $this->duplName;
            case "idNotVal":
                return $this->idNotValid;
            case "attr":
                return $this->attrFail;
            case "db":
                return $this->dbFail;
            case "noUserId":
                return $this->noUserId;
            case "idToBig":
                return $this->idToBig;
            /* Collection specific error */
            case "noProc":
                return $this->noProcess;
            /* Default, Generic errors, and single errors */
            case "UsrAgeErr":
                return $this->UsrAgeErr;
            case "deviceErr":
                return $this->deviceErr;
            /* Default error that should never be used/reached */
            default:
                return $this->defaultErr;
        }
    }
}
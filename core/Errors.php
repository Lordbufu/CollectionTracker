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

    /* Album related errors ? */

    /* Load errors */
    protected $loadFail = "The loading process for items failed, plz contact your Administrator if this keeps happening!";
    protected $idToBig = "Er waren te veel indentifiers, neem contact op met de administrator als dit blijft gebeuren!";
    protected $noItems = "No items loaded, plz contact your Administrator if this keeps happening!";

    /* Search errors */
    protected $idNotValid = "The provided id was not valid for finding attributes, plz contact your Administrator if this keeps happening!";
    protected $attrFail = "Failed to find the requested attribute, plz contact your Administrator if this keeps happening!";

    /* Errors when checking database entries against provided data */
    protected $duplName = "This name was already used, plz pick another name!";
    protected $noUserId = "No user id was found, plz contact your Administrator if this keeps happening!";
    protected $noProcess = "Can't process collection data, plz contact your Administrator if this keeps happening!";

    // Database errors ?
    protected $dbFail = "Er was een database error, neem contact op met de administrator als dit blijft gebeuren!";

    /* Default, Generic errors, and single errors */
    protected $defaultErr = "Een onbekend probleem is opgevangen, neem contact op met de administrator als dit blijft gebeuren!";
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
            /* Album related errors */
            case "load":                    // For the class load functions
                return $this->loadFail;
            case "noItems":                 // For functions using the class global load function
                return $this->noItems;
            case "dupl":                    // For inserting items that have duplicate fields like name
                return $this->duplName;
            case "idNotVal":                // For functions using a identifier
                return $this->idNotValid;
            case "attr":                    // For the attribute search functions
                return $this->attrFail;
            case "db":                      // For when a db calls fail (querrybuilder calls), replacing the PDO error ¿.
                return $this->dbFail;
            case "noUserId":                // For when there is no user id when changing collection states.
                return $this->noUserId;
            case "noProc":                  // When evaluating a collection item.
                return $this->noProcess;
            case "idToBig":                 // If the id for loading items has to many pairs
                return $this->idToBig;
            /* Default, Generic errors, and single errors */
            case "UsrAgeErr":
                return $this->UsrAgeErr;
            case "deviceErr":
                return $this->deviceErr;
            default:                        // Default error that should never be used
                return $this->defaultErr;
        }
    }
}
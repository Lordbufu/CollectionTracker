<?php

/*  $_SESSION Structure:
        The session storage structure, is designed around ease of use, and hopefully reduce the need of front-end processing.
        Much like most uses of session storage, it will play a large role in user verification across pages, but also simply used to required templates.
        I personally think a good way to view a session storage, is like a bit of memory, that stores things that i need on the fly.
        Below a more detailed outline of what my session storage is likely to contain, some of the '_flash' tags have been ommited to reduce clutter.

            'user' memory:
                User memory is there to validate the user and its rights, but also used to load various user specific resources.
                    'rights'    - A String with the user there rights, as stored in the database.
                    'id'        - A INT value that represents there index as stored in the databse.

            'page-data' memory:
                The data required to populate tables that represent a collection, reeks or items list.
                Often way to much data to store in the browser itself, and requires frequent updates, to refelect changes made by the user.
                    'huidige-reeks'   - The reeks name that is currently selected, used for header and certain logic operation.
                    'reeks'           - The loaded 'series' data, required to make selections on page.
                    'items'           - The loaded 'album' data, used to display items in a table when a 'serie' is selected.
                    'collecties'      - The loaded 'collection' data, used to display a user collection from a specific 'serie'.

            '_flash' memory:
                The idea behind this, it to have place for temp data, that is used for minor page logic.
                Think about loading pop-ins, re-filling previous form input (sanitised), or feedback messages.
                In many cases this dat is only required once, but likely needs to be preserved across page-refreshes and redirects.
                Other then that, the data is removed as soon as its no longer relevant, or when the user submits for example a form.
                To outline the most relevant tags:
                    'feedback'      - User feedback messages, for telling the user what happened.
                    'oldForm'       - Previous filled in form data, that has been sanitised before returning.
                    'oldItem'       - For when a item/reeks is being edited, this will contain the most current data to pre-fill the forms.
                    'isbn-choices'  - A choice of titles, if a scanned bar-code provided more then 1 match (admin only). (potentially redundant)
                    'tags'          - A few simple examples (sanitised if it was a user input):
                                'pop-in'    - A array with the pop-in name that needs to be required.
                                'redirect'  - A simple indicator that the user was redirected, used to preserve _flash data.
                                'rNaam'     - The name the user had filled in on the controller bar for making a reeks.
 */

namespace App\Core;

class SessionMan {
    /* Original Code from the project */
    protected $savePath = '../tmp/sessions/';
    protected $browser;

    /*  configSession():
            Set all required session settings and paths, session files are stored in a temp folder, this requires seperate php instances to work properly.\
            For now a session_start() is still included, to always start the session as soon as possible.
            And it also contains a bit of header code, to resolve a issue with certain browsers no dealing well with session flashing (firefox for example).
     */
    public function configSession() {
        $adress = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 10);
        ini_set('session.gc_maxlifetime', 1800);
        ini_set('session.user_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_trans_sid', 1);
        ini_set('session.referer_check', $adress);
        ini_set('session.cache_limiter', 'private');
        ini_set('session.sid_length', 128);
        ini_set('session.side_bits_per_character', 6);
        ini_set('session.hash_function', 'sha512');

        if(!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777, true);
            session_save_path($this->savePath);
        } else {
            session_save_path($this->savePath);
        }

        session_start();

        // Cant use this in the live version atm, so i have uncommneted it untill i know a better solution
        // /* Set browser name if not set, requires 'browscap' file to work, cause it needs to parse the user-agent */
        // if(!isset($this->browser)) {
        //     $this->setBrowser($_SERVER['HTTP_USER_AGENT']);
        // }

        // /* If the browser is firefox, change the cache controle to solve JS issues. */
        // if($this->browser === 'Firefox') {
        //     header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        //     header("Cache-Control: post-check=0, pre-check=0", false);                  
        //     header("Pragma: no-cache");
        // }

        // Debug section, to see if browser detection was the problem.
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);                  
        header("Pragma: no-cache");

        return;
    }

    /*  setBrowser($agent):
            This is a temp test function, that uses the 'HTTP_USER_AGENT' to parse the browser name.
            This requirs an up-to-date browscap.ini file, to be included in the php install, so for my live enviroment this is not yet included.
            In the future this function migh even just be removed, depending on how i can solve the caching issues, that seem to come with session flashing.
     */
    protected function setBrowser($agent) {
        $this->browser = get_browser($agent, TRUE)['browser'];
        return;
    }

    /*  setVariable($data):
            Desgined to append data to session data keys, since i have to do this a lot, i made a function for it.
                $name (string)              - The name of the first session data key.
                $data (assoc/multiD array)  - Data that needs to be added to the session.

            Return Type: None.
     */
    public function setVariable($name, $data) {
        if(isset($_SESSION[$name])) {
            foreach($data as $key => $value) {
                $_SESSION[$name][$key] = $value;
            }
        } else {
            foreach($data as $key => $value) {
                $_SESSION[$name][$key] = $value;
            }
        }

        return;
    }

    /*  remVar($name, $keys):
            This function was created to remove specific session data, so i can reset triggers when there no longer required.
            Normaly clearing the _flash memory is enough, but some logic might require more specific actions, to reset a certain states and/or behaviors.
                $name (String)      - The name of the Session store the item(s) are in.
                $key (String\Array) - The content i want removed/reset, where the $keys array holds simple strings, that associate with the session keys.

            Return Value: None.
     */
    public function remVar($name, $keys) {
        if(!is_array($keys)) {
            unset($_SESSION[$name][$keys]);
        } else {
            foreach($_SESSION[$name] as $oKey) {
                foreach($keys as $iKey) {
                    if($oKey == $iKey) {
                        unset($_SESSION[$name][$iKey]);
                    }
                }
            }
        }

        return;
    }

    /*  flash($key, $value):
            Flash temp data into the session, so it can stay for a limited amount of time, and be used across page-refreshes.
                $sKey   (string/assoc or multi/assoc array) - Either a key i want to set, or a array that needs to be set.
                $sValue (null/string/assoc array)           - The value i want to store, or a array that needs to be nested.

            Return Value: none.
     */
    public function flash($sKey, $sValue=null) {
        /* In some cases a 'flash' array was easier, this loops over said associative array ($sKey) and loads it properly into the flash. */
        if($sValue == null) { 
            foreach($sKey as $key => $value) {
                $_SESSION['_flash'][$key] = $value;
            }
        /* In other cases its either a key + value, or key + associative array, so the loop changes a bit. */
        } else {
            if(is_array($sValue)) {
                foreach($sValue as $lKey => $lValue) {
                    $_SESSION['_flash'][$sKey][$lKey] = $lValue;
                }
            } else {
                $_SESSION['_flash'][$sKey] = $sValue;
            }
        }

        return;
    }

    /*  unflash():
            Remove all flash data from session.
     */
    public function unflash() {
        unset($_SESSION['_flash']);
        return;
    }

    /*  flush():
            Function to simple clear the entire $_SESSION variable.
     */
    public function flush() {
        session_unset();
        return;
    }

    /*  destroy():
            To properly end a session, we need to first unset the session variables (flush()), and set the cookie to isntantly expire.
            Destroy the session, and return the boolean of said function.

            Return Type: Boolean.
     */
    public function destroy() {
        static::flush();

        $params = session_get_cookie_params();

        setcookie(
            'PHPSESSID',
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );

        return session_destroy();
    }
}
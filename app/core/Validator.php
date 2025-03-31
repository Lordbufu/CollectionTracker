<?php

namespace App\Core;

class Validator {
    /*  string($value, $min=1, $max=INF):
            A function to validate user input string lengths, where the min and max count can be changed from the default.
                $value (String) - The string we want to check the length of.
                $min (Int)      - The min length the string should have, defaulting to 1.
                $max (Int/INF)  - The max length the string should have, defaulting to infinite.
            
            Return Value: Boolean.
     */
    public static function string($value, $min=1, $max=INF) {
        $value = trim($value);

        return strlen($value) >= $min && strlen($value) <= $max;
    }

    /*  email($value):
            This function simply checks, if the provided input is a valid email adress.
                $value (String) - The user input that was in the POST data.

            Return Value: Boolean.
     */
    public static function email(string $value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /*  pwSecure($value):
            This function attempt to check if the user input pw is considered 'secure' (not very complex atm).
                $value (String) - The user input password, as was presented in the POST data.
            
            Return Value: Boolean.
     */
    public static function pwSecure(string $value): bool {
        return !ctype_alnum($value);
    }

    /*  complex($value):
            This function checks if the password is complex enough, i only used a basic regex nothing to complex.
            The regex explained:
                - Has minimum 8 characters in length. Adjust it by modifying {8,}
                - At least one uppercase English letter. You can remove this condition by removing (?=.*?[A-Z])
                - At least one lowercase English letter.  You can remove this condition by removing (?=.*?[a-z])
                - At least one digit. You can remove this condition by removing (?=.*?[0-9])
                - At least one special character,  You can remove this condition by removing (?=.*?[#?!@$%^&*-])
            
            Return Value: Boolean.
     */
    public static function complex(string $value): bool {
        $regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{7,}$/";
        return perg_match($regex, $value);
    }
}
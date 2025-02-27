<?php

namespace App\Core;

use App\Core\App;

class FileHandler {
    protected $file;
    protected $tempFile;
    protected $convFile;

    /*  setFile($file): */
    protected function setFile($file) {
        /* Set global file with parameter file, */
        $this->file = $file;

        /* return FALSE if no array was set, */
        if(!is_array($this->file)) { return FALSE; }

        /* return true if all seems well. */
        return TRUE;
    }

    /*  extractData(): */
    protected function extractData() {
        /* Try to set all relevant info to the tempFile variable. */
        $this->tempFile['name'] = basename($this->file['name']);
        $this->tempFile['type'] = pathinfo($this->tempFile['name'], PATHINFO_EXTENSION);
        $this->tempFile['content'] = file_get_contents($this->file['tmp_name']);

        /* Return if the the tempFile is now an array. */
        return is_array($this->tempFile);
    }

    /*  setString() */
    protected function setString() {
        /* Prepare the string in 2 parts, appending information from the tempFile variable. */
        $dataPart = 'data:image/' . $this->tempFile['type'];
        $charsetPart = ';charset=utf8;base64,' . base64_encode($this->tempFile['content']);

        /* Combine both halfs into a full base64 string. */
        $this->convFile = $dataPart . $charsetPart;

        /* Return if a string was made. */
        return is_string($this->convFile);
    }

    /*  procFile($inpFile): */
    public function procFile($inpFile) {
        /* If the provided data is not a array, and has a error code, */
        if(!is_array($inpFile)) {
            /* Return a costum feedback error for the user. */
            return App::resolve('errors')->getError('fileHand', 'no-file');
        }

        /* Attempt to set the file to the global store. */
        if($this->setFile($inpFile)) {
            /* If the data cant be extracted, return a costum feedback error for the user. */
            if(!$this->extractData()) {
                return App::resolve('errors')->getError('fileHand', 'proc-fail');
            }
        }

        /* If a string is set, return the string or a feedback error. */
        if($this->setString()) {
            return (is_string($this->convFile)) ? $this->convFile : App::resolve('errors')->getError('fileHand', 'no-string');
        }
    }

    //  TODO: Write error condition for this, so i dont store odd strings that are not images ¿
    /*  procUrl($url): */
    public function procUrl($url) {
        /* Init curl to request the image data from the Google api url. */
        $ch = curl_init();

        /* Some curl stuff i copt and pasted, not 100% sure what it does. */
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        /* Save the data curl extracted, and close curl to finalize it. */
        $data = curl_exec($ch);
        curl_close($ch);

        /* Return the base64 converted string, that can be stored in the database, and show in a html <img> tag. */
        return 'data:image/jpeg;charset=utf8;base64,' . base64_encode($data);
    }
}
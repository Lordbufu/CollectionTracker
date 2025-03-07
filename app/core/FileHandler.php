<?php

namespace App\Core;

use App\Core\App;

class FileHandler {
    protected $file;
    protected $tempFile;
    protected $convFile;

    /*  setFile($file):
            With this function i simply set the file variable, to the file being requested for processing (always images ¿).
                $file (file)    - The file being requested to processing.

            Return Value: Boolean.
     */
    protected function setFile($file) {
        $this->file = $file;

        if(!is_array($this->file)) {
            return FALSE;
        }

        return TRUE;
    }

    /*  extractData():
            Here i attempt to extract he usefull data from a image file, so i can convert it to be stored in the database.

            Return Value: Boolean.
     */
    protected function extractData() {
        $this->tempFile['name'] = basename($this->file['name']);
        $this->tempFile['type'] = pathinfo($this->tempFile['name'], PATHINFO_EXTENSION);
        $this->tempFile['content'] = file_get_contents($this->file['tmp_name']);

        return is_array($this->tempFile);
    }

    /*  setString():
            This function converts the image data, to a useable base64 string, that can be stored and displayed.
                $dataPart (String)      - The data part of the string, saying its and image and what type of image.
                $charsetPart (String)   - The charset part, where the base64 converted data gets concatinated.

                Return Value: String.
     */
    protected function setString() {
        $dataPart = 'data:image/' . $this->tempFile['type'];
        $charsetPart = ';charset=utf8;base64,' . base64_encode($this->tempFile['content']);

        $this->convFile = $dataPart . $charsetPart;

        return is_string($this->convFile);
    }

    /*  procFile($inpFile):
            This function is the controller side of the file processing, and is used to trigger the protected functions above.
                $inpFile (File/Array)   - The file as presented by the HTML input.

            Return Value:
                On success: String.
                On failure: Associative Array .
     */
    public function procFile($inpFile) {
        if(!is_array($inpFile)) {
            return App::resolve('errors')->getError('fileHand', 'no-file');
        }

        if($this->setFile($inpFile)) {
            if(!$this->extractData()) {
                return App::resolve('errors')->getError('fileHand', 'proc-fail');
            }
        }

        if($this->setString()) {
            return (is_string($this->convFile)) ? $this->convFile : ['error' => App::resolve('errors')->getError('fileHand', 'no-string')];
        }
    }

    /*  procUrl($url):
            A function to process images from a url link, converting it to a base64 string, to store and display.
                $url (String)           - Url string as provided by the Google API.
                $ch (Object)            - A curl stream to read the url data.
                $data (Object/Array)    - The image data as extracted from the Google API link.
            
            Return Value: base64 String.
     */
    public function procUrl($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return 'data:image/jpeg;charset=utf8;base64,' . base64_encode($data);
    }
}
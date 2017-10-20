<?php

class TV_WebFormDownload_IndexController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("titlename", array(
            "label" => $this->__("Titlename"),
            "title" => $this->__("Titlename")
        ));

        $this->renderLayout();
    }

    function downloadfileAction() {

        $path = Mage::getBaseDir('media') . '/downloads/';
        $files = scandir($path);
        for ($i = 0; $i < count($files); $i++) {
            if (!in_array($files[$i], array(".", "..", "cafeideas.zip"))) {
                if (file_exists($path . $files[$i])) {
                    $filename = $files[$i];
                    header("Content-Description: File Transfer");
                    header("Content-Type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"$filename\"");
                    readfile($filename);
                }
            }
        }
        exit();
    }

    function downloadfilezipAction() {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        $path = Mage::getBaseDir('media') . DS . 'downloads' . DS;
        $pathzip = Mage::getBaseDir('media') . DS . 'downloads' . DS . 'cafeideas.zip';
        $files = scandir($path);
        $arr_files = array();
        for ($i = 0; $i < count($files); $i++) {
            if (!in_array($files[$i], array(".", "..", "cafeideas.zip"))) {
                if (file_exists($path . $files[$i])) {
                    $arr_files[] = $path . $files[$i];
                }
            }
        }
        if (count($arr_files) > 0) {
            if ($this->createZip($arr_files, $pathzip, false)) {
                $this->downloadFile($pathzip);
            }
        }
        exit();
    }

    function addZipFile($archive, $file, $newName) {
        $zip = new \ZipArchive();
        if ($zip->open($archive) === TRUE) {
            $zip->addFile($file, $newName);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    function createZip($files = array(), $destination = '', $overwrite = false) {

        $valid_files = array();
        //if files were passed in...
        if (is_array($files)) {
            //cycle through each file
            $i = 0;
            foreach ($files as $file) {
                //make sure the file exists

                if (file_exists($file)) {
                    $itemFile = new \stdClass();
                    $itemFile->pathFile = $file;
                    $itemFile->nameFile = basename($file);
                    $valid_files[$i] = $itemFile;
                    $i++;
                }
            }
        }

        //if we have good files...
        if (count($valid_files)) {
            //create the archive
            $zip = new \ZipArchive();
            $property = $overwrite ? \ZipArchive::OVERWRITE : \ZipArchive::CREATE;
            if ($zip->open($destination, $property) !== true) {
                return false;
            }

            //add the files
            foreach ($valid_files as $file) {
                $zip->addFile($file->pathFile, $file->nameFile);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            //close the zip -- done!
            $zip->close();

            //check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }

    public function downloadFile($fullPath, $ext = 'zip') {
        // Must be fresh start
        if (headers_sent())
            die('Headers Sent');
        // Required for some browsers
        if (ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');

        // File Exists?

        if (file_exists($fullPath)) {
            // Parse Info / Get Extension
            $fsize = filesize($fullPath);
            //$path_parts = pathinfo($fullPath);
            //$ext = strtolower($path_parts["extension"]);
            // Determine Content Type
            switch ($ext) {
                case "pdf": $ctype = "application/pdf";
                    break;
                case "exe": $ctype = "application/octet-stream";
                    break;
                case "zip": $ctype = "application/zip";
                    break;
                case "doc": $ctype = "application/msword";
                    break;
                case "xls": $ctype = "application/vnd.ms-excel";
                    break;
                case "ppt": $ctype = "application/vnd.ms-powerpoint";
                    break;
                case "gif": $ctype = "image/gif";
                    break;
                case "png": $ctype = "image/png";
                    break;
                case "jpeg":
                case "jpg": $ctype = "image/jpg";
                    break;
                default: $ctype = "application/force-download";
            }

            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false); // required for certain browsers
            header("Content-Type: $ctype");
            header("Content-Disposition: attachment; filename=\"" . basename($fullPath) . "\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $fsize);
            ob_clean();
            flush();
            readfile($fullPath);
            exit();
        } else
            die('File Not Found');
    }

}

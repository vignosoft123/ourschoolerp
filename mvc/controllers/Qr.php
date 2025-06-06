<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qr extends CI_Controller {

    public function show()
    {
        $path = APPPATH . 'images/qr.jpg'; // APPPATH = mvc/
        // echo $path;die;
        if (file_exists($path)) {
            header('Content-Type: image/jpeg');
            readfile($path);
        } else {
            show_404();
        }
    }

    public function pqr(){
        $path = APPPATH . 'images/pqr.jpg'; // APPPATH = mvc/
        // echo $path;die;
        if (file_exists($path)) {
            header('Content-Type: image/jpeg');
            readfile($path);
        } else {
            show_404();
        }
    }

}

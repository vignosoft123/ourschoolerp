<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    spl_autoload_register(function($className) {
        if ( strpos($className, 'CI_') !== 0 ) {
            $file = APPPATH . 'libraries/' . $className . '.php';
            if ( file_exists($file) && is_file($file) ) {
                @include_once( $file );
            }
        }
    });

    $route['version']            = 'app/version';
    $route['global_payment/new']                       = 'global_payment_new/index';
    $route['global_payment/new/(:num)/(:num)']         = 'global_payment_new/index/$1/$2';
    $route['global_payment/new/(:any)']                = 'global_payment_new/index/$1';
    $route['default_controller'] = 'frontend/index';
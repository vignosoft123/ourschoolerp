<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('dd'))
{
   function dd( $data ) {
         $pre =  "<pre>";

        echo $pre; print_r($data);die;
}

}
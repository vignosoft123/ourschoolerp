<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| FTP Server Configuration
| -------------------------------------------------------------------------
| This file contains FTP server configurations for different hosting providers
| Update the credentials according to your server configurations
|
*/

$config['ftp_servers'] = array(
    'hostgator' => array(
        'host' => '119.18.54.141',
        'port' => 21,
        'username' => 'mindw2ft_ftp', // Update with actual FTP username
        'password' => 'your_hostgator_ftp_password', // Update with actual FTP password
        'path' => '/public_html/{subdomain}' // {subdomain} will be replaced with actual subdomain
    ),
    'godaddy' => array(
        'host' => '118.139.183.79',
        'port' => 21,
        'username' => 'your_godaddy_ftp_user', // Update with actual FTP username
        'password' => 'your_godaddy_ftp_password', // Update with actual FTP password
        'path' => '/public_html/{subdomain}' // {subdomain} will be replaced with actual subdomain
    ),
    'myschools' => array(
        'host' => '119.18.54.166',
        'port' => 21,
        'username' => 'your_myschools_ftp_user', // Update with actual FTP username
        'password' => 'your_myschools_ftp_password', // Update with actual FTP password
        'path' => '/public_html/{subdomain}' // {subdomain} will be replaced with actual subdomain
    ),
    'schoolhour' => array(
        'host' => '162.241.123.136',
        'port' => 21,
        'username' => 'your_schoolhour_ftp_user', // Update with actual FTP username
        'password' => 'your_schoolhour_ftp_password', // Update with actual FTP password
        'path' => '/public_html/{subdomain}' // {subdomain} will be replaced with actual subdomain
    ),
    'collegehour' => array(
        'host' => '103.76.231.69',
        'port' => 21,
        'username' => 'your_collegehour_ftp_user', // Update with actual FTP username
        'password' => 'your_collegehour_ftp_password', // Update with actual FTP password
        'path' => '/public_html/{subdomain}' // {subdomain} will be replaced with actual subdomain
    )
);

/*
| -------------------------------------------------------------------------
| Alternative SSH Configuration (if you prefer SSH over FTP)
| -------------------------------------------------------------------------
| Uncomment and configure if you want to use SSH instead of FTP
|
*/

/*
$config['ssh_servers'] = array(
    'hostgator' => array(
        'host' => '119.18.54.141',
        'port' => 22,
        'username' => 'your_ssh_user',
        'password' => 'your_ssh_password', // Or use key-based authentication
        'path' => '/home/username/public_html/{subdomain}'
    ),
    // ... add other servers
);
*/

/* End of file ftp_config.php */
/* Location: ./mvc/config/ftp_config.php */
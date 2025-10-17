<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
                 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

// $db['default']['dsn']	   = '';
// $db['default']['hostname'] = 'localhost';
// $db['default']['username'] = 'root';
// $db['default']['password'] = '';
// $db['default']['database'] = 'school';
// $db['default']['dbdriver'] = 'mysqli';
// $db['default']['dbprefix'] = '';
// $db['default']['pconnect'] = FALSE;
// $db['default']['db_debug'] = FALSE;
// $db['default']['cache_on'] = FALSE;
// $db['default']['cachedir'] = '';
// $db['default']['char_set'] = 'utf8';
// $db['default']['dbcollat'] = 'utf8_general_ci';
// $db['default']['swap_pre'] = '';
// $db['default']['encrypt']  = FALSE;
// $db['default']['compress'] = FALSE;
// $db['default']['autoinit'] = FALSE;
// $db['default']['stricton'] = FALSE;
// $db['default']['failover'] = array();
// $db['default']['save_queries'] = TRUE;



$db['default']['dsn']	   = ''; 

// //hostgator

 $db['default']['hostname'] = '119.18.54.141'; 
$db['default']['username'] = 'mindw2ft_dummy';
$db['default']['database'] = 'mindw2ft_dummy';
$db['default']['password'] = 'DjmyAZTeNAq3'; 

//   myschools
// $db['default']['hostname'] = '119.18.54.166'; 
// $db['default']['username'] = 'myschknc_vasavidb';
// $db['default']['password'] = '8OAu8?JNoggk'; 
// $db['default']['database'] = 'myschknc_vasavidb';

//  $db['default']['hostname'] = '119.18.54.141'; 
// $db['default']['username'] = 'mindw2ft_ourschoolerp';
// $db['default']['database'] = 'mindw2ft_ourschoolerp';
// $db['default']['password'] = '7[oLxwM^gI#T'; //ourschoolerp 


//pragna
// $db['default']['hostname'] = '118.139.183.79'; 
// $db['default']['username'] = 'pragnaschool';
// $db['default']['database'] = 'pragnaschool';
// $db['default']['password'] = 'pragnaschool@123'; 


// $db['default']['username'] = 'mindw2ft_dummy';
// $db['default']['database'] = 'mindw2ft_dummy';
// $db['default']['password'] = '(ZQFQZ{,g!8?'; //dummy

    //resale server
    // $db['default']['hostname'] = '103.53.40.13'; 
    // $db['default']['username'] = 'ggsdarsi_school';
    // $db['default']['password'] = 'yVz[B;&{@c&e';
    // $db['default']['database'] = 'ggsdarsi_school';

 


// $db['default']['hostname'] = 'localhost'; 
// $db['default']['username'] = 'root';
// $db['default']['password'] = ''; 
// $db['default']['database'] = 'ourscupz_school'; //ggs


$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = FALSE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['encrypt']  = FALSE;
$db['default']['compress'] = FALSE;
$db['default']['autoinit'] = FALSE;
$db['default']['stricton'] = FALSE;
$db['default']['failover'] = array();
$db['default']['save_queries'] = TRUE;

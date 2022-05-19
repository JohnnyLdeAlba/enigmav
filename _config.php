<?php

$NetworkName = 'EnigmaV Network';
$NetworkUrl = 'website.com';
$NetworkDomain = 'website.com';

$MySqlPrefix = 'enigmav_';
$MySqlHost = 'localhost';
$MySqlUsername = 'username';
$MySqlPassword = 'password';
$MySqlDatabase = 'database';

$TemplateTheme = 'default/';

date_default_timezone_set('America/Los_Angeles');

if (get_magic_quotes_gpc() == 1) {

  $_POST = array_map( 'stripslashes', $_POST );
  $_GET = array_map( 'stripslashes', $_GET );
  $_COOKIE = array_map( 'stripslashes', $_COOKIE );

}

?>

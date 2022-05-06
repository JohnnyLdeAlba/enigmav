<?php

include('config.php');
include('lib/libmysql.php');
include('lib/libuser.php');
include('lib/libsession.php');
include('lib/libinput.php');
include('lib/libtemplate.php');

$sql = new LibMySql();
$usr = new LibUser();
$ses = new LibSession();
$in = new LibInput();

$sql->Create();
$usr->Create();
$ses->Create();

include('bin/mrecoverpassword.php');

?>

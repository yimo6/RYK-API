<?php
include 'config.php';
include 'RYK-API.php';
$RYKAPI=new RYKAPI( array( 'token'=>server_key , 'auth_mod'=>server_auth ) );
if(!isset($_GET['token'])) $RYKAPI->API(1002,'The param is empty','');
$token = $_GET['token'];
$RYKAPI->run($token);
?>
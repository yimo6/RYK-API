<?php
include 'config.php';
$sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
$ip="127.0.0.1";
socket_connect($sock,$ip,socket_port) or die("{$ip}:".socket_port."服务器拒绝连接");
socket_write($sock,json_encode(['token' => md5(socket_key)]));
$string = socket_read($sock,8192);
echo $string.PHP_EOL;
?>
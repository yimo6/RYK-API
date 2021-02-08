<?php
ignore_user_abort();
set_time_limit(0);
include 'config.php';
include 'RYK-API.php';
$RYKAPI = new RYKAPI(['token'=>server_key,'auth'=>server_auth]);
$sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
socket_bind($sock,socket_ip,socket_port)  or die("socket_bind: Error");
socket_listen($sock) or die("socket_listen: Error");
while(true){
	if(($accept = socket_accept($sock)) != false){
		socket_getpeername($accept,$addr,$port); //Get the user ip
		echo "[Server]: {$addr} are connected".PHP_EOL;
		$read = socket_read($accept,8192);
		echo "[Server]: {$addr} send the data -> ".$read.PHP_EOL;
		if(!is_array(json_decode($read,true))){
			socket_write($accept,$RYKAPI->SAPI(10001,'Error,it is not json',''));
			socket_close($accept);
			echo "[Server]: {$addr}'s data is not json -> close()".PHP_EOL;
			continue;
		}else{
			$index = json_decode($read,true);
			if(!isset($index['token'])){
				socket_write($accept,$RYKAPI->SAPI(10002,'token is empty',''));
				socket_close($accept);
				echo "[Server]: {$addr} not send the Token -> close()".PHP_EOL;
				continue;
			}
			$key = $index['token'];
			if(!$RYKAPI->auth($key)){ 
				socket_write($accept,$RYKAPI->SAPI(10003,'fail',''));
				socket_close($accept);
				echo "[Server]: {$addr}'s token({$key}) is not true -> close()".PHP_EOL;
				continue;
			}
			socket_write($accept,$RYKAPI->SAPI(200,'success',$RYKAPI->info()));
			echo "[Server]: {$addr} -> YAPI()".PHP_EOL;
			unset($index,$key);
		}
		socket_close($accept);
		echo "[Server]: {$addr} -> close()".PHP_EOL;
		unset($accept,$read,$addr);
	}
}
socket_close($sock);
?>
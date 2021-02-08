<?php

/**
 * RYKAPI-Core
 * @author     Yimo(QQ413255675)
 */
class RYKAPI extends core{

	private $token;
	private $root;

	public function __construct(array $config=['token' => '123456']){
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') exit("Don't use Windows");
		$this->root = dirname(__FILE__);
		$this->token = $config['token'];
		!isset($config['auth_mod']) ? $this->auth_mod = 'md5' : $this->auth_mod = $config['auth_mod'];
	}


	/**
	 * run
	 * @access public
	 * @param string $key
	 * @return true
	 */ 
	public function run($key){
		if(!$this->auth($key)) $this->API(1001,'Fail','');
		$this->API(200,'Success',$this->info());
		return true;
	}

	/**
	 * auth
	 * @access public
	 * @param string $key
	 * @return bool
	 */ 
	public function auth($key){
		if(!function_exists($this->auth_mod)) $this->error(501,'Unkown method -> ('.$this->auth_mod.')','auth');
		$callback = call_user_func("{$this->auth_mod}",$this->token);
		return ($callback == $key);
	}

	/**
	 * API
	 * @access public
	 * @param int    $code
	 * @param string $msg
	 * @param mixed  $data
	 * @return none
	 */ 
	public function API($code,$msg,$data){
		exit(json_encode(array('code'=>$code,'msg'=>$msg,'data'=>$data)));
	}
	
	/**
	 * SAPI
	 * @access public
	 * @param int    $code
	 * @param string $msg
	 * @param mixed  $data
	 * @return json
	 */ 
	public function SAPI($code,$msg,$data){
		return json_encode(array('code'=>$code,'msg'=>$msg,'data'=>$data));
	}

	/**
	 * error
	 * @access private
	 * @param int    $code
	 * @param string $msg
	 * @param string $func (name)
	 * @return none
	 */ 
	private function error($code=500,$msg='',$func='unkown'){
		exit("Error!<br/>Code: {$code}<br/>Msg: {$msg}<br/>Error function: {$func}");
	}

}


/**
 * Core
 */
class core{


	/**
	 * info
	 * Get the info
	 * @access public
	 * @return none
	 */ 
	public function info(){
		$result=array();
		$res=array();
		$infos = $this->shell('cat /proc/cpuinfo');
		$info = explode("\n",$infos);
		$info_number = count($info);
		for($i=0;$i<$info_number;$i++){
			$arr = explode(":",$info[$i]);
			$id = trim($arr[0]);
			if(isset($arr[1])) $res[$id]=trim($arr[1]);
		}
		$mem_info = $this->shell('free -m | grep Mem');
		$temp = explode(" ",preg_replace("/\s(?=\s)/","\\1",$mem_info));
		
		$uses = $this->uses();
		$result = array(
			'cpu_name' => $res['model name'],
			'cpu_num'  => trim($this->shell('cat /proc/stat | grep cpu[0-9] -c')),
			'cpu_used' => $uses['cpu'],
			'mem'      => ['total'=>$temp[1],'used'=>$temp[2],'free'=>$temp[3]],
			'network'  => $uses['network']
		);
		unset($arr,$info,$info_number,$info,$infos,$res,$temp);
		return $result;
	}

	/**
	 * shell
	 * Run the shell
	 * @access private
	 * @param  string $cmd
	 * @return mixed
	 */ 
	private function shell($cmd){
		if(is_array($cmd)){
			$cmds = "";
			foreach($cmd as $value){
			$cmds .= $value."\n";
			}
			return shell_exec($cmds);
		}else{
		return shell_exec($cmd);
		}
	}
	
	
	/**
	 * uses
	 * Get info (CPU/memory) from the system
	 * @access private
	 * @param  string $cmd
	 * @return mixed
	 */ 
	private function uses(){
		$net_start = preg_replace("/\s(?=\s)/","\\1",$this->shell('more /proc/net/dev | grep eth0 | cut -d \':\' -f 2'));
		$nets1 = explode(' ',$net_start);
		$cpu_str = $this->shell('more /proc/stat');
		$pattern = "/(cpu[0-9]?)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/";
		preg_match_all($pattern, $cpu_str, $out);
		$n = 0;
		$cpu_s1 = $out[2][$n]+$out[3][$n]+$out[4][$n]+$out[5][$n]+$out[6][$n]+$out[7][$n]+$out[8][$n]+$out[9][$n]+$out[10][$n]+$out[11][$n];
		$cpu_t1 = $out[5][$n];
		sleep(1);
		$cpu_str = $this->shell('more /proc/stat');
		$pattern = "/(cpu[0-9]?)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/";
		preg_match_all($pattern, $cpu_str, $out);
		$n = 0;
		$cpu_s2 = $out[2][$n]+$out[3][$n]+$out[4][$n]+$out[5][$n]+$out[6][$n]+$out[7][$n]+$out[8][$n]+$out[9][$n]+$out[10][$n]+$out[11][$n];
		$cpu_t2 = $out[5][$n];
		$cpu_t=$cpu_t2-$cpu_t1;
		$cpu_s=$cpu_s2-$cpu_s1;
		$net_start = preg_replace("/\s(?=\s)/","\\1",$this->shell('more /proc/net/dev | grep eth0 | cut -d \':\' -f 2'));
		$nets2 = explode(' ',$net_start);
		$get = (int)$nets2[1]-(int)$nets1[1];
		$post = (int)$nets2[9]-(int)$nets1[9];
		$cpu_use = round(100*($cpu_s-$cpu_t)/$cpu_s);
		$network_use = ['upload'=>$post,'download'=>$get];
		return ['cpu'=>$cpu_use,'network'=>$network_use];
	}

}
?>
<?php

class Middleware
{
	var $jwt_key = "Qwertyuiop;lkjhgfdsazxcvbnm,loSAHJDjhbygvtfcrdxeszwaqwsdfghj";
	public function __construct()
	{
	
		$this->load->library('JWT');
	}

	public function __get($key)
	{
		return get_instance()->$key;
	}


	public function check_user($token)
	{
		if (!$token)
			die(json_encode(array("data" => 0, "error" => 100, "msg" => 'INVALID_TOKEN')));
		try {
			$load = $this->jwt->decode($token, $this->jwt_key);
		} catch (Exception $e) {
			die(json_encode(array("data" => 0, "error" => 100, "msg" => 'INVALID_TOKEN')));
		}
		if (!$load || !isset($load->role))
			die(json_encode(array("data" => 0, "error" => 100, "msg" => 'INVALID_TOKEN')));

		return $load;
	}

	public function check_admin_and_super_admin($token)
	{
		if (!$token)
			die(json_encode(array("data" => 0, "error" => 100, "msg" => 'INVALID_TOKEN')));
		try {
			$load = $this->jwt->decode($token, $this->jwt_key);
		} catch (Exception $e) {
			die(json_encode(array("data" => 0, "error" => 100, "msg" => 'INVALID_TOKEN')));
		}
		if (!$load || !isset($load->role) || ($load->role != 2 && $load->role != 3))
			die(json_encode(array("data" => 0, "error" => 101, "msg" => 'invalid_auth')));

		return $load;
	}

	public function check_super_admin($token)
	{
		if (!$token)
			die(json_encode(array("data" => 0, "error" => 100, "msg" => 'INVALID_TOKEN')));
		try {
			$load = $this->jwt->decode($token, $this->jwt_key);
		} catch (Exception $e) {
			die(json_encode(array("data" => 0, "error" => 100, "msg" => 'INVALID_TOKEN')));
		}
		if (!$load || !isset($load->role) || ($load->role != 3))
			die(json_encode(array("data" => 0, "error" => 101, "msg" => 'invalid_auth')));

		return $load;
	}
}

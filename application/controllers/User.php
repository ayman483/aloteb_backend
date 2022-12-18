<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
	var $jwt_key = "Qwertyuiop;lkjhgfdsazxcvbnm,loSAHJDjhbygvtfcrdxeszwaqwsdfghj";
	public function __construct()
	{
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct(
			header('Content-Type: application/json')
		);
		$this->load->library('JWT');
		$this->load->library('Middleware');
		$this->load->library('Log_');
	}

	public function login()
	{
		$email = $this->input->post("email");
		$pwd = $this->input->post("pwd");

		if (!isset($email) || !isset($pwd)) {
			$res = array("data" => 0, "error" => 1, "msg" => 'Please provaid us with email and pwd');
			die(json_encode($res));
		}
		$_pwd = md5($pwd);
		$user = $this->db->query("SELECT * FROM user where email = '$email' and pwd = '$_pwd'")->row();
		if (isset($user) & isset($user->id)) {
			$user->token = $this->jwt->encode($user, $this->jwt_key);
		}
		$this->log_->add_activity($user->id,'login','login',0);
		die(json_encode($user));
	}

	public function update_users_status()
	{
		$user = $this->middleware->check_admin_and_super_admin($this->input->post('token'));
		$id = $this->input->post("id");
		$status = $this->input->post("status");

		if (!isset($id) || !isset($status)) {
			$res = array("data" => 0, "error" => 1, "msg" => 'Please provaid us with id and status');
			die(json_encode($res));
		}

		$where = array('id' => $id);
		$data = array('status' => $status);
		$this->db->update("user", $data, $where);
		$this->log_->add_activity($user->id,'update_itme','users',$id);
		die(json_encode('Update user succssfull'));
	}

	public function remove_user($id)
	{
		$user = $this->middleware->check_admin_and_super_admin($this->input->post('token'));
		$query = $this->db->query('Select * from user')->result_array;
		$this->log_->add_activity($user->id,'delete','users',$id);
		die(json_encode('Remove user succssfull'));
	}

	public function get_all()
	{
		$user = $this->middleware->check_admin_and_super_admin($this->input->post('token'));
		if ($user->role == 2) {
			$query = $this->db->query('Select * from user where role != 3')->result_array();
		} else {
			$query = $this->db->query('Select * from user')->result_array();
		}
		$this->log_->add_activity($user->id,'view_list','users',0);
		die(json_encode($this->json_numeric($query)));
	}

	public function add_user()
	{
		$user = $this->middleware->check_admin_and_super_admin($this->input->post('token'));
		$first_name = $this->input->post("first_name");
		$last_name = $this->input->post("last_name");
		$email = $this->input->post("email");
		$role = $this->input->post("role");
		$pwd = $this->input->post("password");
		$id = $this->input->post("id");

		if (!isset($first_name) || !isset($last_name) || !isset($email) || !isset($role) || !isset($pwd)) {
			$res = array("data" => 0, "error" => 1, "msg" => 'Please provaid us with first_name and last_name and email and role and pwd');
			die(json_encode($res));
		}

		$data = array(
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $email,
			'role' => $role,
			'pwd' => md5($pwd)
		);
		if ($id) {
			$where = array('id' => $id);
			$this->db->update("user", $data, $where);
			$this->log_->add_activity($user->id,'update_itme','users',$id);
		} else {
			$user_id = $this->db->insert("user", $data);
			$this->log_->add_activity($user->id,'add_item','users',$this->db->insert_id());
		}
		die(json_encode('added user succssfull'));
	}

	private function json_numeric($array)
	{
		if (is_array($array) || is_object($array)) {
			foreach ($array as &$prop) {
				if (is_numeric($prop)) {
					if ($prop . '' === doubleval($prop) . '')
						$prop = doubleval($prop);
					if ($prop . '' === intval($prop) . '')
						$prop = intval($prop);
				}
				if (is_object($prop) || is_array($prop)) {
					$prop = $this->json_numeric($prop);
				}
			}
		}
		return $array;
	}
}

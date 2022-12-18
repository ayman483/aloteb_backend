<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logs extends CI_Controller
{
	public function __construct()
	{
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct(
			header('Content-Type: application/json')
		);
		$this->load->library('Middleware');
	}
	public function get_all()
	{
		$this->middleware->check_super_admin($this->input->post('token'));
		$query = "SELECT logs.*, user.first_name AS user,orders.title,user.first_name  as target_user
		FROM logs
		INNER JOIN user ON user.id = logs.user_id
		left JOIN orders ON orders.id = logs.item_id";

		$query = $this->db->query($query)->result_array();
		foreach ($query as $key => $item) {
			if($item['entity'] == 'users' && $item['service']  !='view_list'){
				$user_id = $item['item_id'];
				$user = $this->db->query("SELECT * FROM user where id = $user_id")->row();
				if (isset($user) & isset($user->id)) {
					$item['target_user'] = $user ;
				}
			}
		}
		die(json_encode($this->json_numeric($query)));
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

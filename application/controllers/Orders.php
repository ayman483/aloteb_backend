<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends CI_Controller
{
	var $jwt_key = "Qwertyuiop;lkjhgfdsazxcvbnm,loSAHJDjhbygvtfcrdxeszwaqwsdfghj";
	public function __construct()
	{
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct(header('Content-Type: application/json'));
		$this->load->library('Middleware');
		$this->load->library('Log_');
	}

	public function update_order_status()
	{
		$user = $this->middleware->check_user($this->input->post('token'));
		$id = $this->input->post("id");
		$status = $this->input->post("status");

		if (!isset($id) || !isset($status)) {
			$res = array("data" => 0, "error" => 1, "msg" => 'Please provaid us with id and status');
			die(json_encode($res));
		}

		$where = array('id' => $id);
		$data = array('status' => $status);
		$this->db->update("orders", $data, $where);
		$this->log_->add_activity($user->id,'update_itme','orders',$id);
		die(json_encode('Update order succssfull'));
	}

	public function remove_order($id)
	{
		$user = $this->middleware->check_user($this->input->post('token'));
		$this->db->query("DELETE FROM orders WHERE id = $id");
		$this->log_->add_activity($user->id,'delete','orders',$id);
		die(json_encode('Remove order succssfull'));
	}

	public function add_order()
	{
		$user = $this->middleware->check_user($this->input->post('token'));
		$title = $this->input->post("title");
		$description = $this->input->post("description");
		$df_file = $this->input->post("pdf_file");
		$status = $this->input->post("status");
		$id = $this->input->post("id");

		if (!isset($title) || !isset($description) || !isset($status)) {
			$res = array("data" => 0, "error" => 1, "msg" => 'Please provaid us with title and description and status');
			die(json_encode($res));
		}

		$data = array(
			'title' => $title,
			'description' => $description,
			'df_file' => $df_file,
			'status' => $status,
		);
		if ($id) {
			$where = array('id' => $id);
			$this->db->update("orders", $data, $where);
			$this->log_->add_activity($user->id,'update_itme','orders',$id);
		} else {
			$this->db->insert("orders", $data);
			$this->log_->add_activity($user->id,'add_item','orders',$this->db->insert_id());
		}
		die(json_encode('added order succssfull'));
	}
	public function get_all()
	{
		$user = $this->middleware->check_user($this->input->post('token'));
			$query = $this->db->query('Select * from orders')->result_array();
		$this->log_->add_activity($user->id,'view_list','orders',0);
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
	public function update_status()
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
		$this->db->update("orders", $data, $where);
		$this->log_->add_activity($user->id,'update_itme','orders',$id);
		die(json_encode('Update order succssfull'));
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_m extends CI_Model {
	public function __construct() {
		parent::__construct();
		// Your own constructor code
	}

	public function get_all() {
		return $this -> db -> get("configs") -> result();
	}

	public function set_config($configs) {
		foreach ($configs as $config => $value) {
			$this -> db -> update('configs', array("value" => $value), array('name' => $config));
		}
	}

}

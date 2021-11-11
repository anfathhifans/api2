<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_Model extends CI_Model {

	public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->__settings();       
    }

    public function __settings(){
        // [sql_mode] privilege set [NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION] is works for all
        $this->db->simple_query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");        
    }

	public function get_product(){
		$query = $this->db->get('products');
		return $query->result();
	}	

	public function insert_product($data){
		return $this->db->insert('products', $data);
	}

	public function edit_product($data, $id){
		$this->db->where('id', $id);
		$query = $this->db->get('products');
        return $query->row();
	}

	public function update_product($data, $id){
		$this->db->where('id', $id);
        return $this->db->update('products', $data);
	}

	public function delete_product($id){
		return $this->db->delete('products', ['id' => $id]);
	}

}
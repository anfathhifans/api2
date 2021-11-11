<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('database_model', 'dbconnect');
    }

    public function index(){
    	$data = [];
    	$this->load->view('start-page', $data);
    }

}
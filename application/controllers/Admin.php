<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        redirect(site_url('admin/dashboard'));
        exit();
    }
    
    # LOGIN & LOGOUT ===========================================================
    
    public function login(){
        $page = 'login';
        $page_title = 'Log in';
        $response = $this->session->flashdata('response');
        
        // clear old session value
        $this->session->unset_userdata('logged_admin');
        
        if($this->input->post()){
            $this->form_validation->set_error_delimiters('<span class="error">', '</span>');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            if ($this->form_validation->run() === TRUE){                
                $username = $this->input->post('username');
                $password = md5(md5(md5($this->input->post('password'))));
                $checkQuery = $this->dbconnect->getWhere('admin', compact('username', 'password'), true);
                if($checkQuery){
                    
                    $this->session->set_userdata('logged_admin', arrayToObject(['id' => $checkQuery->id, 'username' => $checkQuery->username]));        
                    $this->session->set_flashdata('response', array('type' => 'normal', 'error_type' => 'success', 'message' => 'Welcome Administrator!'));                            
                    if(!empty($this->input->get('redirect_url'))){                                
                        redirect(urldecode(base64_decode($this->input->get('redirect_url'))));
                    }else{
                        redirect(base_url('admin/dashboard'));     
                    }                   
                    
                }else{ $response = array('type' => 'normal', 'error_type' => 'error', 'message' => 'Your Username or Password is incorrect.' ); }
            }else{ $response = array('type' => 'normal', 'error_type' => 'error', 'message' => implode('<br/>', $this->form_validation->error_array())); }  
        }
        
        if($this->input->get('logout') == true){
            $response = array('type' => 'normal', 'error_type' => 'success', 'message' => 'You have been successfully logged out!');
        }
        
        $data = compact('page','page_title','response');
        
        $this->load->view('admin/include/header', $data);
        $this->load->view('admin/login', $data);
        $this->load->view('admin/include/footer', $data);
    }
    
    public function logout(){
        $this->session->unset_userdata('logged_admin');
        redirect(base_url('admin/login/?logout=true'));
        exit();
    }
    
    # DASHBOARD ================================================================
    
    public function dashboard(){
        $this->__is_logged_user();
    }

    private function __is_logged_user(){
        $userdata = $this->session->userdata('logged_admin');
        if(!$userdata){
            redirect(base_url('admin/login'));
        }
    }
   
}

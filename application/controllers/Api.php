<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class Api extends RestController {

	public function __construct(){
        parent::__construct();
        $this->load->model('product_model', 'product');
    }

	public function indexProduct_get(){
        $products = $this->product->get_product();
        $this->response($products, 200);
	}

	public function storeProduct_post(){
        $data = [
            'name' =>  $this->input->post('name'),
            'price' => $this->input->post('price'),
            'available' => $this->input->post('available')
        ];
        $result = $this->product->insert_product($data);
        if($result > 0)
        {
            $this->response([
                'status' => true,
                'message' => 'NEW PRODUCT CREATED'
            ], RestController::HTTP_OK); 
        }
        else
        {
            $this->response([
                'status' => false,
                'message' => 'FAILED TO CREATE NEW PRODUCT'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function editProduct_get($id){
        $students = $this->product->edit_products($id);
        $this->response($students, 200);
    }

    public function updateProduct_put($id){
        $data = [
            'name' =>  $this->put('name'),
            'class' => $this->put('class'),
            'email' => $this->put('email')
        ];
        $result = $this->product->update_product($id, $data);
        if($result > 0)
        {
            $this->response([
                'status' => true,
                'message' => 'PRODUCT UPDATED'
            ], RestController::HTTP_OK); 
        }
        else
        {
            $this->response([
                'status' => false,
                'message' => 'FAILED TO UPDATE PRODUCT'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function deleteProduct_delete($id)
    {
        $result = $this->product->delete_product($id);
        if($result > 0)
        {
            $this->response([
                'status' => true,
                'message' => 'PRODUCT DELETED'
            ], RestController::HTTP_OK); 
        }
        else
        {
            $this->response([
                'status' => false,
                'message' => 'FAILED TO DELETE PRODUCT'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

}

// refere : https://www.fundaofwebit.com/post/codeigniter-3-restful-api-tutorial-using-postman
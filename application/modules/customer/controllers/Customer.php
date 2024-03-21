<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        verify_session('customer');

        $this->load->model(array(
            'payment_model' => 'payment',
            'order_model' => 'order',
            'review_model' => 'review'
        ));
    }

    public function index()
    {
        $params['title'] = get_settings('store_tagline');

        $home['total_process_order'] = $this->order->count_process_order();
        $home['total_unpaid_order'] = $this->order->count_unpaid_order();
        $home['total_delivery_order'] = $this->order->count_delivery_order();
        $home['total_success_order'] = $this->order->count_success_order();

        $home['flash'] = $this->session->flashdata('store_flash');

        $this->load->view('header', $params);
        $this->load->view('home', $home);
        $this->load->view('footer');
    }
}
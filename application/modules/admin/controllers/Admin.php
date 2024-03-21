<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        verify_session('admin');

        $this->load->model(array(
            'product_model' => 'product',
            'customer_model' => 'customer',
            'order_model' => 'order',
            'payment_model' => 'payment'
        ));
    }

    public function index()
    {
        $params['title'] = 'Admin '. get_store_name();
        //AND DATE(order_date) = '".$day."'
        $overview['total_products'] = $this->product->count_all_products();
        $overview['total_customers'] = $this->customer->count_all_customers();
        $overview['total_order'] = $this->order->count_all_orders();
        $overview['total_income'] = $this->payment->sum_success_payment();
        $day = date("Y-m-d");
        $month = date("Y-m");
        $total_income_raw = $order = $this->db->query("
            SELECT * FROM orders WHERE ORDER_STATUS = 4
        ")->result();
        $total_income_today = 0;
        $total_income_month = 0;
        foreach($total_income_raw as $k => $row){
            $order_date = explode("-", $row->order_date);
            $order_date_day = explode(" ", $order_date[2]);
            if($order_date[0]."-".$order_date[1] == $month){
                $total_income_month += (int) $row->total_price;
            }
            if($order_date[0]."-".$order_date[1]."-".$order_date_day[0] == $day){
                $total_income_today += (int) $row->total_price;
            }
        }
        $overview['total_income_today'] = $total_income_today;
        $overview['total_income_month'] = $total_income_month;
        // var_dump($total_income_today);
        // exit;

        $overview['products'] = $this->product->latest();
        $overview['categories'] = $this->product->latest_categories();
        $overview['payments'] = $this->payment->payment_overview();
        $overview['orders'] = $this->order->latest_orders();
        $overview['customers'] = $this->customer->latest_customers();

        $overview['order_overviews'] = $this->order->order_overview();
        $overview['income_overviews'] = $this->order->income_overview();

        $this->load->view('header', $params);
        $this->load->view('overview', $overview);
        $this->load->view('footer');
    }
}